<?php

namespace App\Services\DocumentRenderer;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Exception;

class AssetResolverService
{
    /**
     * Resolves the final asset URL/Data URI for a given element and mode.
     * 
     * @param array $element The abstract JSON element
     * @param array|object $userData The data context (for user photo, QR generation, etc)
     * @param string $mode 'html' or 'pdf' (PDF often requires absolute paths or base64)
     * @return string|null
     */
    public static function resolveAsset(array $element, $userData = [], string $mode = 'html'): ?string
    {
        $type = $element['type'] ?? 'image';

        try {
            switch ($type) {
                case 'photo':
                    return self::resolveUserPhoto($userData, $mode);
                    
                case 'qr_code':
                    // QR codes usually embed a verification URL or unique ID
                    $qrContent = isset($userData['uuid']) 
                        ? url("/verify/{$userData['uuid']}") 
                        : (isset($userData['id']) ? url("/verify/{$userData['id']}") : 'https://acetech.in');
                    return self::generateQrCode($qrContent, $mode);
                    
                case 'barcode':
                    $barcodeContent = $userData['admission_number'] ?? ($userData['employee_id'] ?? '123456789');
                    return self::generateBarcode($barcodeContent, $mode);
                    
                case 'logo':
                case 'signature':
                case 'image':
                    return self::resolveStaticImage($element['properties']['src'] ?? null, $mode);
                    
                default:
                    return null;
            }
        } catch (Exception $e) {
            // Never crash, return fallback
            return self::getFallbackImage($mode);
        }
    }

    private static function resolveUserPhoto($userData, $mode)
    {
        $photoPath = $userData['photo'] ?? null;
        if (!$photoPath || !file_exists(public_path($photoPath))) {
            return self::getFallbackAvatar($mode);
        }

        if ($mode === 'pdf') {
            return self::pathToBase64(public_path($photoPath));
        }

        return asset($photoPath);
    }

    private static function resolveStaticImage($src, $mode)
    {
        if (!$src) {
            return self::getFallbackImage($mode);
        }

        // If it's an external URL or already a base64 string
        if (str_starts_with($src, 'data:') || str_starts_with($src, 'http')) {
            return $src;
        }

        // It might be a relative URL from our app. Strip out the root url if present
        $baseUrl = url('/');
        $relativePath = str_replace($baseUrl . '/', '', $src);
        
        $absolutePath = public_path($relativePath);
        if (file_exists($absolutePath)) {
            if ($mode === 'pdf') {
                return self::pathToBase64($absolutePath);
            }
            return asset($relativePath);
        }

        return self::getFallbackImage($mode);
    }

    private static function generateQrCode($content, $mode)
    {
        // For PDF, Base64 PNG is highly recommended over SVG to prevent dompdf crashes
        if ($mode === 'pdf') {
            $pngData = QrCode::format('png')->size(300)->margin(0)->generate($content);
            return 'data:image/png;base64,' . base64_encode($pngData);
        }

        // For HTML, SVG is perfect and crisp
        $svgData = QrCode::format('svg')->size(300)->margin(0)->generate($content);
        return 'data:image/svg+xml;base64,' . base64_encode($svgData);
    }

    private static function generateBarcode($content, $mode)
    {
        // Placeholder for barcode generation. 
        // In a real app we might use a package like picqer/php-barcode-generator.
        // For now, return a dummy transparent pixel or fallback image until package is added.
        return self::getFallbackImage($mode);
    }

    public static function pathToBase64($path)
    {
        if (!file_exists($path)) {
            return null;
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public static function getFallbackAvatar($mode)
    {
        // Minimal gray circle SVG as fallback avatar
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#cccccc"/><circle cx="50" cy="35" r="20" fill="#999999"/><path d="M20 90 Q50 60 80 90" stroke="#999999" stroke-width="10" fill="none"/></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function getFallbackImage($mode)
    {
        // Minimal transparent pixel or missing image placeholder
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }
}
