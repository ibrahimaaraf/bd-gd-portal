<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('citizen');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $hasError = false;
    $type = $_POST['gd_type'] ?? '';
    $incidentDate = $_POST['incident_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $evidencePath = null;

    if (!in_array($type, GD_TYPES, true) || $incidentDate === '' || $location === '' || $subject === '' || $description === '') {
        flash('error', 'Please complete all required fields.');
        $hasError = true;
    } else {
        if (!empty($_FILES['evidence']['name'])) {
            $maxBytes = ((int) env_value('UPLOAD_MAX_MB', '5')) * 1024 * 1024;
            $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
            if ($_FILES['evidence']['error'] !== UPLOAD_ERR_OK || $_FILES['evidence']['size'] > $maxBytes || !in_array(mime_content_type($_FILES['evidence']['tmp_name']), $allowed, true)) {
                flash('error', 'Evidence must be a JPG, PNG, or PDF within the upload limit.');
                $hasError = true;
            } else {
                $ext = pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION);
                $fileName = gd_reference() . '.' . strtolower($ext);
                $target = base_path('assets/uploads/evidence/' . $fileName);
                if (move_uploaded_file($_FILES['evidence']['tmp_name'], $target)) {
                    $evidencePath = 'assets/uploads/evidence/' . $fileName;
                }
            }
        }

        if (!$hasError) {
            $reference = gd_reference();
            $token = verification_token();
            $stmt = db()->prepare('INSERT INTO general_diaries (user_id, reference_no, verification_token, gd_type, subject, description, incident_date, location, evidence_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$user['id'], $reference, $token, $type, $subject, $description, $incidentDate, $location, $evidencePath]);
            log_gd_status((int) db()->lastInsertId(), (int) $user['id'], 'submitted', 'GD submitted by citizen.');
            flash('success', 'Your GD has been submitted. Reference: ' . $reference);
            redirect('citizen/track_gd.php');
        }
    }
}

$pageTitle = 'Submit GD';
$activeNav = 'submit';
require __DIR__ . '/../includes/header.php';
?>
<div class="app-card">
    <h1 class="h3 mb-3">Submit General Diary</h1>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="col-md-6">
            <label class="form-label">GD type</label>
            <select class="form-select" name="gd_type" required>
                <option value="">Select type</option>
                <?php foreach (GD_TYPES as $type): ?>
                    <option value="<?= e($type) ?>"><?= e(ucwords(str_replace('_', ' ', $type))) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Incident date</label>
            <input class="form-control" type="date" name="incident_date" max="<?= e(date('Y-m-d')) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Subject</label>
            <input class="form-control" name="subject" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Location</label>
            <input class="form-control" name="location" required>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="6" required></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Evidence file</label>
            <input class="form-control custom-file-input" type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf">
            <div class="selected-file-name small text-secondary mt-1"></div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary"><i class="bi bi-send me-2"></i>Submit GD</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
