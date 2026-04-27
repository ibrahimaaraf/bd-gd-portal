<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

const GD_STATUSES = ['submitted', 'under_review', 'investigating', 'resolved', 'rejected'];
const GD_TYPES = ['lost_document', 'theft', 'missing_person', 'cyber_complaint', 'threat', 'other'];

function gd_status_badge(string $status): string
{
    return '<span class="badge badge-status status-' . e($status) . '">' . e(str_replace('_', ' ', $status)) . '</span>';
}

function gd_reference(): string
{
    return 'GD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

function verification_token(): string
{
    return bin2hex(random_bytes(16));
}

function gd_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT gd.*, u.name AS citizen_name, u.email AS citizen_email, u.phone AS citizen_phone FROM general_diaries gd JOIN users u ON u.id = gd.user_id WHERE gd.id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function log_gd_status(int $gdId, int $userId, string $status, string $note): void
{
    $stmt = db()->prepare('INSERT INTO gd_status_logs (gd_id, user_id, status, note) VALUES (?, ?, ?, ?)');
    $stmt->execute([$gdId, $userId, $status, $note]);
}
