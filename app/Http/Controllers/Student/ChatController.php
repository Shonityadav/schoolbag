<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Ebook;
use App\Models\EbookChapter;
use App\Models\Categories;
use App\Models\Series;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
public function __construct()
    {
        $this->middleware('auth',  ['except' => ['index', 'saveIndexPage', 'saveChapters']]);
    }

    public function index(Request $request)
    {
        $ebookId = $request->query('ebook_id');
        $ebookUrl = $request->query('ebook_url');
        $indexPages = $request->query('index_pages');

        /*
        |--------------------------------------------------------------------------
        | Get saved chapters
        |--------------------------------------------------------------------------
        */

        $savedChapters = [];

        if ($ebookId) {
            $savedChapters = EbookChapter::where('ebook_id', $ebookId)
                ->orderBy('chapter_number')
                ->get()
                ->map(function ($chapter) {
                    return [
                        'title' => $chapter->chapter_name,
                        'start_page' => $chapter->start_page,
                        'end_page' => $chapter->end_page,
                    ];
                })
                ->values()
                ->toArray();
        }

        return view(
            'chat_with_ebook.index',
            compact(
                'ebookId',
                'ebookUrl',
                'indexPages',
                'savedChapters'
            )
        );
    }

    
    public function saveIndexPage(Request $request)
	{
		$request->validate([
		    'ebook_id'   => 'required|integer',
		    'index_page' => 'required|integer'
		]);
		$updated = Ebook::where('id', $request->ebook_id)
		    ->update([
		        'index_page' => $request->index_page
		    ]);

		if (!$updated) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to save index page. Ebook not found.'
			], 404);
		}

		return response()->json([
		    'success' => true
		]);
	}
    public function saveChapters(Request $request)
    {
        $request->validate([
            'ebook_id' => 'required|integer|exists:ebooks,id',
            'chapters' => 'required|array|min:1',
        ]);

        $ebookId = $request->ebook_id;

        /*
        |--------------------------------------------------------------------------
        | Remove old chapters
        |--------------------------------------------------------------------------
        |
        | This ensures that if chapters are fetched again,
        | we don't create duplicates.
        |
        */

        EbookChapter::where('ebook_id', $ebookId)->delete();

        /*
        |--------------------------------------------------------------------------
        | Insert chapters individually
        |--------------------------------------------------------------------------
        */

        $chapters = [];

        foreach ($request->chapters as $index => $chapter) {

            $chapters[] = EbookChapter::create([
                'ebook_id' => $ebookId,

                'chapter_name' => $chapter['title'] ?? 'Chapter ' . ($index + 1),

                'start_page' => (int) ($chapter['start_page'] ?? 0),

                'end_page' => isset($chapter['end_page'])
                    ? (int) $chapter['end_page']
                    : null,

                'chapter_number' => $index + 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chapters saved successfully.',

            'chapters' => collect($chapters)
                ->map(function ($chapter) {
                    return [
                        'title' => $chapter->chapter_name,
                        'start_page' => $chapter->start_page,
                        'end_page' => $chapter->end_page,
                    ];
                })
                ->values()
                ->toArray(),
        ]);
    }
}

