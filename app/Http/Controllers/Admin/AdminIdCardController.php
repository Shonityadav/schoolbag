<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdCardTemplate;
use App\Models\IdCardAsset;
use App\Models\InstituteIdCardSetting;
use App\Models\IdCardDownload;
use App\Services\IdCard\LayoutConverterService;
use App\Services\IdCard\IdCardFieldRegistry;
use App\Services\IdCard\AuditLoggerService;
use App\Services\DocumentRenderer\IdCardRenderer;
use App\Services\DocumentRenderer\DocumentRendererService;
use App\Models\UserIdentityCard;
use App\Jobs\ProcessBulkIdCardPrint;
use Illuminate\Support\Str;

class AdminIdCardController extends Controller
{
    public function index()
    {
        $instituteId = auth()->user()->institute_id;
        $templates = IdCardTemplate::where('institute_id', $instituteId)->latest()->get();
        return view('admin.id_cards.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.id_cards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:student,staff',
            'orientation' => 'required|in:portrait,landscape',
        ]);

        $template = IdCardTemplate::create([
            'uuid' => (string) Str::uuid(),
            'institute_id' => auth()->user()->institute_id,
            'name' => $request->name,
            'type' => $request->type,
            'orientation' => $request->orientation,
            'status' => 'Draft',
        ]);

        AuditLoggerService::log('Template Created', $template->id);

        return redirect()->route('admin.id_cards.designer', $template->uuid)->with('success', 'Template created. Welcome to the designer!');
    }

    public function edit(IdCardTemplate $template)
    {
        if ($template->institute_id !== auth()->user()->institute_id) {
            abort(403);
        }
        return view('admin.id_cards.edit', compact('template'));
    }

    public function update(Request $request, IdCardTemplate $template)
    {
        if ($template->institute_id !== auth()->user()->institute_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $template->update(['name' => $request->name]);

        AuditLoggerService::log('Template Renamed', $template->id);

        return redirect()->route('admin.id_cards.index')->with('success', 'Template renamed successfully.');
    }

    public function destroy(IdCardTemplate $template)
    {
        if ($template->institute_id !== auth()->user()->institute_id) {
            abort(403);
        }
        $templateId = $template->id;
        $template->delete();
        AuditLoggerService::log('Template Deleted', $templateId);
        return redirect()->route('admin.id_cards.index')->with('success', 'Template deleted successfully.');
    }

    // --- Designer Routes ---

    public function designer($uuid)
    {
        $template = IdCardTemplate::where('uuid', $uuid)->where('institute_id', auth()->user()->institute_id)->firstOrFail();
        
        $variables = IdCardFieldRegistry::getVariables();
        // Load assets for this institute
        $assets = IdCardAsset::where('institute_id', auth()->user()->institute_id)->get();
        
        // Ensure abstract layouts exist
        $frontLayout = $template->front_layout_json;
        if (!$frontLayout) {
            $frontLayout = [
                'canvas' => [
                    'width' => $template->orientation === 'landscape' ? 1012 : 638,
                    'height' => $template->orientation === 'landscape' ? 638 : 1012,
                    'unit' => 'px',
                    'background' => '#ffffff'
                ],
                'elements' => []
            ];
        }
        
        $backLayout = $template->back_layout_json;
        if (!$backLayout) {
            $backLayout = $frontLayout;
        }

        // Convert Abstract JSON to Fabric JSON for the editor
        $fabricFront = LayoutConverterService::convertAbstractToFabric($frontLayout);
        $fabricBack = LayoutConverterService::convertAbstractToFabric($backLayout);

        return view('admin.id_cards.designer', compact('template', 'variables', 'assets', 'fabricFront', 'fabricBack'));
    }

    public function saveLayout(Request $request, $uuid)
    {
        $template = IdCardTemplate::where('uuid', $uuid)->where('institute_id', auth()->user()->institute_id)->firstOrFail();

        $request->validate([
            'front_layout' => 'required|array',
            'back_layout' => 'required|array',
        ]);

        // Convert incoming Fabric JSON to internal Abstract JSON
        $abstractFront = LayoutConverterService::convertFabricToAbstract($request->front_layout);
        $abstractBack = LayoutConverterService::convertFabricToAbstract($request->back_layout);

        $template->update([
            'front_layout_json' => $abstractFront,
            'back_layout_json' => $abstractBack,
        ]);

        AuditLoggerService::log('Template Layout Saved', $template->id);

        return response()->json(['success' => true, 'message' => 'Layout saved automatically.']);
    }

    public function preview(Request $request, $uuid)
    {
        $template = IdCardTemplate::where('uuid', $uuid)->where('institute_id', auth()->user()->institute_id)->firstOrFail();

        $request->validate([
            'front_layout' => 'required|array',
            'back_layout' => 'required|array',
        ]);

        // Convert Fabric JSON to Abstract JSON
        $abstractFront = LayoutConverterService::convertFabricToAbstract($request->front_layout);
        $abstractBack = LayoutConverterService::convertFabricToAbstract($request->back_layout);

        $coreRenderer = new DocumentRendererService();
        $renderer = new IdCardRenderer($coreRenderer);
        
        $html = $renderer->renderPreview($abstractFront, $abstractBack);

        return response()->json(['html' => $html]);
    }

    public function publish($uuid)
    {
        $template = IdCardTemplate::where('uuid', $uuid)->where('institute_id', auth()->user()->institute_id)->firstOrFail();
        
        // TODO: Validate required fields using IdCardTemplateValidator
        
        // Archive existing published template of same type
        IdCardTemplate::where('institute_id', auth()->user()->institute_id)
            ->where('type', $template->type)
            ->where('status', 'Published')
            ->update(['status' => 'Archived']);

        $template->update(['status' => 'Published']);

        AuditLoggerService::log('Template Published', $template->id);

        return redirect()->route('admin.id_cards.index')->with('success', 'Template published successfully.');
    }

    public function duplicate($uuid)
    {
        $template = IdCardTemplate::where('uuid', $uuid)->where('institute_id', auth()->user()->institute_id)->firstOrFail();
        
        $newTemplate = $template->replicate();
        $newTemplate->uuid = (string) Str::uuid();
        $newTemplate->name = $newTemplate->name . ' (Copy)';
        $newTemplate->status = 'Draft';
        $newTemplate->save();

        return redirect()->route('admin.id_cards.index')->with('success', 'Template duplicated.');
    }

    public function archive($uuid)
    {
        $template = IdCardTemplate::where('uuid', $uuid)->where('institute_id', auth()->user()->institute_id)->firstOrFail();
        $template->update(['status' => 'Archived']);
        return redirect()->route('admin.id_cards.index')->with('success', 'Template archived.');
    }

    // --- Assets Routes ---

    public function uploadAsset(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'type' => 'required|in:logo,background,signature,icon,decorative',
            'name' => 'required|string',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
        $path = "uploads/idcards/institute_" . auth()->user()->institute_id . "/assets";
        $file->move(public_path($path), $fileName);

        $asset = IdCardAsset::create([
            'institute_id' => auth()->user()->institute_id,
            'name' => $request->name,
            'type' => $request->type,
            'file_path' => "$path/$fileName"
        ]);

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    public function deleteAsset(IdCardAsset $asset)
    {
        if ($asset->institute_id !== auth()->user()->institute_id) {
            abort(403);
        }
        
        if (file_exists(public_path($asset->file_path))) {
            unlink(public_path($asset->file_path));
        }
        $asset->delete();
        
        return response()->json(['success' => true]);
    }

    // --- Settings Route ---

    public function settings()
    {
        $settings = InstituteIdCardSetting::firstOrCreate(
            ['institute_id' => auth()->user()->institute_id]
        );
        return view('admin.id_cards.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = InstituteIdCardSetting::firstOrCreate(
            ['institute_id' => auth()->user()->institute_id]
        );

        $request->validate([
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'text_color' => 'required|string',
        ]);

        $settings->update([
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'text_color' => $request->text_color,
            'show_qr' => $request->has('show_qr'),
            'show_barcode' => $request->has('show_barcode'),
            'show_signature' => $request->has('show_signature'),
        ]);

        AuditLoggerService::log('Settings Updated', $settings->id);

        return redirect()->route('admin.id_cards.settings')->with('success', 'Settings updated.');
    }

    // --- Downloads Route ---

    public function downloads()
    {
        // Cleanup old downloads (older than 7 days)
        $oldDownloads = IdCardDownload::where('created_at', '<', now()->subDays(7))->get();
        foreach ($oldDownloads as $od) {
            if ($od->file_path && file_exists(public_path($od->file_path))) {
                unlink(public_path($od->file_path));
            }
            $od->delete();
        }

        $downloads = IdCardDownload::where('institute_id', auth()->user()->institute_id)->latest()->get();
        return view('admin.id_cards.downloads', compact('downloads'));
    }

    // --- Bulk Print Logic ---
    public function bulkPrint(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:id_card_templates,id',
            'user_ids' => 'required|string', // Comma separated IDs
            'type' => 'required|in:student,staff',
            'export_type' => 'required|in:single_pdf,zip'
        ]);

        $userIds = explode(',', $request->user_ids);

        // Create download entry
        $download = IdCardDownload::create([
            'institute_id' => auth()->user()->institute_id,
            'requested_by' => auth()->id(),
            'status' => 'Pending',
            'progress' => 0,
        ]);

        // Dispatch Job
        ProcessBulkIdCardPrint::dispatch($download->id, $request->template_id, $userIds, $request->type, $request->export_type);

        AuditLoggerService::log('Bulk Print Initiated', ['download_id' => $download->id, 'type' => $request->type, 'count' => count($userIds)]);

        return redirect()->route('admin.id_cards.downloads')->with('success', 'Bulk print queued! You can track the progress here.');
    }

    // --- Single Issue & Revoke ---
    public function revokeCard(UserIdentityCard $card)
    {
        // Verify ownership via template->institute
        if ($card->template->institute_id !== auth()->user()->institute_id) {
            abort(403);
        }

        $card->update(['status' => 'Revoked']);
        AuditLoggerService::log('Card Revoked', $card->id);

        return back()->with('success', 'Card revoked successfully.');
    }
}
