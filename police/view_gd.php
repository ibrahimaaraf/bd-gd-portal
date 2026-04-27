<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('police');
$gd = gd_by_id((int) ($_GET['id'] ?? 0));
if (!$gd) {
    flash('error', 'GD not found.');
    redirect('police/manage_gd.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $newStatus = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');
    if ($gd['status'] === 'resolved') {
        flash('error', 'Resolved GD records cannot be changed.');
        redirect('police/view_gd.php?id=' . $gd['id']);
    } elseif (!in_array($newStatus, GD_STATUSES, true) || $note === '') {
        flash('error', 'Select a status and add a note.');
    } else {
        $stmt = db()->prepare('UPDATE general_diaries SET status = ?, assigned_officer_id = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$newStatus, $user['id'], $gd['id']]);
        log_gd_status((int) $gd['id'], (int) $user['id'], $newStatus, $note);
        flash('success', 'GD status updated.');
        redirect('police/view_gd.php?id=' . $gd['id']);
    }
}

$stmt = db()->prepare('SELECT l.*, u.name FROM gd_status_logs l LEFT JOIN users u ON u.id = l.user_id WHERE gd_id = ? ORDER BY created_at DESC');
$stmt->execute([$gd['id']]);
$logs = $stmt->fetchAll();

$pageTitle = 'View GD';
require __DIR__ . '/../includes/header.php';
?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="app-card">
            <div class="d-flex justify-content-between gap-3 mb-3">
                <div>
                    <div class="eyebrow"><?= e($gd['reference_no']) ?></div>
                    <h1 class="h3 mb-1"><?= e($gd['subject']) ?></h1>
                    <?= gd_status_badge($gd['status']) ?>
                </div>
            </div>
            <dl class="row">
                <dt class="col-sm-4">Citizen</dt><dd class="col-sm-8"><?= e($gd['citizen_name']) ?>, <?= e($gd['citizen_phone']) ?></dd>
                <dt class="col-sm-4">Type</dt><dd class="col-sm-8"><?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></dd>
                <dt class="col-sm-4">Incident date</dt><dd class="col-sm-8"><?= e($gd['incident_date']) ?></dd>
                <dt class="col-sm-4">Location</dt><dd class="col-sm-8"><?= e($gd['location']) ?></dd>
                <dt class="col-sm-4">Description</dt><dd class="col-sm-8"><?= nl2br(e($gd['description'])) ?></dd>
            </dl>
            <?php if ($gd['evidence_path']): ?>
                <a class="btn btn-outline-primary btn-sm" target="_blank" href="<?= e(app_url($gd['evidence_path'])) ?>">Open Evidence</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="app-card mb-4">
            <h2 class="h5 mb-3">Update status</h2>
            <?php if ($gd['status'] === 'resolved'): ?>
                <div class="alert alert-success mb-0">This GD is resolved and cannot be changed.</div>
            <?php else: ?>
                <form method="post" class="vstack gap-3">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <select class="form-select" name="status" required>
                        <?php foreach (GD_STATUSES as $status): ?>
                            <option value="<?= e($status) ?>" <?= $gd['status'] === $status ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $status))) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea class="form-control" name="note" rows="4" placeholder="Status note" required></textarea>
                    <button class="btn btn-primary">Save Update</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="app-card">
            <h2 class="h5 mb-3">History</h2>
            <?php foreach ($logs as $log): ?>
                <div class="border-bottom pb-2 mb-2">
                    <strong><?= e(ucwords(str_replace('_', ' ', $log['status']))) ?></strong>
                    <div class="small text-secondary"><?= e($log['name'] ?? 'System') ?>, <?= e(date('M d, Y h:i A', strtotime($log['created_at']))) ?></div>
                    <div><?= e($log['note']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
