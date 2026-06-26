<?php

namespace App\Services\IdCard;

class LayoutConverterService
{
    /**
     * Converts a raw Fabric.js JSON export into our Abstract Internal Layout JSON.
     * This decouples the database from the specific frontend editor used.
     */
    public static function convertFabricToAbstract(array $fabricJson): array
    {
        $abstract = [
            'canvas' => [
                'width' => $fabricJson['width'] ?? 1012, // CR80 standard horizontal approx
                'height' => $fabricJson['height'] ?? 638,
                'unit' => 'px',
                'background' => $fabricJson['background'] ?? '#ffffff'
            ],
            'elements' => []
        ];

        if (isset($fabricJson['objects']) && is_array($fabricJson['objects'])) {
            foreach ($fabricJson['objects'] as $index => $obj) {
                // Determine our internal type from Fabric type
                $internalType = self::mapFabricTypeToInternal($obj);

                $element = [
                    'id' => $obj['id'] ?? uniqid('el_'),
                    'name' => $obj['name'] ?? ($internalType . '_' . $index),
                    'type' => $internalType,
                    'locked' => $obj['selectable'] === false,
                    'z_index' => $index,
                    'position' => [
                        'x' => $obj['left'] ?? 0,
                        'y' => $obj['top'] ?? 0,
                    ],
                    'dimensions' => [
                        'width' => ($obj['width'] ?? 0) * ($obj['scaleX'] ?? 1),
                        'height' => ($obj['height'] ?? 0) * ($obj['scaleY'] ?? 1),
                        'angle' => $obj['angle'] ?? 0,
                    ],
                    'properties' => self::extractProperties($internalType, $obj)
                ];

                $abstract['elements'][] = $element;
            }
        }

        return $abstract;
    }

    /**
     * Converts our Abstract Internal Layout JSON back into Fabric.js JSON.
     */
    public static function convertAbstractToFabric(array $abstractJson): array
    {
        $fabric = [
            'version' => '5.3.0',
            'objects' => [],
            'background' => $abstractJson['canvas']['background'] ?? '#ffffff',
            'width' => $abstractJson['canvas']['width'] ?? 1012,
            'height' => $abstractJson['canvas']['height'] ?? 638,
        ];

        if (isset($abstractJson['elements']) && is_array($abstractJson['elements'])) {
            foreach ($abstractJson['elements'] as $el) {
                $fabricObj = [
                    'type' => self::mapInternalTypeToFabric($el['type']),
                    'id' => $el['id'],
                    'name' => $el['name'],
                    'left' => $el['position']['x'],
                    'top' => $el['position']['y'],
                    'width' => $el['dimensions']['width'],
                    'height' => $el['dimensions']['height'],
                    'angle' => $el['dimensions']['angle'],
                    'scaleX' => 1,
                    'scaleY' => 1,
                    'selectable' => !$el['locked'],
                    'originX' => 'left',
                    'originY' => 'top',
                ];

                $fabricObj = array_merge($fabricObj, $el['properties'] ?? []);

                $fabric['objects'][] = $fabricObj;
            }
        }

        return $fabric;
    }

    private static function mapFabricTypeToInternal(array $fabricObj): string
    {
        $type = strtolower($fabricObj['type'] ?? 'rect');

        // Check metadata name/id for special types
        $name = strtolower($fabricObj['name'] ?? '');
        if (str_contains($name, 'qr_code')) return 'qr_code';
        if (str_contains($name, 'barcode')) return 'barcode';
        if (str_contains($name, 'user_photo')) return 'photo';
        if (str_contains($name, 'logo')) return 'logo';
        if (str_contains($name, 'signature')) return 'signature';

        return match ($type) {
            'i-text', 'text', 'textbox' => 'text',
            'image' => 'image',
            'rect' => 'rectangle',
            'circle' => 'circle',
            'line' => 'line',
            default => 'rectangle',
        };
    }

    private static function mapInternalTypeToFabric(string $internalType): string
    {
        return match ($internalType) {
            'text' => 'i-text',
            'image', 'photo', 'qr_code', 'barcode', 'logo', 'signature' => 'image',
            'rectangle' => 'rect',
            'circle' => 'circle',
            'line' => 'line',
            default => 'rect',
        };
    }

    private static function extractProperties(string $internalType, array $obj): array
    {
        $props = [];
        
        if ($internalType === 'text') {
            $props = [
                'text' => $obj['text'] ?? '',
                'fontFamily' => $obj['fontFamily'] ?? 'Arial',
                'fontSize' => $obj['fontSize'] ?? 20,
                'fill' => $obj['fill'] ?? '#000000',
                'fontWeight' => $obj['fontWeight'] ?? 'normal',
                'fontStyle' => $obj['fontStyle'] ?? 'normal',
                'textAlign' => $obj['textAlign'] ?? 'left',
                'underline' => $obj['underline'] ?? false,
                'lineHeight' => $obj['lineHeight'] ?? 1.16,
            ];
        } else {
            $props = [
                'fill' => $obj['fill'] ?? 'transparent',
                'stroke' => $obj['stroke'] ?? null,
                'strokeWidth' => $obj['strokeWidth'] ?? 0,
                'src' => $obj['src'] ?? null, // for images
                'radius' => $obj['radius'] ?? 0, // for circles or rounded rects
            ];
            
            // Fix radius property mapping for rects
            if ($internalType === 'rectangle' || $internalType === 'photo') {
                $props['rx'] = $obj['rx'] ?? 0;
                $props['ry'] = $obj['ry'] ?? 0;
            }
        }

        return $props;
    }
}
