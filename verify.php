<?php
require_once __DIR__ . '/includes/gd_helpers.php';

$token = trim($_GET['token'] ?? '');
$reference = trim($_GET['reference'] ?? '');
$query = trim($_GET['q'] ?? '');
if ($query !== '') {
    if (preg_match('/^[a-f0-9]{32}$/i', $query)) {
        $token = $query;
        $reference = '';
    } else {
        $reference = $query;
        $token = '';
    }
}
$searched = $token !== '' || $reference !== '';
$gd = $searched ? verified_gd_by_token_or_reference($token, $reference) : null;

$pageTitle = 'Verify GD';
$activeNav = 'verify';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="app-card">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <div class="eyebrow mb-2">Public verification</div>
                    <h1 class="h3 mb-1">Verify GD authenticity</h1>
                    <p class="text-secondary mb-0">Enter a GD reference number or scan the QR code printed on an official GD copy.</p>
                </div>
                <?php if ($gd): ?>
                    <i class="bi bi-shield-check verify-icon text-primary"></i>
                <?php endif; ?>
            </div>

            <form class="row g-3 mb-4" method="get">
                <div class="col-md-8">
                    <label class="form-label">Reference number or verification token</label>
                    <input class="form-control" name="q" value="<?= e($query ?: ($reference ?: $token)) ?>" placeholder="Example: GD-20260426-DEMO01">
                </div>
                <div class="col-md-4 d-grid align-items-end">
                    <button class="btn btn-primary"><i class="bi bi-search me-2"></i>Verify</button>
                </div>
            </form>

            <?php if ($searched && !$gd): ?>
                <div class="alert alert-danger mb-0">
                    No matching GD record was found. Check the reference number or scan the QR code again.
                </div>
            <?php elseif ($gd): ?>
                <div class="alert alert-success">
                    This GD record was found and verified from the portal database.
                </div>
                <div class="verification-result">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                        <div>
                            <div class="text-secondary small">Reference</div>
                            <h2 class="h4 mb-0"><?= e($gd['reference_no']) ?></h2>
                        </div>
                        <?= gd_status_badge($gd['status']) ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Citizen:</strong> <?= e($gd['citizen_name']) ?></div>
                        <div class="col-md-6"><strong>Type:</strong> <?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></div>
                        <div class="col-md-6"><strong>Incident date:</strong> <?= e($gd['incident_date']) ?></div>
                        <div class="col-md-6"><strong>Location:</strong> <?= e($gd['location']) ?></div>
                        <div class="col-md-6"><strong>Submitted:</strong> <?= e(date('M d, Y h:i A', strtotime($gd['created_at']))) ?></div>
                        <div class="col-12"><strong>Subject:</strong> <?= e($gd['subject']) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
