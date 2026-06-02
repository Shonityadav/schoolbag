<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Distinct filter options from the ebooks table
        $publications = Ebook::whereNotNull('publication')
            ->where('publication', '!=', '')
            ->distinct()
            ->orderBy('publication')
            ->pluck('publication');

        $standards = Ebook::whereNotNull('standard')
            ->where('standard', '!=', '')
            ->distinct()
            ->orderByRaw('CAST(standard AS UNSIGNED)')   // numeric sort: 1,2,3... not 1,10,2
            ->pluck('standard');

        $subjects = Ebook::whereNotNull('subject')
            ->where('subject', '!=', '')
            ->distinct()
            ->orderBy('subject')
            ->pluck('subject');

        // Active filter values from URL
        $activePub = $request->get('publisher', '');
        $activeCls = $request->get('class', '');      // maps to `standard` column
        $activeSub = $request->get('subject', '');

        // Build filtered ebook query
        $ebooks = Ebook::query()
            ->when($activePub, fn($q) => $q->where('publication', $activePub))
            ->when($activeCls, fn($q) => $q->where('standard',    $activeCls))
            ->when($activeSub, fn($q) => $q->where('subject',     $activeSub))
            ->orderBy('standard')
            ->orderBy('name')
            ->get();

        $user = \Illuminate\Support\Facades\Auth::user();
        $assignedEbookIds = \App\Models\Course::where('user_id', $user->id)
            ->whereNotNull('ebook_id')
            ->pluck('ebook_id')
            ->toArray();

        return view('student.worksheets.index', compact(
            'publications', 'standards', 'subjects', 'ebooks', 'assignedEbookIds'
        ));
    }

    public function show(int $id)
    {
        $ebook = Ebook::with(['pages' => fn($q) => $q->whereNull('deleted_at')])->findOrFail($id);

        return view('student.worksheets.show', compact('ebook'));
    }

    public function toc(int $id)
    {
        $ebook = Ebook::with('pages')->findOrFail($id);

        // 1. Check DB Cache
        $cachedChapters = \App\Models\EbookChapter::with('stages')->where('ebook_id', $id)->orderBy('chapter_number')->get();
        if ($cachedChapters->isNotEmpty()) {
            return response()->json(['chapters' => $cachedChapters]);
        }

        // 2. Fetch Index Pages (Pages 2-5, assuming cover is pos 1)
        $indexPages = $ebook->pages->sortBy('position')->where('position', '>', 1)->take(4);
        
        $inlineData = [];
        foreach ($indexPages as $page) {
            $path = public_path($page->url . '/' . $page->title);
            if (file_exists($path)) {
                $imageData = base64_encode(file_get_contents($path));
                // Infer mime type
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $mimeType = $extension == 'png' ? 'image/png' : 'image/jpeg';
                
                $inlineData[] = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => $imageData
                    ]
                ];
            }
        }

        if (empty($inlineData)) {
            return response()->json(['error' => 'No index pages found.'], 404);
        }

        // 3. Call Gemini API
        $prompt = "You are an expert document structure analyzer.\nThese images are pages from a book's Table of Contents.\n\nTASK:\n1. Read ALL entries in order.\n2. Extract every chapter / lesson / unit.\n3. Use the listed page number as start_page.\n4. Identify the printed page number(s) on the Table of Contents pages themselves.\n\nOUTPUT RULES:\n- Return ONLY valid JSON\n- Return a JSON OBJECT with two keys: 'index_pages' (string, e.g., '3,4' or 'iii,iv') and 'chapters' (list of objects).\n- Each object in 'chapters' MUST have:\n  {\"title\": string, \"start_page\": number, \"section\": string | null}\n- 'start_page' MUST be an integer\n- Do NOT include explanations\n\nExample:\n{\n  \"index_pages\": \"3,4\",\n  \"chapters\": [\n    {\"title\": \"Chapter 1\", \"start_page\": 5, \"section\": \"Part A\"}\n  ]\n}";

        $apiKey = env('GEMINI_API_KEY');
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
            'contents' => [
                [
                    'parts' => array_merge(
                        [['text' => $prompt]],
                        $inlineData
                    )
                ]
            ]
        ]);

        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . $response->body());
            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
        }

        $responseData = $response->json();
        $textResult = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Clean JSON markdown blocks if any
        $textResult = trim($textResult);
        if (str_starts_with($textResult, '```json')) {
            $textResult = substr($textResult, 7);
            if (str_ends_with($textResult, '```')) {
                $textResult = substr($textResult, 0, -3);
            }
        } elseif (str_starts_with($textResult, '```')) {
            $textResult = substr($textResult, 3);
            if (str_ends_with($textResult, '```')) {
                $textResult = substr($textResult, 0, -3);
            }
        }
        $textResult = trim($textResult);
        
        $parsedData = json_decode($textResult, true);
        if (!is_array($parsedData) || !isset($parsedData['chapters'])) {
            return response()->json(['error' => 'Invalid AI response format.'], 500);
        }

        $chaptersData = $parsedData['chapters'];
        $extractedIndexPages = $parsedData['index_pages'] ?? '';

        // Save index pages to ebook table
        $ebook->update(['index_page' => $extractedIndexPages]);

        // 4. Save to DB
        $savedChapters = [];
        $chapterNumber = 1;
        $totalExtracted = count($chaptersData);
        
        foreach ($chaptersData as $index => $data) {
            $startPage = (int) ($data['start_page'] ?? 0);
            $endPage = null;
            if ($index < $totalExtracted - 1 && isset($chaptersData[$index + 1]['start_page'])) {
                $endPage = (int) $chaptersData[$index + 1]['start_page'] - 1;
            }

            $chapter = \App\Models\EbookChapter::create([
                'ebook_id' => $id,
                'chapter_number' => $chapterNumber,
                'chapter_name' => $data['title'] ?? 'Untitled Chapter',
                'start_page' => $startPage,
                'end_page' => $endPage,
                'total_stages' => 4
            ]);

            // Create stages
            $stageNames = ['Reading Mission', 'Hard Words', 'Activity Mission', 'Exercise Mission'];
            $descriptions = [
                'Explore the pages of the ebook.',
                'Master the difficult words found in this chapter.',
                'Play fun activities to test your understanding.',
                'Complete exercises to test your knowledge.'
            ];
            
            foreach ($stageNames as $sIndex => $sName) {
                \App\Models\EbookChapterStage::create([
                    'ebook_id' => $id,
                    'ebook_chapter_id' => $chapter->id,
                    'stage_number' => $sIndex + 1,
                    'stage_name' => $sName,
                    'description' => $descriptions[$sIndex]
                ]);
            }
            
            $chapter->load('stages');
            $savedChapters[] = $chapter;
            $chapterNumber++;
        }

        return response()->json(['chapters' => $savedChapters]);
    }

    public function assign(int $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $ebook = Ebook::findOrFail($id);

        // Check if already assigned
        $existing = \App\Models\Course::where('user_id', $user->id)
            ->where('ebook_id', $ebook->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.courses.index')
                ->with('success', 'Ebook is already assigned to your subjects.');
        }

        // Get max order
        $maxOrder = \App\Models\Course::where('class_id', $user->class_id)->max('order') ?? 0;

        // Create the Course (Subject)
        $course = \App\Models\Course::create([
            'user_id' => $user->id,
            'ebook_id' => $ebook->id,
            'class_id' => null, // Not a global class subject
            'title' => $ebook->name,
            'description' => $ebook->subject . ' - ' . $ebook->publication,
            'icon' => '📘',
            'color' => '#1A6BAA',
            'order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        // Fetch Ebook Chapters
        $ebookChapters = \App\Models\EbookChapter::where('ebook_id', $ebook->id)
            ->orderBy('chapter_number')
            ->get();

        if ($ebookChapters->count() > 0) {
            foreach ($ebookChapters as $index => $ebChapter) {
                // Create Course Chapter
                $chapter = \App\Models\Chapter::create([
                    'course_id' => $course->id,
                    'title' => $ebChapter->chapter_name ?? 'Chapter ' . ($index + 1),
                    'description' => 'Pages ' . $ebChapter->start_page . ' to ' . $ebChapter->end_page,
                    'order' => $index,
                    'unlock_threshold' => 0,
                    'xp_reward' => 50,
                    'is_active' => true,
                ]);

                // Create 4 Lessons (Stages 1-4)
                $stageNames = ['Reading Mission', 'Hard Words', 'Activity Mission', 'Exercise Mission'];
                foreach ($stageNames as $lessonIndex => $name) {
                    \App\Models\Lesson::create([
                        'chapter_id' => $chapter->id,
                        'title' => $name,
                        'type' => 'reading',
                        'content' => 'Explore the pages of the ebook.',
                        'order' => $lessonIndex, // 0 to 3
                        'duration_minutes' => 10,
                        'xp_reward' => 20,
                        'is_active' => true,
                    ]);
                }

            }
        } else {
            // Create 1 Fallback Chapter
            $chapter = \App\Models\Chapter::create([
                'course_id' => $course->id,
                'title' => 'Read ' . $ebook->name,
                'description' => 'Complete all stages to finish this book.',
                'order' => 0,
                'unlock_threshold' => 0,
                'xp_reward' => 50,
                'is_active' => true,
            ]);

            // Create 4 Lessons (Stages 1-4)
            $stageNames = ['Reading Mission', 'Hard Words', 'Activity Mission', 'Exercise Mission'];
            foreach ($stageNames as $index => $name) {
                \App\Models\Lesson::create([
                    'chapter_id' => $chapter->id,
                    'title' => $name,
                    'type' => 'reading',
                    'content' => 'Explore the pages of the ebook.',
                    'order' => $index, // 0 to 3
                    'duration_minutes' => 10,
                    'xp_reward' => 20,
                    'is_active' => true,
                ]);
            }

        }

        return redirect()->route('student.courses.index')
            ->with('success', 'Ebook assigned successfully! You can now view it here.');
    }
}
