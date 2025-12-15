<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Quick pre-bootstrap API endpoint for raw multipart upload diagnostics
if (isset($_SERVER['REQUEST_URI']) && preg_match('#^/api/upload-test#', $_SERVER['REQUEST_URI'])) {
    header('Content-Type: application/json');
    $info = [
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? null,
        'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        'files' => []
    ];
    foreach ($_FILES as $k => $f) {
        $info['files'][$k] = [
            'name' => $f['name'],
            'size' => $f['size'],
            'type' => $f['type'],
            'tmp_name' => $f['tmp_name'],
            'error' => $f['error']
        ];
    }
    echo json_encode($info);
    exit;
}

// Pre-bootstrap controller-upload for test: save uploaded files directly to storage
if (isset($_SERVER['REQUEST_URI']) && preg_match('#^/api/controller-upload#', $_SERVER['REQUEST_URI'])) {
    $targetDir = __DIR__ . '/../storage/app/public/entertainer-uploads';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $saved = [];
    foreach ($_FILES as $key => $f) {
        if ($f['error'] === UPLOAD_ERR_OK) {
            $name = basename($f['name']);
            $dest = $targetDir . '/' . uniqid() . '-' . $name;
            if (move_uploaded_file($f['tmp_name'], $dest)) {
                $saved[$key] = $dest;
            } else {
                $saved[$key] = 'move_failed';
            }
        } else {
            $saved[$key] = 'upload_error_'.$f['error'];
        }
    }
    $out = ['saved' => $saved, 'content_length' => $_SERVER['CONTENT_LENGTH'] ?? null];
    file_put_contents(__DIR__ . '/../storage/logs/controller-upload.json', json_encode($out));
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
