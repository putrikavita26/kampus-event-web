<?php
include "../config/koneksi.php";


$is_export = isset($_GET['export']) && $_GET['export'] == 'true';

if ($is_export) {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Event.xls");
}

// Logika Filter
$conditions = [];
$id_hmj = isset($_GET['id_hmj']) ? mysqli_real_escape_string($conn, $_GET['id_hmj']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Filter HMJ
if (!empty($id_hmj)) {
    $conditions[] = "event.id_hmj = '$id_hmj'";
}

// Filter Status
if (!empty($status)) {
    $conditions[] = "event.status = '$status'";
}

$where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Ambil data HMJ untuk dropdown filter
$query_hmj = mysqli_query($conn, "SELECT * FROM users WHERE role='HMJ'");

// Mengambil data dengan filter
$query = mysqli_query($conn, "
    SELECT 
        event.*, 
        users.nama AS nama_hmj 
    FROM event 
    JOIN users ON event.id_hmj = users.id_user
    $where
    ORDER BY event.tanggal_event ASC
");
?>

<?php if (!$is_export): ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Event - SEMA</title>
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
                <h4 class="mb-0 fw-bold text-dark">Laporan Rekapitulasi Event HMJ</h4>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-funnel-fill text-purple me-2" style="color: var(--theme-purple);"></i>Penyaringan Data</h5>
            <form method="GET" action="laporan.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Himpunan Mahasiswa (HMJ)</label>
                    <select name="id_hmj" class="form-select">
                        <option value="">-- Semua HMJ --</option>
                        <?php while($h = mysqli_fetch_assoc($query_hmj)): ?>
                            <option value="<?= $h['id_user']; ?>" <?= ($id_hmj == $h['id_user']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($h['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">Status Validasi</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Process" <?= ($status == 'Process') ? 'selected' : ''; ?>>Process</option>
                        <option value="Disetujui" <?= ($status == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                        <option value="Ditolak" <?= ($status == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 flex-grow-1" style="background-color: var(--theme-purple); border:none;">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="laporan.php" class="btn btn-outline-secondary px-3">Reset</a>
                </div>
            </form>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-table text-blue me-2"></i>Lembar Kerja Data Rencana Kegiatan
                </h5>
                <a href="laporan.php?export=true&id_hmj=<?= $id_hmj; ?>&status=<?= $status; ?>" class="btn btn-success d-inline-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-excel-fill"></i> Ekspor ke Excel
                </a>
            </div>
<?php endif; ?>

            <div class="table-responsive">
                <table <?= $is_export ? 'border="1" cellpadding="5"' : 'class="table table-hover align-middle mb-0"'; ?>>
                    <thead <?= !$is_export ? 'class="table-light"' : ''; ?>>
                        <tr>
                            <th <?= !$is_export ? 'class="py-3"' : ''; ?>>Nama HMJ</th>
                            <th <?= !$is_export ? 'class="py-3"' : ''; ?>>Nama Event</th>
                            <th <?= !$is_export ? 'class="py-3"' : ''; ?>>Tanggal</th>
                            <th <?= !$is_export ? 'class="py-3 text-center"' : ''; ?>>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($data = mysqli_fetch_assoc($query)){ 
                            if (!$is_export) {
                                $status_class = 'badge-process';
                                if($data['status'] == 'Disetujui') { $status_class = 'badge-disetujui'; }
                                elseif($data['status'] == 'Ditolak') { $status_class = 'badge-ditolak'; }
                            }
                        ?>
                        <tr>
                            <td <?= !$is_export ? 'class="fw-semibold text-dark"' : ''; ?>><?= htmlspecialchars($data['nama_hmj']); ?></td>
                            <td><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td>
                                <?php if (!$is_export): ?>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars($data['tanggal_event']); ?>
                                    </div>
                                <?php else: ?>
                                    <?= htmlspecialchars($data['tanggal_event']); ?>
                                <?php endif; ?>
                            </td>
                            <td <?= !$is_export ? 'class="text-center"' : ''; ?>>
                                <?php if (!$is_export): ?>
                                    <span class="badge <?= $status_class; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>
                                <?php else: ?>
                                    <?= htmlspecialchars($data['status']); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">Tidak ada rekaman data kegiatan yang sesuai dengan filter.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

<?php if (!$is_export): ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php endif; ?>