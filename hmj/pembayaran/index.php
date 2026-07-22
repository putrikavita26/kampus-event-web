<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

if (isset($_GET['validasi'])) {
    $id = (int) $_GET['validasi'];

    mysqli_query($conn, "UPDATE pembayaran SET status='Valid' WHERE id_pembayaran='$id'");

    $bayar = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT id_pendaftaran FROM pembayaran WHERE id_pembayaran='$id'
    "));
    $id_pendaftaran = (int) ($bayar['id_pendaftaran'] ?? 0);

    mysqli_query($conn, "
        UPDATE pendaftaran SET status='Diterima'
        WHERE id_pendaftaran='$id_pendaftaran'
    ");

    header("Location: index.php");
    exit;
}

if (isset($_GET['tolak'])) {
    $id = (int) $_GET['tolak'];

    mysqli_query($conn, "UPDATE pembayaran SET status='Ditolak' WHERE id_pembayaran='$id'");

    $bayar = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT id_pendaftaran FROM pembayaran WHERE id_pembayaran='$id'
    "));

    mysqli_query($conn, "
        UPDATE pendaftaran SET status='Ditolak'
        WHERE id_pendaftaran='{$bayar['id_pendaftaran']}'
    ");

    header("Location: index.php");
    exit;
}

// Filter status
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$where_status  = '';
if ($filter_status !== '') {
    $fs = mysqli_real_escape_string($conn, $filter_status);
    $where_status = "AND pembayaran.status = '$fs'";
}

$query = mysqli_query($conn, "
    SELECT
        pembayaran.id_pembayaran,
        pembayaran.bukti_bayar,
        pembayaran.status,
        pembayaran.tanggal_upload,
        users.nama,
        mahasiswa.nim,
        event.nama_event,
        pendaftaran.id_pendaftaran

    FROM pembayaran

    JOIN pendaftaran
    ON pembayaran.id_pendaftaran = pendaftaran.id_pendaftaran

    JOIN mahasiswa
    ON pendaftaran.nim = mahasiswa.nim

    JOIN users
    ON mahasiswa.id_user = users.id_user

    JOIN event
    ON pendaftaran.id_event = event.id_event

    WHERE event.id_hmj = '$id_hmj'
    $where_status

    ORDER BY pembayaran.tanggal_upload DESC
");

if (!$query) {
    die(mysqli_error($conn));
}

$total_rows = mysqli_num_rows($query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembayaran Event - HMJ</title>
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
        <div class="top-navbar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <button class="btn btn-primary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Data Validasi Pembayaran Kegiatan</h4>
            </div>
        </div>

        <div class="card card-custom p-3 mb-4">
            <form method="GET" action="index.php" class="row g-3 align-items-center">
                <div class="col-12 col-sm-auto">
                    <label class="text-secondary small fw-bold d-flex align-items-center gap-1">
                        <i class="bi bi-funnel-fill text-purple"></i> Filter Status Data:
                    </label>
                </div>
                <div class="col-12 col-sm-4 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="Menunggu" <?= ($filter_status == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="Valid"    <?= ($filter_status == 'Valid')    ? 'selected' : ''; ?>>Valid</option>
                        <option value="Ditolak"  <?= ($filter_status == 'Ditolak')  ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-12 col-sm-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-medium" style="background-color: var(--theme-purple); border:none;">
                        Terapkan
                    </button>
                    <a href="index.php" class="btn btn-sm btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="card card-custom p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-wallet2 text-purple me-2"></i>Daftar Unggahan Pembayaran
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width: 50px;">No</th>
                            <th class="py-3">NIM</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">Event</th>
                            <th class="py-3">Waktu Unggah</th>
                            <th class="py-3 text-center">Bukti Transaksi</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_rows == 0) { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted small">
                                <i class="bi bi-inbox me-1"></i> Tidak ada catatan data pembayaran yang sesuai kriteria
                            </td>
                        </tr>
                        <?php } ?>

                        <?php $no = 1; while ($data = mysqli_fetch_assoc($query)) { 
                            $status_clean = htmlspecialchars($data['status']);
                            $badge_class = 'badge-menunggu';
                            if ($status_clean == 'Valid') { $badge_class = 'badge-valid'; }
                            elseif ($status_clean == 'Ditolak') { $badge_class = 'badge-ditolak'; }
                        ?>
                        <tr>
                            <td class="px-3 text-secondary fw-medium"><?= $no++; ?></td>
                            <td class="font-monospace small text-secondary"><?= htmlspecialchars($data['nim']); ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama']); ?></td>
                            <td class="text-dark fw-medium"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td class="text-muted small">
                                <i class="bi bi-clock me-1"></i><?= date('d-m-Y H:i', strtotime($data['tanggal_upload'])); ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($data['bukti_bayar'])) { ?>
                                    <a href="../../uploads/<?= htmlspecialchars($data['bukti_bayar']); ?>"
                                       class="btn btn-xs btn-outline-primary py-1 px-2.5 d-inline-flex align-items-center gap-1 text-xs rounded-pill"
                                       target="_blank" style="font-size: 0.8rem;">
                                        <i class="bi bi-file-image"></i> Lihat Bukti
                                    </a>
                                <?php } else { ?>
                                    <span class="text-muted small"><i>-</i></span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badge_class; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem; font-weight: 700;">
                                    <?= $status_clean; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($data['status'] == 'Menunggu') { ?>
                                    <div class="d-inline-flex gap-1">
                                        <a href="index.php?validasi=<?= $data['id_pembayaran']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin memvalidasi pembayaran ini?')" 
                                           class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                            <i class="bi bi-check-lg"></i> Valid
                                        </a>
                                        <a href="index.php?tolak=<?= $data['id_pembayaran']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menolak berkas pembayaran ini?')" 
                                           class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                            <i class="bi bi-x"></i> Tolak
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <span class="text-muted small">Selesai diperiksa</span>
                                <?php } ?>
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