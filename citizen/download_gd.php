<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('citizen');
$gd = gd_by_id((int) ($_GET['id'] ?? 0));
if (!$gd || (int) $gd['user_id'] !== (int) $user['id']) {
    flash('error', 'GD not found.');
    redirect('citizen/track_gd.php');
}

$stmt = db()->prepare('SELECT l.*, u.name FROM gd_status_logs l LEFT JOIN users u ON u.id = l.user_id WHERE gd_id = ? ORDER BY created_at ASC');
$stmt->execute([$gd['id']]);
$logs = $stmt->fetchAll();

$pageTitle = 'GD ' . $gd['reference_no'];
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h1 class="h3 mb-0">GD Details</h1>
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print / Save PDF</button>
</div>
<div class="app-card">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <div class="eyebrow">Official GD Copy</div>
            <h2 class="h4 mb-1"><?= e($gd['reference_no']) ?></h2>
            <?= gd_status_badge($gd['status']) ?>
        </div>
        <div class="qr-box">
            Verify:<br><?= e($gd['verification_token']) ?>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6"><strong>Citizen:</strong> <?= e($gd['citizen_name']) ?></div>
        <div class="col-md-6"><strong>Phone:</strong> <?= e($gd['citizen_phone']) ?></div>
        <div class="col-md-6"><strong>Type:</strong> <?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></div>
        <div class="col-md-6"><strong>Incident date:</strong> <?= e($gd['incident_date']) ?></div>
        <div class="col-md-6"><strong>Location:</strong> <?= e($gd['location']) ?></div>
        <div class="col-md-6"><strong>Submitted:</strong> <?= e(date('M d, Y h:i A', strtotime($gd['created_at']))) ?></div>
    </div>
    <h3 class="h5">Subject</h3>
    <p><?= e($gd['subject']) ?></p>
    <h3 class="h5">Description</h3>
    <p class="text-prewrap"><?= nl2br(e($gd['description'])) ?></p>
    <?php if ($gd['evidence_path']): ?>
        <p class="no-print"><a class="btn btn-outline-primary btn-sm" href="<?= e(app_url($gd['evidence_path'])) ?>" target="_blank">View Evidence</a></p>
    <?php endif; ?>
    <hr>
    <h3 class="h5">Status history</h3>
    <ul class="list-group list-group-flush">
        <?php foreach ($logs as $log): ?>
            <li class="list-group-item px-0">
                <strong><?= e(ucwords(str_replace('_', ' ', $log['status']))) ?></strong>
                <span class="text-secondary small">by <?= e($log['name'] ?? 'System') ?> on <?= e(date('M d, Y h:i A', strtotime($log['created_at']))) ?></span>
                <div><?= e($log['note']) ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
