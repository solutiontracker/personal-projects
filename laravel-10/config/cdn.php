<?php
return [
    'cdn_enabled'   => true,
    'cdn_domain'    => env('CDN_URL', 'http://eventbuizz.local'),
    'cdn_upload_path'    => env('CDN_UPLOAD_PATH', ''),
    'cdn_protocol'  => env('CDN_PROTOCOL', 'http'),
];
