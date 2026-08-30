<?php

/**
 * DomPDF Configuration
 *
 * Font cache uses /tmp on Vercel (read-only filesystem)
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Protocols
    |--------------------------------------------------------------------------
    */
    'protocol' => env('DOMPDF_PROTOCOL', 'file'),
    'enable_remote' => true,

    /*
    |--------------------------------------------------------------------------
    | Helper Settings
    |--------------------------------------------------------------------------
    */
    'root' => '',
    'chroot' => realpath(base_path('/')),
    'is_font_subsetting_enabled' => false,
    'isHtml5ParserEnabled' => true,
    'is_php_enabled' => false,
    'is_font_enabled' => true,
    'is_remote_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Font Settings
    |--------------------------------------------------------------------------
    */
    'font_dir' => env('DOMPDF_FONT_DIR', storage_path('fonts')),
    'font_cache' => env('DOMPDF_FONT_CACHE', function_exists('sys_get_temp_dir')
        ? sys_get_temp_dir() . '/dompdf_fonts'
        : storage_path('fonts')),
    'font_height_ratio' => 1.05,
    'default_font' => 'sans-serif',

    /*
    |--------------------------------------------------------------------------
    | Font Families
    |--------------------------------------------------------------------------
    */
    'font_families' => [
        'sans-serif' => [
            'normal' => null,
            'bold' => null,
            'italic' => null,
            'bold_italic' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    */
    'image_cache' => function_exists('sys_get_temp_dir')
        ? sys_get_temp_dir() . '/dompdf_images'
        : storage_path('app'),
    'is_javascript_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Parser Settings
    |--------------------------------------------------------------------------
    */
    'is_parser_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Render Settings
    |--------------------------------------------------------------------------
    */
    'debugLayout' => false,
    'debugKeepInlines' => false,
    'debugLayoutInline' => false,
    'debugLayoutBlock' => false,
    'debugLayoutInlineBlock' => false,

    /*
    |--------------------------------------------------------------------------
    | Context Options
    |--------------------------------------------------------------------------
    */
    'context' => [
        'compress' => 1,
        'fontDir' => [
            'real' => env('DOMPDF_FONT_DIR', storage_path('fonts')),
        ],
        'fontCache' => [
            'real' => env('DOMPDF_FONT_CACHE', function_exists('sys_get_temp_dir')
                ? sys_get_temp_dir() . '/dompdf_fonts'
                : storage_path('fonts')),
        ],
        'tmp' => [
            'real' => function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : sys_get_temp_dir(),
            'wm' => 0600,
        ],
        'logOutputFile' => 'php://stderr',
        'logLevel' => 1,
    ],

];
