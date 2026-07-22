<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

// Filter kategori dari GET
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$where_kategori = '';
if ($filter_kategori !== '') {
    $filter_esc = mysqli_real_escape_string($conn, $filter_kategori);
    $where_kategori = "AND event.kategori = '$filter_esc'";
}

$query = mysqli_query($conn, "
    SELECT *
    FROM event
    WHERE id_hmj = '$id_hmj'
    $where_kategori
    ORDER BY tanggal_event ASC
");

$total = mysqli_num_rows($query);

// Daftar kategori (harus sama dengan ENUM di database)
$daftar_kategori = ['Lomba', 'Seminar', 'Open Recruitment', 'Workshop', 'Lainnya'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event - HMJ</title>
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
                <h4 class="text-align-center mb-0 fw-bold text-dark">Data Program Kerja & Event</h4>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-funnel-fill me-2 text-primary"></i>Filter Kategori</h5>
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-lg-8">
                    <label class="form-label small fw-bold text-muted">Pilih Kategori Event</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                        <?php foreach ($daftar_kategori as $kat) { ?>
                        <option value="<?= $kat; ?>" <?= ($filter_kategori == $kat) ? 'selected' : ''; ?>>
                            <?= $kat; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="background-color: var(--theme-purple); border:none;">Terapkan</button>
                    <a href="index.php" class="btn btn-outline-secondary px-3">Reset</a>
                </div>
            </form>
        </div>

        <div class="alert alert-light border card-custom p-3 mb-4 d-flex align-items-center justify-content-between">
            <span class="text-dark">
                Menampilkan <strong><?= $total; ?></strong> data kegiatan terdaftar
                <?php if ($filter_kategori !== '') { ?>
                    — Kategori: <span class="badge bg-secondary px-2.5 py-1.5 ms-1"><?= htmlspecialchars($filter_kategori); ?></span>
                <?php } ?>
            </span>
             <a href="tambah.php" class="btn btn-primary d-flex align-items-center gap-2" style="background-color: var(--theme-purple); border:none;">
                <i class="bi bi-plus-circle-fill"></i> Tambah Event Baru
            </a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width: 60px;">No</th>
                            <th class="py-3" style="width: 100px;">Poster</th>
                            <th class="py-3">Nama Event</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Lokasi / Tempat</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3 text-center">Kuota</th>
                            <th class="py-3">Biaya Masuk</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total == 0) { ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted small">Tidak ada event ditemukan dalam data.</td>
                        </tr>
                        <?php } ?>

                        <?php $no = 1; while ($data = mysqli_fetch_assoc($query)) { 
                            $status_class = 'badge-process';
                            if($data['status'] == 'Disetujui') { $status_class = 'badge-disetujui'; }
                            elseif($data['status'] == 'Ditolak') { $status_class = 'badge-ditolak'; }
                        ?>
                        <tr>
                            <td class="px-3 text-secondary fw-medium"><?= $no++; ?></td>
                            <td>
                                <?php if (!empty($data['poster'])) { ?>
                                    <img src="../../uploads/<?= htmlspecialchars($data['poster']); ?>" width="64" height="85" class="rounded object-fit-cover shadow-sm border" alt="Poster">
                                <?php } else { ?>
                                    <div class="rounded bg-light text-muted d-flex align-items-center justify-content-center border" style="width: 64px; height: 85px;">
                                        <i class="bi bi-image small"></i>
                                    </div>
                                <?php } ?>
                            </td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td><span class="badge bg-light text-dark border px-2.5 py-1.5"><?= htmlspecialchars($data['kategori']); ?></span></td>
                            <td class="text-secondary small"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($data['lokasi']); ?></td>
                            <td class="text-muted small"><i class="bi bi-calendar3 text-primary me-1"></i><?= date('d-m-Y', strtotime($data['tanggal_event'])); ?></td>
                            <td class="text-center fw-bold text-dark"><?= $data['kuota']; ?></td>
                            <td class="fw-medium text-dark">
                                <?php if ($data['biaya'] > 0) { ?>
                                    <span class="text-success fw-bold">Rp <?= number_format($data['biaya'], 0, ',', '.'); ?></span>
                                <?php } else { ?>
                                    <span class="badge bg-success-subtle text-success px-2.5 py-1.5 fw-bold">Gratis</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $status_class; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($data['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="edit.php?id=<?= $data['id_event']; ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $data['id_event']; ?>"
                                       onclick="return confirm('Yakin hapus event ini?')"
                                       class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-trash3"></i> Hapus
                                    </a>
                                </div>
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