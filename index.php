<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($uri, '/');

if ($path === '') {
    include 'home.html';
    exit;
}

$file = $path . '.html';

if (file_exists($file)) {
    include $file;
    exit;
}

http_response_code(404);
include '404.html';
