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
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Get assigned courses for this student's class or user ID
        $assignedCourses = \App\Models\AssignedEbook::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $ebookIds = [];
        $externalEbooks = collect();
        foreach ($assignedCourses as $course) {
            if (!empty($course->ebook_url)) {
                $hasChapters = $course->chapters()->count() > 0;
                $externalEbooks->push((object)[
                    'id' => $course->ebook_id,
                    'course_id' => $course->id,
                    'has_chapters' => $hasChapters,
                    'name' => $course->title ?: ("Ebook - " . strtoupper($course->ebook_id)),
                    'publication' => $course->publication ?: 'MyEbook',
                    'subject' => $course->subject ?: '',
                    'standard' => $course->standard ?: '',
                    'series' => $course->series ?: '',
                    'external_url' => $course->ebook_url,
                ]);
            } else {
                $resolvedId = $course->getResolvedEbookId();
                if ($resolvedId && is_numeric($resolvedId)) {
                    $ebookIds[] = $resolvedId;
                }
            }
        }

        // Only fetch the ebooks that are unlocked/assigned to this student
        $internalEbooks = Ebook::whereIn('id', array_unique($ebookIds))
            ->orderBy('standard')
            ->orderBy('name')
            ->get();

        $ebooks = $externalEbooks->merge($internalEbooks);
        $assignedEbookIds = $internalEbooks->pluck('id')->toArray();

        return view('student.worksheets.index', compact('ebooks', 'assignedEbookIds'));
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

    public function scanQr(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = trim($request->input('code'));
        $user = \Illuminate\Support\Facades\Auth::user();

        // Step 1: Check if the scanned text is a URL (using the initial scanned URL directly without redirect tracing)
        $actualUrl = $code;
        $isUrl = (bool) preg_match('/^https?:\/\//i', $code);

        // Save URL in assigned_ebooks ONLY if the URL contains 'myebook'
        $isMyEbook = (stripos($code, 'myebook') !== false);
        if ($isMyEbook) {
            $apiUrl = rtrim($code, '/') . '/schoolbag';
            
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->get($apiUrl);
                $apiData = $response->json();
                
                if ($response->successful() && isset($apiData['success']) && $apiData['success'] && isset($apiData['url'])) {
                    $flipbookUrl = $apiData['url'];
                    $extractedId = $apiData['uid'] ?? basename(rtrim(parse_url($code, PHP_URL_PATH) ?? $code, '/'));
                    
                    // If the received URL has 'myebook/' followed by a number, use that number as ebook_id
                    if (preg_match('/myebook\/(\d+)/i', $flipbookUrl, $matches)) {
                        $extractedId = $matches[1];
                    }

                    // Fetch metadata from flipbook API by appending /schoolbag
                    $title = "Ebook - " . strtoupper($extractedId);
                    $metaStandard = null;
                    $metaSubject = null;
                    $metaPublication = null;
                    $metaSeries = null;
                    
                    try {
                        $metaApiUrl = rtrim($flipbookUrl, '/') . '/schoolbag';
                        $metaResponse = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(8)->get($metaApiUrl);
                        if ($metaResponse->successful()) {
                            $metaData = $metaResponse->json();
                            if (!empty($metaData['name'])) $title = $metaData['name'];
                            if (!empty($metaData['standard'])) $metaStandard = $metaData['standard'];
                            if (!empty($metaData['subject'])) $metaSubject = $metaData['subject'];
                            if (!empty($metaData['publication'])) $metaPublication = $metaData['publication'];
                            if (!empty($metaData['series'])) $metaSeries = $metaData['series'];
                        }
                    } catch (\Exception $e) {
                        // Silently fallback to defaults if meta fetch fails
                    }
                    
                    $existingAssigned = \App\Models\AssignedEbook::where('user_id', $user->id)
                        ->where('ebook_id', $extractedId)
                        ->first();

                    if ($existingAssigned) {
                        return response()->json([
                            'success' => true,
                            'already_assigned' => true,
                            'message' => " '{$title}' is already in your library!",
                            'redirect_url' => $flipbookUrl
                        ]);
                    }

                    $parsedUrl = parse_url($flipbookUrl);
                    $baseUrl = (isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) ? ($parsedUrl['scheme'] . '://' . $parsedUrl['host']) : '';
                    $coverImageUrl = $baseUrl ? ($baseUrl . '/uploads/ebook/ebook-' . $extractedId . '/1.jpg') : '';

                    return response()->json([
                        'success' => true,
                        'requires_confirmation' => true,
                        'message' => "Preview Ebook",
                        'ebook' => [
                            'id' => $extractedId,
                            'name' => $title,
                            'publication' => $metaPublication ?? 'MyEbook',
                            'subject' => $metaSubject ?? '',
                            'standard' => $metaStandard ?? '',
                            'series' => $metaSeries ?? '',
                            'url' => $flipbookUrl,
                            'cover_image' => $coverImageUrl
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore and fall through to default error message if API fails
            }
            
            return response()->json([
                'success' => false,
                'is_url' => true,
                'initial_url' => $code,
                'actual_url' => $code,
                'message' => '⚠️ Could not resolve Ebook URL from the scanned QR code.'
            ]);
        }

        // Step 2: Collect all string variations (original code & resolved actual URL) to match against internal DB
        $candidates = array_unique([$code, $actualUrl]);
        $ebook = null;

        foreach ($candidates as $val) {
            if (is_numeric($val) && !$ebook) {
                $ebook = Ebook::find($val);
            }
            if (!$ebook) {
                $ebook = Ebook::where('key_code', $val)
                    ->orWhere('uid', $val)
                    ->orWhere('ref_id', $val)
                    ->orWhere('key_link', $val)
                    ->orWhere('name', 'like', '%' . $val . '%')
                    ->first();
            }
            // Extract slug or ID from end of URL path
            if (!$ebook && preg_match('/\/([^\/?#]+)(?:[\?#].*)?$/', parse_url($val, PHP_URL_PATH) ?? $val, $matches)) {
                $slug = trim($matches[1]);
                if (is_numeric($slug)) {
                    $ebook = Ebook::find($slug);
                }
                if (!$ebook && !empty($slug)) {
                    $ebook = Ebook::where('key_code', $slug)
                        ->orWhere('uid', $slug)
                        ->orWhere('ref_id', $slug)
                        ->orWhere('key_link', 'like', '%' . $slug . '%')
                        ->orWhere('name', 'like', '%' . $slug . '%')
                        ->first();
                }
            }
            if ($ebook) break;
        }

        // If no matching internal ebook was found in database
        if (!$ebook) {
            return response()->json([
                'success' => false,
                'is_url' => $isUrl,
                'initial_url' => $code,
                'actual_url' => $actualUrl,
                'message' => $isUrl 
                    ? 'Resolved final URL from cover redirect successfully.' 
                    : 'Invalid or unrecognized QR Code. No matching ebook found in the library.'
            ]);
        }

        // If an internal ebook matched, check if already assigned
        $existing = \App\Models\AssignedEbook::where(function($query) use ($user) {
                $query->where('class_id', $user->class_id)
                      ->orWhere('user_id', $user->id);
            })
            ->where(function($query) use ($ebook) {
                $query->where('ebook_id', $ebook->id)
                      ->orWhere('title', $ebook->name)
                      ->orWhere('title', $ebook->subject);
            })
            ->first();

        if (!$existing) {
            $this->performAssign($user, $ebook);
            $msg = " '{$ebook->name}' has been successfully unlocked and added to your ebooks!";
        } else {
            $msg = " '{$ebook->name}' is already unlocked! Ready to view.";
        }

        return response()->json([
            'success' => true,
            'is_url' => $isUrl,
            'initial_url' => $code,
            'actual_url' => $actualUrl,
            'message' => $msg,
            'redirect_url' => route('student.ebooks.show', $ebook->id),
            'ebook' => [
                'id' => $ebook->id,
                'name' => $ebook->name,
                'publication' => $ebook->publication,
                'subject' => $ebook->subject,
                'standard' => $ebook->standard,
                'url' => route('student.ebooks.show', $ebook->id)
            ]
        ]);
    }



    public function assign(int $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $ebook = Ebook::findOrFail($id);

        // Check if already assigned
        $existing = \App\Models\AssignedEbook::where('user_id', $user->id)
            ->where('ebook_id', $ebook->id)
            ->first();

        if ($existing) {
            return redirect()->route('student.assigned_ebooks.index')
                ->with('success', 'Ebook is already assigned to your subjects.');
        }

        $this->performAssign($user, $ebook);

        return redirect()->route('student.assigned_ebooks.index')
            ->with('success', 'Ebook assigned successfully! You can now view it here.');
    }

    private function performAssign($user, $ebook)
    {
        // Get max order
        $maxOrder = \App\Models\AssignedEbook::where('class_id', $user->class_id)->max('order') ?? 0;

        // Create the Course (Subject)
        $course = \App\Models\AssignedEbook::create([
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
    }

    public function confirmAssignQr(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $request->validate([
            'ebook.id' => 'required|string',
            'ebook.url' => 'required|string',
            'ebook.name' => 'required|string',
        ]);

        $eb = $request->input('ebook');

        $existingAssigned = \App\Models\AssignedEbook::where(function($query) use ($user) {
                $query->where('class_id', $user->class_id)
                      ->orWhere('user_id', $user->id);
            })
            ->where('ebook_id', $eb['id'])
            ->first();

        if (!$existingAssigned) {
            \App\Models\AssignedEbook::create([
                'user_id' => $user->id,
                'class_id' => $user->class_id,
                'ebook_id' => $eb['id'],
                'ebook_url' => $eb['url'],
                'title' => $eb['name'],
                'standard' => $eb['standard'] ?? null,
                'subject' => $eb['subject'] ?? null,
                'publication' => $eb['publication'] ?? null,
                'series' => $eb['series'] ?? null,
                'is_active' => true,
            ]);
            $msg = " '{$eb['name']}' unlocked and saved to your library!";
        } else {
            $existingAssigned->update([
                'ebook_url' => $eb['url'],
                'title' => $eb['name'],
                'standard' => $eb['standard'] ?? null,
                'subject' => $eb['subject'] ?? null,
                'publication' => $eb['publication'] ?? null,
                'series' => $eb['series'] ?? null,
            ]);
            $msg = " '{$eb['name']}' is ready to view!";
        }

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }
}
