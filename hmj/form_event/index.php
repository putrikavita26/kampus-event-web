<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

$query = mysqli_query($conn,"
SELECT *
FROM event
WHERE id_hmj='$id_hmj'
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Event - HMJ</title>
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
<?php include "../sidebar.php"; ?>
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" style="width: 260px; background: #4c1d95;">
        <div class="offcanvas-header text-white">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <?php include "../sidebar.php"; ?>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <button class="btn btn-primary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Form Pendaftaran Event</h4>
            </div>
        </div>

        <div class="alert alert-light border card-custom p-3 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-purple fs-5"></i>
            <span class="text-secondary small">
                Silakan pilih event di bawah ini untuk mengelola, menambah, atau menyesuaikan field formulir pendaftaran khusus bagi calon peserta
            </span>
        </div>

        <div class="card card-custom p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-file-earmark-text-fill me-2 text-purple"></i>Daftar Formulir Kegiatan
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4" style="width: 80px;">No</th>
                            <th class="py-3">Nama Event</th>
                            <th class="py-3 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($data=mysqli_fetch_assoc($query)){ ?>
                        <tr>
                            <td class="px-4 text-secondary fw-medium"><?= $no++; ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td class="text-center">
                                <a href="tambah.php?id=<?= $data['id_event']; ?>" class="btn btn-sm btn-outline-purple d-inline-flex align-items-center gap-1 fw-semibold" style="color: var(--theme-purple); border-color: var(--theme-purple);">
                                    <i class="bi bi-sliders"></i> Kelola Form
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted small">Belum ada kegiatan yang terdaftar</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>