<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    header("Location: ../login.php");
    exit;
}

// Tambah HMJ
if (isset($_POST['tambah_hmj'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "
        INSERT INTO users (nama, email, password, role)
        VALUES ('$nama', '$email', '$password', 'HMJ')
    ");
    header("Location: kelola_hmj.php?sukses=ditambah");
    exit;
}

// Hapus HMJ
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);

    $cek_event = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) as total FROM event WHERE id_hmj = '$id'
    "));

    if ($cek_event['total'] > 0) {
        header("Location: kelola_hmj.php?error=masih_punya_event");
        exit;
    }

    mysqli_query($conn, "DELETE FROM users WHERE id_user='$id' AND role='HMJ'");
    header("Location: kelola_hmj.php?sukses=terhapus");
    exit;
}


// Ambil Data HMJ
$query = mysqli_query($conn, "SELECT * FROM users WHERE role='HMJ' ORDER BY id_user ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --theme-purple: #6f42c1;
            --theme-blue: #0d6efd;
            --theme-pink: #d63384;
            --sidebar-gradient: linear-gradient(180deg, #4c1d95 0%, #6f42c1 60%, #d63384 100%);
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        /* Layout Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            width: 260px;
            background: var(--sidebar-gradient);
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar-brand {
            padding: 24px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 24px;
            font-weight: 500;
            margin: 4px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.2);
            box-shadow: inset 3px 0 0 #fff;
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }
        /* Main Workspace */
        .main-content {
            margin-left: 260px;
            padding: 40px;
        }
        .top-navbar {
            background: #fff;
            padding: 15px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 30px;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            background: #fff;
        }
        .btn-custom-purple {
            background-color: var(--theme-purple);
            color: white;
            border: none;
        }
        .btn-custom-purple:hover {
            background-color: #59359a;
            color: white;
        }

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
            <h5 class="offcanvas-title fw-bold">MENU SEMA</h5>
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
            <h4 class="mb-0 fw-bold text-dark">Kelola Data Akun HMJ</h4>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'masih_punya_event'): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>HMJ ini masih memiliki event terdaftar dan <strong>tidak bisa dihapus</strong>. Hapus event HMJ terlebih dahulu.</span>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['sukses'])): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <span>
                <?php if ($_GET['sukses'] == 'terhapus'): ?>
                    Akun HMJ berhasil dihapus.
                <?php elseif ($_GET['sukses'] == 'ditambah'): ?>
                    Akun HMJ baru berhasil ditambahkan.
                <?php elseif ($_GET['sukses'] == 'diedit'): ?>
                    Akun HMJ berhasil diperbarui.
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['sukses'])): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <span>
                <?php if ($_GET['sukses'] == 'terhapus'): ?>
                    Akun HMJ berhasil dihapus.
                <?php elseif ($_GET['sukses'] == 'ditambah'): ?>
                    Akun HMJ baru berhasil ditambahkan.
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Form Tambah HMJ -->
            <div class="col-12 col-xl-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-person-plus-fill me-2" style="color: var(--theme-pink);"></i>Tambah HMJ Baru
                    </h5>
                    <form action="kelola_hmj.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama HMJ</label>
                            <input type="text" name="nama" class="form-control"
                                   placeholder="Contoh: HMJ Informatika" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Alamat Email</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Contoh: hmj@kampus.ac.id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" name="tambah_hmj"
                                    class="btn py-2 fw-semibold"
                                    style="background-color: var(--theme-purple); color: white;">
                                <i class="bi bi-save me-1"></i> Simpan Akun HMJ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar HMJ -->
            <div class="col-12 col-xl-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-4">
                        <i class="bi bi-shield-shaded text-primary me-2"></i>Daftar Pengurus HMJ Aktif
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Nama HMJ</th>
                                    <th class="py-3">Email Aktif</th>
                                    <th class="py-3 text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query) == 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">
                                        <i class="bi bi-inbox me-1"></i>
                                        Belum ada data HMJ yang terdaftar.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width:32px;height:32px;background:rgba(111,66,193,0.1);color:var(--theme-purple);">
                                                <i class="bi bi-building"></i>
                                            </div>
                                            <span class="fw-semibold text-dark">
                                                <?= htmlspecialchars($data['nama']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        <?= htmlspecialchars($data['email']); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">

                                            <!-- Tombol Edit — tambahkan ini -->
                                            <a href="edit.php?id=<?= $data['id_user']; ?>"
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>


                                            <!-- Tombol Hapus — sudah ada -->
                                            <a href="kelola_hmj.php?hapus=<?= $data['id_user']; ?>"
                                            onclick="return confirm('Yakin ingin menghapus akun HMJ ini? Pastikan tidak ada event aktif.')"
                                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-trash3"></i> Hapus
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>