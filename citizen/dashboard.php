<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('citizen');

$stmt = db()->prepare('SELECT status, COUNT(*) total FROM general_diaries WHERE user_id = ? GROUP BY status');
$stmt->execute([$user['id']]);
$counts = array_fill_keys(GD_STATUSES, 0);
foreach ($stmt->fetchAll() as $row) {
    $counts[$row['status']] = (int) $row['total'];
}
$stmt = db()->prepare('SELECT COUNT(*) FROM general_diaries WHERE user_id = ?');
$stmt->execute([$user['id']]);
$counts['submitted'] = (int) $stmt->fetchColumn();

$stmt = db()->prepare('SELECT * FROM general_diaries WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$user['id']]);
$recent = $stmt->fetchAll();

$pageTitle = 'Citizen Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <div class="eyebrow">Citizen</div>
        <h1 class="h2 mb-0">Dashboard</h1>
    </div>
    <a class="btn btn-primary align-self-md-center" href="<?= e(app_url('citizen/submit_gd.php')) ?>"><i class="bi bi-plus-circle me-2"></i>Submit New GD</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($counts as $status => $total): ?>
        <div class="col-6 col-lg">
            <div class="stat-card">
                <div class="stat-value"><?= $total ?></div>
                <div class="text-secondary text-capitalize"><?= e(str_replace('_', ' ', $status)) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="app-card mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <div class="eyebrow">How to submit a GD</div>
            <h2 class="h4 mb-1">Use this portal to file and track your General Diary</h2>
            <p class="text-secondary mb-0">Keep your NID number, active phone number, incident details, and any supporting document ready before you start.</p>
        </div>
        <a class="btn btn-primary align-self-lg-center" href="<?= e(app_url('citizen/submit_gd.php')) ?>"><i class="bi bi-send me-2"></i>Start GD</a>
    </div>
    <div class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="guide-step">
                <div class="guide-step-icon"><i class="bi bi-person-vcard"></i></div>
                <h3 class="h6">1. Confirm your identity</h3>
                <p>Register or log in with your citizen account. Your NID number is used to identify the applicant.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="guide-step">
                <div class="guide-step-icon"><i class="bi bi-ui-checks"></i></div>
                <h3 class="h6">2. Fill the GD form</h3>
                <p>Select the GD type, incident date, location, subject, and write a clear description of what happened.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="guide-step">
                <div class="guide-step-icon"><i class="bi bi-paperclip"></i></div>
                <h3 class="h6">3. Add evidence</h3>
                <p>Attach a JPG, PNG, or PDF if you have related documents, screenshots, photos, or other proof.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="guide-step">
                <div class="guide-step-icon"><i class="bi bi-shield-check"></i></div>
                <h3 class="h6">4. Track and verify</h3>
                <p>After submission, track the status from this dashboard and download the GD copy with QR verification.</p>
            </div>
        </div>
    </div>
    <div class="alert alert-info mt-3 mb-0">
        If the incident is a serious criminal matter or requires urgent police action, contact the nearest police station directly.
    </div>
</div>
<div class="app-card table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Recent GDs</h2>
        <a class="link-primary" href="<?= e(app_url('citizen/track_gd.php')) ?>">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Type</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recent as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e(str_replace('_', ' ', $gd['gd_type'])) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('citizen/download_gd.php?id=' . $gd['id'])) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recent): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">No GD submitted yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
