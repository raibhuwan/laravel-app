<?php

$logo = env('LOGO', 'logo_default_dark.png');

return [
    /**
     * The link to goto admin backend
     */

    'admin_backend_url' => env('ADMIN_BACKEND_URL', 'admin'),
    'copyright_text' => env('COPYRIGHT_TEXT', 'Datingappscript.com'),
    'copyright_year' => env('COPYRIGHT_YEAR', \Carbon\Carbon::now()->year),
    'logo' => ($logo == 'logo_default_dark.png') ? '/assets/demo/default/media/img/logo/logo_default_dark.png' : "/images/logo/{$logo}",
    'google_map_api_key' => env('GOOGLE_MAP_API')
];
