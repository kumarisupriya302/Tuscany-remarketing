<?php
$candidates = [
    __DIR__ . '/tuscany.jpg',
    'C:/Users/Supriya/.cursor/projects/c-xampp-htdocs-Tuscany-remarketing/assets/tuscany-bg.jpg',
];

foreach ($candidates as $path) {
    if (is_file($path)) {
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=86400');
        readfile($path);
        exit;
    }
}

http_response_code(404);
echo 'Photo not found';
