<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$hmj_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM users WHERE id_user = '$id_hmj'"));

$where_search = '';
if (!empty($search)) {
    $where_search = "AND (event.nama_event LIKE '%$search%' OR event.status LIKE '%$search%')";
}

$total_event = mysqli_num_rows(
    mysqli_query($conn, "SELECT id_event FROM event WHERE id_hmj = '$id_hmj'")
);

$total_disetujui = mysqli_num_rows(
    mysqli_query($conn, "SELECT id_event FROM event WHERE id_hmj = '$id_hmj' AND status = 'Disetujui'")
);

$total_process = mysqli_num_rows(
    mysqli_query($conn, "SELECT id_event FROM event WHERE id_hmj = '$id_hmj' AND status = 'Process'")
);

$total_ditolak_event = mysqli_num_rows(
    mysqli_query($conn, "SELECT id_event FROM event WHERE id_hmj = '$id_hmj' AND status = 'Ditolak'")
);

/* Peserta menunggu persetujuan */
$total_peserta_menunggu = mysqli_num_rows(
    mysqli_query($conn, "
        SELECT pendaftaran.id_pendaftaran 
        FROM pendaftaran
        INNER JOIN event ON pendaftaran.id_event = event.id_event
        WHERE event.id_hmj = '$id_hmj' 
        AND event.status = 'Disetujui'
        AND pendaftaran.status = 'Menunggu'
    ")
);

/* Event disetujui + kuota sisa */
$event = mysqli_query($conn, "
    SELECT
        event.*,
        COUNT(pendaftaran.id_pendaftaran) AS terpakai,
        COALESCE(SUM(CASE WHEN pendaftaran.status = 'Menunggu' THEN 1 ELSE 0 END), 0) AS menunggu
    FROM event
    LEFT JOIN pendaftaran
        ON event.id_event = pendaftaran.id_event
        AND pendaftaran.status IN ('Menunggu', 'Diterima')
    WHERE event.id_hmj = '$id_hmj'
    AND event.status = 'Disetujui'
    $where_search
    GROUP BY event.id_event
    ORDER BY event.tanggal_event ASC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HMJ - Portal Event FST</title>
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
            <h4 class="mb-0 fw-bold text-dark">Portal Manajemen Kegiatan</h4>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <span class="text-muted d-block small">Selamat Datang,</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
                <?php if (!empty($hmj_user['foto'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($hmj_user['foto']); ?>" width="45" height="45" alt="Foto Profil" class="rounded-circle border border-2 border-primary" style="object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(255, 193, 7, 0.15);">
                        <i class="bi bi-building-fill text-warning fs-5"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card bg-gradient-blue p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">TOTAL EVENT</p>
                            <h2 class="mb-0 fw-bold"><?= $total_event; ?></h2>
                        </div>
                        <i class="bi bi-collection fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card bg-gradient-purple p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">DISETUJUI SEMA</p>
                            <h2 class="mb-0 fw-bold"><?= $total_disetujui; ?></h2>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card bg-gradient-orange p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">MENUNGGU</p>
                            <h2 class="mb-0 fw-bold"><?= $total_process; ?></h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card bg-gradient-pink p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-50 small fw-bold">EVENT DITOLAK</p>
                            <h2 class="mb-0 fw-bold"><?= $total_ditolak_event; ?></h2>
                        </div>
                        <i class="bi bi-exclamation-octagon-fill fs-1 opacity-50"></i>
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
                <i class="bi bi-bookmark-star-fill me-2 text-warning"></i>Event Aktif<?= !empty($search) ? ' (Hasil Pencarian)' : ''; ?>
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width: 60px;">No</th>
                            <th class="py-3">Nama Event</th>
                            <th class="py-3">Tanggal Pelaksanaan</th>
                            <th class="py-3">Lokasi / Tempat</th>
                            <th class="py-3 text-center">Pendaftar</th>
                            <th class="py-3 text-center text-primary">Menunggu Validasi</th>
                            <th class="py-3 text-center">Sisa Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($data = mysqli_fetch_assoc($event)) {
                            $sisa = $data['kuota'] - $data['terpakai'];
                        ?>
                        <tr>
                            <td class="px-3 text-secondary fw-medium"><?= $no++; ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td>
                                <span class="small text-muted">
                                    <i class="bi bi-calendar3 text-primary me-1"></i>
                                    <?= date('d-m-Y', strtotime($data['tanggal_event'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="small text-dark">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <?= htmlspecialchars($data['lokasi']); ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold text-secondary"><?= $data['terpakai']; ?></td>
                            <td class="text-center">
                                <?php if($data['menunggu'] > 0): ?>
                                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fw-bold">
                                        <?= $data['menunggu']; ?> 
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($sisa > 0) { ?>
                                    <span class="badge badge-sisa px-3 py-2 rounded-pill fw-semibold">
                                        <?= $sisa; ?> 
                                    </span>
                                <?php } else { ?>
                                    <span class="badge badge-penuh px-3 py-2 rounded-pill fw-bold">
                                        Penuh
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($event) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">Tidak ada data event disetujui yang tersedia.</td>
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