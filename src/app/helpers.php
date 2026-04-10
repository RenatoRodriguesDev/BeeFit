<?php

if (!function_exists('avatar_url')) {
    function avatar_url(?string $path): string
    {
        if (!$path) return '';
        if (str_starts_with($path, 'http')) return $path;
        return asset('storage/' . $path);
    }
}
