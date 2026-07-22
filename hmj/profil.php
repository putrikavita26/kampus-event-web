<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'HMJ') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'HMJ') {
    header("Location: ../login.php");
    exit;
}

include "../config/koneksi.php"; 

$id_user = $_SESSION['id_user'];

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user, nama, email, role, foto FROM users WHERE id_user = '$id_user'"));

if (!$user) {
    die('Data HMJ tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil HMJ - Event Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <style>
         @media (max-width: 991.98px) {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 20px !important; }
        }
        @media (min-width: 992px) {
            .sidebar { display: block !important; }
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
    </style>
</head>
<body>
<?php include "sidebar.php"; ?>
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" style="width: 260px; background: #4c1d95;">
        <div class="offcanvas-header text-white">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <?php include "sidebar.php"; ?>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <button class="btn btn-primary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Pengaturan Akun</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="card card-custom p-4 p-md-5">
                    
                    <div class="border-bottom pb-3 mb-4">
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-person-gear text-purple me-2"></i>Profil Organisasi HMJ
                        </h5>
                    </div>

                    <form action="../process/profil_process.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="row align-items-center mb-4 g-3">
                            <div class="col-12 col-sm-auto text-center text-sm-start">
                                <?php if (!empty($user['foto'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($user['foto']); ?>" class="avatar-preview" alt="Foto Profil">
                                <?php else: ?>
                                    <div class="avatar-placeholder mx-auto mx-sm-0">
                                        <i class="bi bi-building"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-sm">
                                <label class="form-label fw-bold text-secondary small mb-1">Foto Avatar Profil</label>
                                <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="form-control form-control-sm shadow-sm" style="max-width: 350px;">
                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                    Tipe gambar: **JPG, JPEG, PNG** (Maksimal berkas 2MB). Kosongkan jika tidak ingin diubah.
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold text-secondary small mb-1">Nama HMJ</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold text-secondary small mb-1">Alamat Email</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small mb-1">Role</label>
                                <div class="input-group" style="max-width: 250px;">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-lock"></i></span>
                                    <input type="text" class="form-control bg-light text-secondary fw-semibold font-monospace" value="<?= htmlspecialchars($user['role']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 d-flex justify-content-start">
                            <button type="submit" name="update_profil" class="btn btn-purple px-4 py-2.5 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> Simpan Perubahan Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>