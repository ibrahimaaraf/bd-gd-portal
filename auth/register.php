<?php
require_once __DIR__ . '/../includes/auth.php';
if (current_user()) {
    redirect(dashboard_path(current_user()['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $nidNumber = preg_replace('/\D+/', '', $_POST['nid_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        flash('error', 'Provide a valid name, email, and a password of at least 6 characters.');
    } elseif (!in_array(strlen($nidNumber), [10, 13, 17], true)) {
        flash('error', 'Provide a valid NID number with 10, 13, or 17 digits.');
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, phone, nid_number, address, password_hash, role) VALUES (?, ?, ?, ?, ?, ?, "citizen")');
            $stmt->execute([$name, $email, $phone, $nidNumber, $address, password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'Registration complete. Please login.');
            redirect('auth/login.php');
        } catch (PDOException $e) {
            flash('error', 'That email or NID number is already registered.');
        }
    }
}

$pageTitle = 'Register';
$activeNav = 'register';
require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="app-card">
            <h1 class="h3 mb-3">Create citizen account</h1>
            <form method="post" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input class="form-control" name="name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" name="phone">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NID number</label>
                    <input class="form-control" name="nid_number" inputmode="numeric" pattern="[0-9]{10}|[0-9]{13}|[0-9]{17}" maxlength="17" required>
                    <div class="form-text">Use 10, 13, or 17 digits.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" minlength="6" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="3"></textarea>
                </div>
                <div class="col-12 d-grid">
                    <button class="btn btn-primary btn-lg">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
