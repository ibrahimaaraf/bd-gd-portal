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

$sql = 'SELECT reference_no, gd_type, subject, status, incident_date, created_at FROM general_diaries WHERE ';
$params = [];
if ($token !== '') {
    $sql .= 'verification_token = ?';
    $params[] = $token;
} else {
    $sql .= 'reference_no = ?';
    $params[] = $reference;
}
$stmt = db()->prepare($sql . ' LIMIT 1');
$stmt->execute($params);
$gd = $stmt->fetch();

if (!$gd) {
    http_response_code(404);
    echo json_encode(['verified' => false, 'message' => 'GD record not found.']);
    exit;
}

echo json_encode(['verified' => true, 'gd' => $gd], JSON_PRETTY_PRINT);
