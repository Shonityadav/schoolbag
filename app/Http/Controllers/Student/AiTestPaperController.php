<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ebook;
use App\Models\EbookPage;
use App\Models\Categories;
use App\Models\Series;
use App\Models\AiPaperGenerator;
use App\Models\AiQuesType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiTestPaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student', ['except' => ['home', 'showPaper', 'create', 'store']]);
    }
    
    public function home($id)
    {
        $companyId = config('app.company_id', 1); // tenant isolation
        
        // The $id passed from the UI is the AssignedEbook ID
        $assignedEbook = \App\Models\AssignedEbook::findOrFail($id);
        $ebookId = $assignedEbook->getResolvedEbookId();

        $data = Ebook::with('pages')
            ->where('id', $ebookId)
            ->orWhere('uid', $ebookId)
            ->firstOrFail();

        // Inject the ebook URL from the assignment into the data object for the view
        $data->ebook_url = $assignedEbook->ebook_url;

        $firstPages = $data->pages
            ->where('index', 1)
            ->pluck('position')
            ->all();
            
        $testPapers = AiPaperGenerator::where('ebook_id', $data->id)
            ->where('company_id', $companyId)
            ->select(
                'set_id',
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('COUNT(id) as total_questions')
            )
            ->groupBy('set_id')
            ->orderByDesc('created_at')
            ->get();
            
        // Removed mysql2 connection for companies, assume unlimited or 0 for now
        $tokensAvailable = 1000;

        $assigned_ebook_id = $id;

        return view('aitest.index', compact('data', 'firstPages', 'testPapers', 'tokensAvailable', 'assigned_ebook_id'));
    }

    public function showPaper(Request $request)
    {
        $companyId = config('app.company_id', 1);

        $ebookId = $request->ebook;
        $setId   = $request->set;

        if (!$ebookId || !$setId) {
            abort(404);
        }
        
        $data = Ebook::with('pages')
            ->where('id', $ebookId)
            ->orWhere('uid', $ebookId)
            ->first();
            
        $firstPages = $data->pages
            ->where('index', 1)
            ->pluck('position')
            ->all();
            
        $ebook = Ebook::findOrFail($ebookId);

        $questions = AiPaperGenerator::with('questionType')
            ->where('ebook_id', $ebookId)
            ->where('company_id', $companyId)
            ->where('set_id', $setId)
            ->orderBy('id')
            ->get();

        return view('aitest.paper', compact('data', 'firstPages','ebook', 'questions', 'setId'));
    }

    public function create(Request $request)
    {
        return view('aitest.generate', [
            'base_url' => $request->base_url,
            'pages'    => $request->pages,
            'ebook_id' => $request->ebook_id,
            'assigned_ebook_id' => $request->assigned_ebook_id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ebook_id' => 'required|integer',
            'paperQuestions' => 'required|array'
        ]);

        $companyId = config('app.company_id', 1);
        $ebookId = $request->ebook_id;

        // Generate auto-incrementing set_id based on max existing
        $maxSetId = AiPaperGenerator::where('ebook_id', $ebookId)->max('set_id');
        $newSetId = $maxSetId ? $maxSetId + 1 : 1;

        foreach ($request->paperQuestions as $q) {
            
            // Try to find the question type if provided
            $quesTypeId = null;
            if (isset($q['type_name'])) {
                $quesType = AiQuesType::firstOrCreate(
                    ['name' => $q['type_name'], 'company_id' => $companyId],
                    ['marks' => 1, 'status' => 1]
                );
                $quesTypeId = $quesType->id;
            }

            AiPaperGenerator::create([
                'set_id' => $newSetId,
                'ebook_id' => $ebookId,
                'chapter' => $q['chapter'] ?? null,
                'chapter_num' => $q['chapter_num'] ?? null,
                'section' => $q['section'] ?? null,
                'ques_type_id' => $quesTypeId,
                'question' => $q['question'] ?? null,
                'diagrams' => isset($q['diagrams']) ? json_encode($q['diagrams']) : null,
                'options' => isset($q['options']) ? json_encode($q['options']) : null,
                'answer' => $q['answer'] ?? null,
                'answer_text' => $q['answer_text'] ?? null,
                'company_id' => $companyId,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Paper saved successfully!',
            'set_id' => $newSetId
        ]);
    }
}
