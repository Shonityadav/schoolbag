<?php

namespace App\Services\DocumentRenderer;

use App\Models\IdCardTemplate;
use Illuminate\Support\Facades\Cache;

class IdCardRenderer
{
    protected DocumentRendererService $coreRenderer;

    public function __construct(DocumentRendererService $coreRenderer)
    {
        $this->coreRenderer = $coreRenderer;
    }

    /**
     * Renders a live preview HTML using AJAX (from the designer)
     * 
     * @param array $frontLayout Abstract JSON array
     * @param array $backLayout Abstract JSON array
     * @return string HTML containing front and back sides
     */
    public function renderPreview(array $frontLayout, array $backLayout): string
    {
        // For preview, we use dummy data.
        $dummyData = $this->getDummyData();

        $frontHtml = $this->coreRenderer->render($frontLayout, $dummyData, 'preview');
        $backHtml = $this->coreRenderer->render($backLayout, $dummyData, 'preview');

        return "<div class='id-card-preview-container d-flex gap-4 justify-content-center'>
                    <div><h6 class='text-center text-muted mb-2'>Front Side</h6>{$frontHtml}</div>
                    <div><h6 class='text-center text-muted mb-2'>Back Side</h6>{$backHtml}</div>
                </div>";
    }

    /**
     * Render the ID Card for a specific user into pure HTML
     * 
     * @param IdCardTemplate $template
     * @param mixed $user User model (StudentDetail or StaffDetail)
     * @return string
     */
    public function renderHtml(IdCardTemplate $template, $user): string
    {
        $userData = $this->prepareUserData($user);
        
        $frontHtml = $this->coreRenderer->render($template->front_layout_json ?? [], $userData, 'html');
        $backHtml = $this->coreRenderer->render($template->back_layout_json ?? [], $userData, 'html');

        return "<div class='id-card-wrapper'>{$frontHtml}{$backHtml}</div>";
    }

    /**
     * Render printable version (optimized for browser printing)
     * 
     * @param IdCardTemplate $template
     * @param mixed $user
     * @return string
     */
    public function renderPrintable(IdCardTemplate $template, $user): string
    {
        $userData = $this->prepareUserData($user);
        
        $frontHtml = $this->coreRenderer->render($template->front_layout_json ?? [], $userData, 'print');
        $backHtml = $this->coreRenderer->render($template->back_layout_json ?? [], $userData, 'print');

        return "<div class='printable-card' style='page-break-after: always;'>
                    {$frontHtml}
                    <div style='page-break-before: always;'></div>
                    {$backHtml}
                </div>";
    }

    /**
     * Render PDF ready HTML string
     * 
     * @param IdCardTemplate $template
     * @param mixed $user
     * @return string
     */
    public function renderPdf(IdCardTemplate $template, $user): string
    {
        // For PDF rendering in DomPDF, we need base64 assets and strict margin handling.
        $userData = $this->prepareUserData($user);

        $frontHtml = $this->coreRenderer->render($template->front_layout_json ?? [], $userData, 'pdf');
        $backHtml = $this->coreRenderer->render($template->back_layout_json ?? [], $userData, 'pdf');

        return "<div class='pdf-card-wrapper' style='page-break-after: always;'>
                    {$frontHtml}
                    <div style='page-break-before: always;'></div>
                    {$backHtml}
                </div>";
    }

    /**
     * Resolves real user relationships dynamically.
     */
    private function prepareUserData($user): array
    {
        // Use json serialization to easily cast models and their nested relationships to array.
        // Ensure relationships (like class, section, etc.) are eager loaded before calling this function!
        return json_decode(json_encode($user), true);
    }

    private function getDummyData(): array
    {
        return [
            'uuid' => 'dummy-1234-5678',
            'admission_number' => 'ADM-2026-001',
            'roll_number' => '12',
            'student_name' => 'John Doe',
            'father_name' => 'Mr. Richard Doe',
            'mother_name' => 'Mrs. Jane Doe',
            'class' => '10th',
            'section' => 'A',
            'blood_group' => 'O+',
            'dob' => '2010-05-15',
            'phone' => '+1 234 567 8900',
            'address' => '123 Fake Street, Tech City',
            
            'employee_id' => 'EMP-007',
            'staff_name' => 'Dr. Alan Turing',
            'designation' => 'Principal',
            'department' => 'Administration',
            
            'institute_name' => 'Acetech International School',
            'academic_year' => '2026-2027',
            'issue_date' => date('Y-m-d'),
            'expiry_date' => date('Y-m-d', strtotime('+1 year')),
            
            'photo' => null, // triggers fallback avatar
        ];
    }
}
