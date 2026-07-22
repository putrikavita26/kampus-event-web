<?php

session_start();
include "../config/koneksi.php";


if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    header("Location: ../login.php");
    exit;
}


if (isset($_GET['setuju'])) {
    $id = mysqli_real_escape_string($conn, $_GET['setuju']);
    mysqli_query($conn, "UPDATE event SET status='Disetujui' WHERE id_event='$id'");
    header("Location: verifikasi.php");
    exit;
}

if (isset($_GET['tolak'])) {
    $id = mysqli_real_escape_string($conn, $_GET['tolak']);
    mysqli_query($conn, "UPDATE event SET status='Ditolak' WHERE id_event='$id'");
    header("Location: verifikasi.php");
    exit;
}


$query = mysqli_query($conn, "
    SELECT 
        event.*, 
        users.nama AS nama_hmj 
    FROM event 
    JOIN users ON event.id_hmj = users.id_user
    ORDER BY id_event DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
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
        .badge-process { background-color: rgba(253, 126, 20, 0.15); color: #fd7e14; }
        .badge-disetujui { background-color: rgba(25, 135, 84, 0.15); color: #198754; }
        .badge-ditolak { background-color: rgba(220, 53, 69, 0.15); color: #dc3545; }

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
            <div class="d-flex align-items-center">
                <h4 class=" mb-0 fw-bold text-dark">Persetujuan Event</h4>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-patch-check-fill text-info me-2"></i>Daftar Antrean Pengajuan Event
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">HMJ</th>
                            <th class="py-3">Nama Kegiatan</th>
                            <th class="py-3">Tanggal Event</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Detail</th>
                            <th class="py-3 text-center" style="width: 200px;">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($data = mysqli_fetch_assoc($query)){ 
                            $status_class = 'badge-process';
                            if($data['status'] == 'Disetujui') { $status_class = 'badge-disetujui'; }
                            elseif($data['status'] == 'Ditolak') { $status_class = 'badge-ditolak'; }
                        ?>
                        <tr>
                            <td><span class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_hmj']); ?></span></td>
                            <td class="text-dark"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    <?= htmlspecialchars($data['tanggal_event']); ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $status_class; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($data['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="detail_event.php?id=<?= $data['id_event']; ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                            <td class="text-center">
                                <?php if($data['status'] == 'Process'): ?>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="verifikasi.php?setuju=<?= $data['id_event']; ?>" class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                            <i class="bi bi-check-lg"></i> Setuju
                                        </a>
                                        <a href="verifikasi.php?tolak=<?= $data['id_event']; ?>" onclick="return confirm('Yakin menolak event ini?')" class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i>Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>