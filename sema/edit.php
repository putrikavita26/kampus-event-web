<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    header("Location: ../login.php");
    exit;
}

$id = (int) $_GET['id'];

// Pastikan yang diedit adalah HMJ
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM users WHERE id_user = '$id' AND role = 'HMJ'
"));

if (!$data) {
    header("Location: kelola_hmj.php?error=tidak_ditemukan");
    exit;
}

$error  = '';
$sukses = '';

// Proses update
if (isset($_POST['update_hmj'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Cek email duplikat (kecuali milik HMJ ini sendiri)
    $cek_email = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT id_user FROM users
        WHERE email = '$email' AND id_user != '$id'
    "));

    if ($cek_email) {
        $error = "Email sudah digunakan oleh akun lain.";

    } else {
        // Update nama dan email
        mysqli_query($conn, "
            UPDATE users SET nama = '$nama', email = '$email'
            WHERE id_user = '$id' AND role = 'HMJ'
        ");

        // Update password jika diisi
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "
                UPDATE users SET password = '$password'
                WHERE id_user = '$id'
            ");
        }

        header("Location: kelola_hmj.php?sukses=diedit");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --theme-purple:    #6f42c1;
            --sidebar-gradient: linear-gradient(180deg, #4c1d95 0%, #6f42c1 60%, #d63384 100%);
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }
        @media (max-width: 991.98px) {
            .sidebar      { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 20px !important; }
        }
        @media (min-width: 992px) {
            .sidebar      { display: block !important; }
            .main-content { margin-left: 260px; padding: 40px; }
        }
        .offcanvas .sidebar {
            display: block !important;
            position: relative !important;
            width: 100% !important;
            left: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            background: #fff;
        }
        .top-navbar {
            background: #fff;
            padding: 15px 24px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-hamburger {
            background: var(--theme-purple);
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 1.1rem;
        }
        .avatar-circle {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6f42c1, #d63384);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            color: #fff;
            margin: 0 auto 16px;
        }
        .form-label { font-weight: 600; font-size: 0.875rem; color: #495057; }
        .form-control:focus, .form-select:focus {
            border-color: #b692f6;
            box-shadow: 0 0 0 0.25rem rgba(111,66,193,0.15);
        }
        .password-toggle { cursor: pointer; }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<!-- Offcanvas Mobile -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar"
     style="width:260px; background: linear-gradient(180deg,#4c1d95 0%,#6f42c1 60%,#d63384 100%);">
    <div class="offcanvas-header border-bottom border-white border-opacity-10">
        <span class="text-white fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-grid-1x2-fill text-warning"></i> DASHBOARD SEMA
        </span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php include "sidebar.php"; ?>
    </div>
</div>

<div class="main-content">

    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-hamburger d-lg-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>
            <h4 class="mb-0 fw-bold text-dark">Edit Akun HMJ</h4>
        </div>
        <a href="kelola_hmj.php" class="text-decoration-none text-muted small fw-semibold">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Kelola HMJ
        </a>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span><?= $error; ?></span>
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card card-custom p-4 p-md-5">

                <!-- Avatar -->
                <div class="text-center mb-4">
                    <div class="avatar-circle">
                        <i class="bi bi-building"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($data['nama']); ?></h5>
                </div>

                <hr class="mb-4">

                <form action="edit_hmj.php?id=<?= $id; ?>" method="POST">

                    <!-- Nama -->
                    <div class="mb-4">
                        <label class="form-label">Nama Himpunan (HMJ)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" name="nama" class="form-control"
                                   value="<?= htmlspecialchars($data['nama']); ?>"
                                   placeholder="Contoh: HMJ Informatika" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($data['email']); ?>"
                                   placeholder="hmj@kampus.ac.id" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label">
                            Password Baru
                            <span class="text-muted fw-normal">(kosongkan jika tidak ingin diubah)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" id="passwordInput"
                                   class="form-control"
                                   placeholder="Masukkan password baru..."
                                   minlength="6">
                            <button type="button"
                                    class="input-group-text bg-light password-toggle"
                                    onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Minimal 6 karakter. Biarkan kosong jika tidak ingin mengubah password.
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex gap-3 mt-4 pt-3 border-top">
                        <a href="kelola_hmj.php"
                           class="btn btn-outline-secondary px-4 fw-semibold">
                            Batal
                        </a>
                        <button type="submit" name="update_hmj"
                                class="btn px-4 fw-bold flex-grow-1"
                                style="background: linear-gradient(135deg, #6f42c1, #9d4edd); color:white; border:none; border-radius:8px;">
                            <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const input   = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type    = 'text';
        eyeIcon.className = 'bi bi-eye-slash';
    } else {
        input.type    = 'password';
        eyeIcon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>