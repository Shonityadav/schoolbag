<?php

namespace App\Services\DocumentRenderer;

use Illuminate\Support\Arr;

class VariableResolverService
{
    /**
     * Replaces dynamic variables in the text with actual data.
     * Supports nested dot notation (e.g., student.admission_number, class.standard).
     * Missing variables are left as is, or optionally replaced with empty strings.
     *
     * @param string $text The text containing variables like {{student.name}}
     * @param array|object $data The data source
     * @param string $fallback Fallback string if missing. Defaults to keeping the tag.
     * @return string
     */
    public static function resolve(string $text, $data, $fallback = null): string
    {
        if (empty($text) || !str_contains($text, '{{')) {
            return $text;
        }

        // Convert object to array for dot notation access if needed
        if (is_object($data)) {
            // Using json encode/decode as a quick deep array conversion
            $data = json_decode(json_encode($data), true);
        }

        return preg_replace_callback('/\{\{([^}]+)\}\}/', function ($matches) use ($data, $fallback) {
            $key = trim($matches[1]);
            
            // Support simple replacements without prefix if needed, or exact matching
            // We use Arr::get for dot notation support.
            $value = Arr::get($data, $key);

            if ($value !== null) {
                return $value;
            }

            // Fallback behavior
            return $fallback !== null ? $fallback : $matches[0];
        }, $text);
    }
}
