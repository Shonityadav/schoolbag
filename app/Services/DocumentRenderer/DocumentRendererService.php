<?php

namespace App\Services\DocumentRenderer;

use Exception;

class DocumentRendererService
{
    /**
     * Renders an abstract layout JSON into HTML.
     * 
     * @param array $layout The abstract layout array (canvas & elements)
     * @param array|object $data The dynamic data context
     * @param string $mode 'preview' (HTML, shadow, borders), 'print' (HTML, no shadow, strict), 'pdf' (HTML, no JS, optimized for dompdf)
     * @return string HTML output
     */
    public function render(array $layout, $data = [], string $mode = 'preview'): string
    {
        try {
            $canvas = $layout['canvas'] ?? ['width' => 1012, 'height' => 638, 'background' => '#ffffff'];
            $elements = $layout['elements'] ?? [];

            // Sort elements by z_index
            usort($elements, function ($a, $b) {
                return ($a['z_index'] ?? 0) <=> ($b['z_index'] ?? 0);
            });

            // Start building HTML
            $html = '';
            $html .= $this->buildCanvasWrapperStart($canvas, $mode);

            foreach ($elements as $element) {
                if (isset($element['properties']['visibility']) && $element['properties']['visibility'] === false) {
                    continue;
                }
                $html .= $this->renderElement($element, $data, $mode);
            }

            $html .= $this->buildCanvasWrapperEnd();

            return $html;
        } catch (Exception $e) {
            return '<div style="color:red; border:1px solid red; padding:20px;">Renderer Error: ' . $e->getMessage() . '</div>';
        }
    }

    private function buildCanvasWrapperStart(array $canvas, string $mode): string
    {
        $width = $canvas['width'];
        $height = $canvas['height'];
        $bg = $canvas['background'];

        $styles = [
            "width: {$width}px",
            "height: {$height}px",
            "background: {$bg}",
            "position: relative",
            "overflow: hidden",
            "box-sizing: border-box"
        ];

        if ($mode === 'preview') {
            $styles[] = "box-shadow: 0 5px 15px rgba(0,0,0,0.2)";
            $styles[] = "border: 1px solid #ccc";
            $styles[] = "margin: 0 auto";
        } elseif ($mode === 'print') {
            // Perfect page break setup usually handled outside, but clean strict wrapper
            $styles[] = "page-break-inside: avoid";
        } elseif ($mode === 'pdf') {
            $styles[] = "margin: 0";
            $styles[] = "padding: 0";
        }

        $styleString = implode('; ', $styles) . ';';

        return "<div class=\"document-canvas\" style=\"{$styleString}\">\n";
    }

    private function buildCanvasWrapperEnd(): string
    {
        return "</div>\n";
    }

    private function renderElement(array $element, $data, string $mode): string
    {
        $type = $element['type'] ?? 'rectangle';
        $style = $this->buildElementStyle($element, $mode);

        $html = "<div class=\"element-{$type}\" style=\"{$style}\">";
        $html .= $this->renderElementContent($element, $data, $mode);
        $html .= "</div>\n";

        return $html;
    }

    private function buildElementStyle(array $element, string $mode): string
    {
        $pos = $element['position'] ?? ['x' => 0, 'y' => 0];
        $dim = $element['dimensions'] ?? ['width' => 100, 'height' => 100, 'angle' => 0];
        $props = $element['properties'] ?? [];

        $styles = [
            "position: absolute",
            "left: {$pos['x']}px",
            "top: {$pos['y']}px",
            "width: {$dim['width']}px",
            "height: {$dim['height']}px",
            "box-sizing: border-box",
            "transform-origin: top left"
        ];

        // Rotation
        if (!empty($dim['angle']) && $dim['angle'] != 0) {
            $styles[] = "transform: rotate({$dim['angle']}deg)";
        }

        // Opacity
        if (isset($props['opacity']) && $props['opacity'] < 1) {
            $styles[] = "opacity: {$props['opacity']}";
        }

        // Background / Fill
        if (isset($props['fill']) && $props['fill'] !== 'transparent') {
            if ($element['type'] === 'rectangle' || $element['type'] === 'circle') {
                $styles[] = "background-color: {$props['fill']}";
            }
        }

        // Border / Stroke
        if (!empty($props['strokeWidth'])) {
            $stroke = $props['stroke'] ?? '#000000';
            $styles[] = "border: {$props['strokeWidth']}px solid {$stroke}";
        }

        // Border Radius (for circle or rect)
        if ($element['type'] === 'circle') {
            $styles[] = "border-radius: 50%";
        } elseif (!empty($props['rx'])) {
            $styles[] = "border-radius: {$props['rx']}px";
        }

        // Text Styles
        if ($element['type'] === 'text') {
            if (!empty($props['fontFamily'])) $styles[] = "font-family: '{$props['fontFamily']}', sans-serif";
            if (!empty($props['fontSize'])) $styles[] = "font-size: {$props['fontSize']}px";
            if (!empty($props['fill'])) $styles[] = "color: {$props['fill']}";
            if (!empty($props['fontWeight']) && $props['fontWeight'] === 'bold') $styles[] = "font-weight: bold";
            if (!empty($props['fontStyle']) && $props['fontStyle'] === 'italic') $styles[] = "font-style: italic";
            if (!empty($props['textAlign'])) $styles[] = "text-align: {$props['textAlign']}";
            if (!empty($props['lineHeight'])) $styles[] = "line-height: {$props['lineHeight']}";
            if (!empty($props['underline']) && $props['underline']) $styles[] = "text-decoration: underline";
            
            // Flex box alignment for text if needed (simulating Fabric.js i-text behavior)
            // Fabric i-text top-left bounding box handles text flow, so standard block flow is usually fine.
            $styles[] = "word-wrap: break-word";
        }

        return implode('; ', $styles) . ';';
    }

    private function renderElementContent(array $element, $data, string $mode): string
    {
        $type = $element['type'] ?? 'rectangle';
        $props = $element['properties'] ?? [];

        switch ($type) {
            case 'text':
                $rawText = $props['text'] ?? '';
                $resolvedText = VariableResolverService::resolve($rawText, $data);
                return nl2br(htmlspecialchars($resolvedText));

            case 'photo':
            case 'logo':
            case 'signature':
            case 'qr_code':
            case 'barcode':
            case 'image':
                $assetUrl = AssetResolverService::resolveAsset($element, $data, $mode);
                if ($assetUrl) {
                    $borderRadius = ($type === 'circle' || !empty($props['rx'])) ? 'border-radius: inherit;' : '';
                    return "<img src=\"{$assetUrl}\" style=\"width: 100%; height: 100%; object-fit: fill; display: block; {$borderRadius}\" alt=\"{$type}\" />";
                }
                return '';

            case 'line':
                // The container handles position, width, height, stroke width and color as border.
                // An explicit SVG line could be drawn, or we just rely on CSS border for horizontal lines.
                return '';

            case 'rectangle':
            case 'circle':
            default:
                // Content is empty, styling handles the look
                return '';
        }
    }
}
