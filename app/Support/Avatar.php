<?php

namespace App\Support;

class Avatar
{
    /** Avatar bulat berisi inisial nama (data URI SVG, tidak butuh internet / file). */
    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $ini = mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 150 150">'
            . '<rect width="150" height="150" fill="#f2eafa"/>'
            . '<text x="50%" y="50%" dy=".35em" text-anchor="middle" font-family="Arial,sans-serif" font-size="58" font-weight="700" fill="#6f579e">'
            . e($ini) . '</text></svg>';

        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
    }
}
