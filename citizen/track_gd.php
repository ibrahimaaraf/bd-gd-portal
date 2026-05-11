<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('citizen');
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT * FROM general_diaries WHERE user_id = ?';
$params = [$user['id']];
if ($search !== '') {
    $sql .= ' AND (reference_no LIKE ? OR subject LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$sql .= ' ORDER BY created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$gds = $stmt->fetchAll();
$qrItems = [];

$pageTitle = 'Track GD';
$activeNav = 'track';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <h1 class="h2 mb-0">Track GD</h1>
    <form class="d-flex gap-2" method="get">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Reference or subject">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
</div>
<div class="app-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Subject</th><th>Type</th><th>Status</th><th>Submitted</th><th>Verification QR</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($gds as $gd): ?>
                <?php
                    $verifyUrl = gd_verification_url($gd['verification_token']);
                    $qrUrl = gd_qr_code_url($verifyUrl, 180);
                    $modalId = 'qrModal' . (int) $gd['id'];
                    $qrItems[] = [
                        'modal_id' => $modalId,
                        'reference_no' => $gd['reference_no'],
                        'verify_url' => $verifyUrl,
                        'qr_url' => $qrUrl,
                    ];
                ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e($gd['subject']) ?></td>
                    <td><?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#<?= e($modalId) ?>">
                            <i class="bi bi-qr-code me-1"></i>Show QR
                        </button>
                    </td>
                    <td><a class="btn btn-sm btn-primary" href="<?= e(app_url('citizen/download_gd.php?id=' . $gd['id'])) ?>">Details</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$gds): ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">No matching GD found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php foreach ($qrItems as $item): ?>
    <div class="modal fade" id="<?= e($item['modal_id']) ?>" tabindex="-1" aria-labelledby="<?= e($item['modal_id']) ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5" id="<?= e($item['modal_id']) ?>Label">Verification QR - <?= e($item['reference_no']) ?></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img class="qr-preview" src="<?= e($item['qr_url']) ?>" alt="Verification QR code for <?= e($item['reference_no']) ?>">
                    <p class="fw-semibold mt-3 mb-1">Scan this QR to verify the GD record.</p>
                    <p class="small text-secondary text-break mb-0"><?= e($item['verify_url']) ?></p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-outline-primary" href="<?= e($item['verify_url']) ?>" target="_blank">Open Verification Page</a>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
