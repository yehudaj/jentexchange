<?php
header('Content-Type: application/json');
$info = [
    'content_length' => isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : null,
    'method' => $_SERVER['REQUEST_METHOD'] ?? null,
    'files' => []
];
foreach ($_FILES as $key => $f) {
    $info['files'][$key] = [
        'name' => $f['name'],
        'size' => $f['size'],
        'type' => $f['type'],
        'tmp_name' => $f['tmp_name'],
        'error' => $f['error']
    ];
}
echo json_encode($info);
