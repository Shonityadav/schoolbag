<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\IdCardDownload;
use App\Models\IdCardTemplate;
use App\Models\StudentDetails;
use App\Models\StaffDetails;
use App\Models\UserIdentityCard;
use App\Services\DocumentRenderer\IdCardRenderer;
use App\Services\DocumentRenderer\DocumentRendererService;
use App\Services\IdCard\PdfGeneratorService;
use Illuminate\Support\Str;

class ProcessBulkIdCardPrint implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour max
    
    protected $downloadId;
    protected $templateId;
    protected $userIds;
    protected $type;
    protected $exportType;

    /**
     * Create a new job instance.
     */
    public function __construct($downloadId, $templateId, array $userIds, $type, $exportType = 'single_pdf')
    {
        $this->downloadId = $downloadId;
        $this->templateId = $templateId;
        $this->userIds = $userIds;
        $this->type = $type;
        $this->exportType = $exportType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $download = IdCardDownload::find($this->downloadId);
        if (!$download) return;

        $download->update(['status' => 'Processing', 'progress' => 5]);

        $template = IdCardTemplate::find($this->templateId);
        if (!$template) {
            $download->update(['status' => 'Failed', 'progress' => 0]);
            return;
        }

        $coreRenderer = new DocumentRendererService();
        $renderer = new IdCardRenderer($coreRenderer);
        $pdfService = new PdfGeneratorService();

        $htmlCards = [];
        $zipData = [];
        $total = count($this->userIds);
        $processed = 0;

        $download->update(['status' => 'Processing', 'progress' => 10]);

        // Chunking the users based on provided IDs
        $chunkSize = 100;
        $chunks = array_chunk($this->userIds, $chunkSize);

        foreach ($chunks as $chunk) {
            $users = [];
            if ($this->type === 'student') {
                $users = StudentDetails::with(['class', 'user'])->whereIn('created_for', $chunk)->get();
            } else {
                $users = StaffDetails::with(['user'])->whereIn('created_for', $chunk)->get();
            }

            foreach ($users as $user) {
                // Issue or duplicate card record
                $identityCard = UserIdentityCard::create([
                    'user_id' => $user->created_for,
                    'template_id' => $template->id,
                    'token' => Str::random(32),
                    'status' => 'Active',
                    'issued_on' => now(),
                    'expires_on' => now()->addYear(),
                    'printed_by' => $download->requested_by,
                ]);

                // We inject the identity card details into the user payload so the renderer can use the token for QR codes
                $userData = clone $user;
                $userData->uuid = $identityCard->token;

                $html = $renderer->renderPdf($template, $userData);

                if ($this->exportType === 'zip') {
                    $name = $userData->user->name ?? 'Unknown';
                    $filename = Str::slug($name . '-' . $userData->created_for) . '.pdf';
                    $zipData[] = [
                        'html' => $html,
                        'filename' => $filename
                    ];
                } else {
                    $htmlCards[] = $html;
                }

                $processed++;
                $progress = 10 + round(($processed / $total) * 70); // Up to 80%
                
                // Only update progress every 10 users to prevent DB spam
                if ($processed % 10 === 0 || $processed === $total) {
                    $download->update(['progress' => $progress]);
                }
            }
        }

        $download->update(['status' => 'Processing', 'progress' => 85]);

        $publicPath = 'uploads/idcards/institute_' . $download->institute_id . '/downloads';
        if (!file_exists(public_path($publicPath))) {
            mkdir(public_path($publicPath), 0755, true);
        }

        $fileName = 'id_cards_' . time() . '_' . Str::random(5);
        
        // Use CR80 portrait size generally: approx [0,0, 154, 243] points for standard ID card or 'a4' for grids
        // Since renderer handles layout size, let's use a4 for easy printing
        $format = 'a4'; 

        if ($this->exportType === 'zip') {
            $download->update(['status' => 'Processing', 'progress' => 90]);
            $fileName .= '.zip';
            $success = $pdfService->generateZip($zipData, public_path("$publicPath/$fileName"), $format);
            if (!$success) {
                $download->update(['status' => 'Failed', 'progress' => 0]);
                return;
            }
        } else {
            $fileName .= '.pdf';
            $pdfContent = $pdfService->generateSinglePdf($htmlCards, $format);
            file_put_contents(public_path("$publicPath/$fileName"), $pdfContent);
        }

        $download->update([
            'status' => 'Completed',
            'progress' => 100,
            'file_path' => "$publicPath/$fileName"
        ]);
    }
}
