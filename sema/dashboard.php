<?php

session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    header("Location: ../login.php");
    exit;
}
$stats = [];
$queries = [
    'total_event' => "SELECT COUNT(*) as total FROM event",
    'total_process' => "SELECT COUNT(*) as total FROM event WHERE status='Process'",
    'total_disetujui' => "SELECT COUNT(*) as total FROM event WHERE status='Disetujui'",
    'total_ditolak' => "SELECT COUNT(*) as total FROM event WHERE status='Ditolak'"
];

foreach ($queries as $key => $sql) {
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $stats[$key] = $data['total'];
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_search = '';
if (!empty($search)) {
    $where_search = "WHERE nama_event LIKE '%$search%' OR status LIKE '%$search%'";
}

$query_terbaru = mysqli_query($conn, "
    SELECT 
        event.nama_event, 
        event.status,
        users.nama AS hmj_nama
    FROM event
    LEFT JOIN users ON event.id_hmj = users.id_user
    $where_search 
    ORDER BY event.id_event DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --theme-purple: #9333ea;
            --theme-blue: #0d6efd;
            --theme-pink: #c026d3;
            --sidebar-gradient: linear-gradient(135deg, #6b21a8 0%, #9333ea 60%, #c026d3 100%);
        }

        body {
            background-color: #f8f5ff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .card-custom {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.08);
            background: #fff;
        }

        .stat-card {
            border: none !important;
            border-radius: 12px !important;
            color: #fff;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .form-control, .form-select, .btn {
            border-radius: 6px !important;
        }

        .bg-gradient-blue { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
        .bg-gradient-purple { background: linear-gradient(135deg, #6f42c1 0%, #59359a 100%); }
        .bg-gradient-pink { background: linear-gradient(135deg, #d63384 0%, #b11f6b 100%); }
        .bg-gradient-orange { background: linear-gradient(135deg, #fd7e14 0%, #e0650d 100%); }
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
            <h4 class="mb-0 fw-bold text-dark">Sistem Informasi SEMA</h4>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <span class="text-muted d-block small">Selamat Datang,</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(255, 193, 7, 0.15);">
                    <i class="bi bi-grid-1x2-fill text-warning fs-5"></i>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stat-card bg-gradient-blue p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">TOTAL EVENT</p>
                            <h2 class="mb-0 text-white fw-bold"><?= $stats['total_event']; ?></h2>
                        </div>
                        <i class="bi bi-calendar4-event fs-1 text-white opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stat-card bg-gradient-orange p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">PROSES VERIFIKASI</p>
                            <h2 class="mb-0 text-white fw-bold"><?= $stats['total_process']; ?></h2>
                        </div>
                        <i class="bi bi-hourglass-split text-white fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stat-card bg-gradient-purple p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">DISETUJUI</p>
                            <h2 class="mb-0 text-white fw-bold"><?= $stats['total_disetujui']; ?></h2>
                        </div>
                        <i class="bi bi-check-circle-fill text-white fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card stat-card bg-gradient-pink p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">DITOLAK</p>
                            <h2 class="mb-0 text-white fw-bold"><?= $stats['total_ditolak']; ?></h2>
                        </div>
                        <i class="bi bi-x-circle-fill text-white fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-search me-2 text-primary"></i>Cari Event</h5>
            <form method="GET" action="dashboard.php" class="row g-2">
                <div class="col-sm-9 col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama event atau status..." value="<?= htmlspecialchars($search); ?>">
                </div>
                <div class="col-sm-3 col-md-2 d-grid gap-2 d-flex">
                    <button type="submit" class="btn btn-primary px-4 w-100" style="background-color: var(--theme-purple); border:none;">Cari</button>
                    <?php if (!empty($search)) { ?>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
                    <?php } ?>
                </div>
            </form>
        </div>

        <div class="card card-custom p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-clock-history me-2 text-danger"></i>Event Terbaru<?= !empty($search) ? ' (Hasil Pencarian)' : ''; ?>
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">Nama Event</th>
                            <th class="py-3">HMJ Penyelenggara</th>
                            <th class="py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query_terbaru)) { 
                            $status_class = 'badge-process';
                            if($row['status'] == 'Disetujui') { $status_class = 'badge-disetujui'; }
                            elseif($row['status'] == 'Ditolak') { $status_class = 'badge-ditolak'; }
                        ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_event']); ?></td>
                            <td><span class="text-muted"><i class="bi bi-building me-1"></i><?= htmlspecialchars($row['hmj_nama'] ?? '-'); ?></span></td>
                            <td class="text-center">
                                <span class="badge <?= $status_class; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
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