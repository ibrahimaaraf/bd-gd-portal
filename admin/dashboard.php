<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $gdId = (int) ($_POST['gd_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';

    if ($gdId <= 0 || !in_array($newStatus, GD_STATUSES, true)) {
        flash('error', 'Select a valid GD and status.');
    } else {
        $stmt = db()->prepare('SELECT status FROM general_diaries WHERE id = ? LIMIT 1');
        $stmt->execute([$gdId]);
        $currentStatus = $stmt->fetchColumn();

        if (!$currentStatus) {
            flash('error', 'GD not found.');
        } elseif ($currentStatus === $newStatus) {
            flash('success', 'Status is already up to date.');
        } else {
            $stmt = db()->prepare('UPDATE general_diaries SET status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$newStatus, $gdId]);
            log_gd_status($gdId, (int) $user['id'], $newStatus, 'Status updated by admin.');
            flash('success', 'GD status updated.');
        }
    }

    redirect('admin/dashboard.php');
}

$totalUsers = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalGds = (int) db()->query('SELECT COUNT(*) FROM general_diaries')->fetchColumn();
$resolved = (int) db()->query("SELECT COUNT(*) FROM general_diaries WHERE status = 'resolved'")->fetchColumn();
$pending = (int) db()->query("SELECT COUNT(*) FROM general_diaries WHERE status IN ('submitted','under_review','investigating')")->fetchColumn();
$latest = db()->query('SELECT gd.*, u.name citizen_name FROM general_diaries gd JOIN users u ON u.id = gd.user_id ORDER BY gd.created_at DESC LIMIT 6')->fetchAll();

$pageTitle = 'Admin Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <div class="eyebrow">Administrator</div>
        <h1 class="h2 mb-0">Dashboard</h1>
    </div>
    <a class="btn btn-primary align-self-md-center" href="<?= e(app_url('admin/analytics.php')) ?>"><i class="bi bi-bar-chart me-2"></i>Analytics</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= $totalUsers ?></div><div class="text-secondary">Users</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= $totalGds ?></div><div class="text-secondary">Total GDs</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= $pending ?></div><div class="text-secondary">Pending</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-value"><?= $resolved ?></div><div class="text-secondary">Resolved</div></div></div>
</div>
<div class="app-card table-card">
    <h2 class="h5 mb-3">Latest GD records</h2>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Citizen</th><th>Subject</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
            <tbody>
            <?php foreach ($latest as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e($gd['citizen_name']) ?></td>
                    <td><?= e($gd['subject']) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                    <td>
                        <form class="d-flex flex-column flex-md-row gap-2" method="post">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="gd_id" value="<?= e((string) $gd['id']) ?>">
                            <select class="form-select form-select-sm" name="status" required>
                                <?php foreach (GD_STATUSES as $status): ?>
                                    <option value="<?= e($status) ?>" <?= $gd['status'] === $status ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $status))) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-primary">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
