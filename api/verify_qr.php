<?php
require_once __DIR__ . '/../includes/gd_helpers.php';

$token = trim($_GET['token'] ?? '');
$reference = trim($_GET['reference'] ?? '');

header('Content-Type: application/json');

if ($token === '' && $reference === '') {
    http_response_code(422);
    echo json_encode(['verified' => false, 'message' => 'Provide token or reference.']);
    exit;
}

$gd = verified_gd_by_token_or_reference($token, $reference);

if (!$gd) {
    http_response_code(404);
    echo json_encode(['verified' => false, 'message' => 'GD record not found.']);
    exit;
}

echo json_encode(['verified' => true, 'gd' => $gd], JSON_PRETTY_PRINT);
