<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

// Filter dari GET
$filter_status   = isset($_GET['status']) ? $_GET['status'] : '';
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$daftar_kategori = ['Lomba', 'Seminar', 'Open Recruitment', 'Workshop', 'Lainnya'];

// WHERE tambahan
$where_status   = '';
$where_kategori = '';

if ($filter_status !== '') {
    $s = mysqli_real_escape_string($conn, $filter_status);
    $where_status = "AND pendaftaran.status = '$s'";
}

if ($filter_kategori !== '') {
    $k = mysqli_real_escape_string($conn, $filter_kategori);
    $where_kategori = "AND event.kategori = '$k'";
}

// Query utama peserta 
$query = mysqli_query($conn, "
    SELECT
        pendaftaran.id_pendaftaran,
        users.nama,
        mahasiswa.nim,
        mahasiswa.angkatan,
        mahasiswa.kelas,
        mahasiswa.no_hp,
        prodi.nama_prodi,
        event.nama_event,
        pendaftaran.status,
        pendaftaran.tanggal_daftar

    FROM pendaftaran

    JOIN mahasiswa  ON pendaftaran.nim      = mahasiswa.nim
    JOIN users      ON mahasiswa.id_user    = users.id_user
    LEFT JOIN prodi ON users.id_prodi       = prodi.id_prodi
    JOIN event      ON pendaftaran.id_event = event.id_event

    WHERE event.id_hmj = '$id_hmj'
    $where_status
    $where_kategori

    ORDER BY pendaftaran.tanggal_daftar DESC
");

// Kumpulkan semua jawaban ke array [id_pendaftaran][id_form] = isi
$semua_id = [];
$rows = [];
while ($r = mysqli_fetch_assoc($query)) {
    $rows[] = $r;
    $semua_id[] = (int) $r['id_pendaftaran'];
}

$jawaban_map = []; // [id_pendaftaran][id_form] = isi

if (!empty($semua_id)) {
    $ids_str = implode(',', $semua_id);
    $query_jawaban = mysqli_query($conn, "
        SELECT
            jf.id_pendaftaran,
            jf.id_form,
            jf.jawaban,
            jf.file_upload,
            fe.tipe_input
        FROM jawaban_form jf
        JOIN form_event fe ON jf.id_form = fe.id_form
        WHERE jf.id_pendaftaran IN ($ids_str)
    ");
    while ($j = mysqli_fetch_assoc($query_jawaban)) {
        $jawaban_map[$j['id_pendaftaran']][$j['id_form']] = $j;
    }
}

$total = count($rows);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Peserta Kegiatan - HMJ</title>
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
                <h4 class="mb-0 fw-bold text-dark">Rekapitulasi Data Peserta</h4>
            </div>
            
            <a href="export_excel.php?status=<?= urlencode($filter_status); ?>&kategori=<?= urlencode($filter_kategori); ?>" 
               class="btn btn-sm btn-success d-flex align-items-center gap-2 px-3 py-2 fw-medium shadow-sm">
                <i class="bi bi-file-earmark-excel-fill fs-6"></i> Export ke Excel
            </a>
        </div>

        <div class="card card-custom p-3 mb-4">
            <form method="GET" action="index.php" class="row g-3 align-items-center">
                <div class="col-12 col-md-4 row g-2 align-items-center">
                    <div class="col-sm-4">
                        <label class="text-secondary small fw-bold mb-0">Kategori Event:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="kategori" class="form-select form-select-sm">
                            <option value="">-- Semua Kategori --</option>
                            <?php foreach ($daftar_kategori as $kat) { ?>
                            <option value="<?= htmlspecialchars($kat); ?>" <?= ($filter_kategori == $kat) ? 'selected' : ''; ?> >
                                <?= htmlspecialchars($kat); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-4 row g-2 align-items-center">
                    <div class="col-sm-4">
                        <label class="text-secondary small fw-bold mb-0">Filter Status:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="Menunggu" <?= ($filter_status == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="Diterima" <?= ($filter_status == 'Diterima') ? 'selected' : ''; ?>>Diterima</option>
                            <option value="Ditolak"  <?= ($filter_status == 'Ditolak')  ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2 justify-content-md-end">
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-medium" style="background-color: var(--theme-purple); border:none;">
                        <i class="bi bi-funnel"></i> Filter Data
                    </button>
                    <a href="index.php" class="btn btn-sm btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-database-fill-check text-purple me-2"></i>Daftar Pengajuan Berkas Peserta
                </h5>
                <span class="text-muted small">
                    Menampilkan <strong class="text-dark"><?= $total; ?></strong> data record peserta ditemukan
                </span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width: 50px;">No</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">NIM</th>
                            <th class="py-3">Prodi</th>
                            <th class="py-3" style="width: 80px;">Kelas</th>
                            <th class="py-3" style="width: 90px;">Angkatan</th>
                            <th class="py-3">Event</th>
                            <th class="py-3 text-center" style="width: 120px;">Status</th>
                            <th class="py-3">Waktu Daftar</th>
                            <th class="py-3" style="min-width: 180px;">Alasan</th>
                            <th class="py-3">Kontak HP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total == 0) { ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted small">
                                <i class="bi bi-folder-x me-1"></i> Tidak ada data pengajuan berkas peserta yang terintegrasi saat ini
                            </td>
                        </tr>
                        <?php } ?>

                        <?php $no = 1; foreach ($rows as $data) { 
                            $status_clean = htmlspecialchars($data['status']);
                            $badge_class = 'badge-menunggu';
                            if ($status_clean == 'Diterima') { $badge_class = 'badge-diterima'; }
                            elseif ($status_clean == 'Ditolak') { $badge_class = 'badge-ditolak'; }
                        ?>
                        <tr>
                            <td class="px-3 text-secondary fw-medium"><?= $no++; ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama']); ?></td>
                            <td class="font-monospace small text-secondary"><?= htmlspecialchars($data['nim']); ?></td>
                            <td class="text-dark small"><?= htmlspecialchars($data['nama_prodi'] ?? '-'); ?></td>
                            <td class="text-secondary"><?= htmlspecialchars($data['kelas']); ?></td>
                            <td class="text-secondary text-center"><?= htmlspecialchars($data['angkatan']); ?></td>
                            <td class="text-dark fw-medium"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td class="text-center">
                                <span class="badge <?= $badge_class; ?> px-2.5 py-1.5 rounded-pill text-uppercase" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">
                                    <?= $status_clean; ?>
                                </span>
                            </td>
                            <td class="text-muted small" style="white-space: nowrap;">
                                <i class="bi bi-clock me-1"></i><?= date('d-m-Y H:i', strtotime($data['tanggal_daftar'])); ?>
                            </td>
                            
                            <td>
                                <?php
                                    $id_pend = $data['id_pendaftaran'];
                                    $has_reason = false;

                                    if (isset($jawaban_map[$id_pend])) {
                                        foreach ($jawaban_map[$id_pend] as $j) {
                                            // HANYA menampilkan data jika tipe kustomnya bertipe 'textarea' dan isinya tidak kosong
                                            if ($j['tipe_input'] == 'textarea' && !empty($j['jawaban'])) {
                                                $has_reason = true;
                                                ?>
                                                <div class="p-2 text-dark mb-1" style="font-size: 0.85rem; max-width: 250px; word-wrap: break-word;">
                                                    <?= htmlspecialchars($j['jawaban']); ?>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    
                                    // Jika tidak ditemukan komponen alasan (textarea), tampilkan strip (-)
                                    if (!$has_reason) {
                                        echo '<span class="text-muted italic">-</span>';
                                    }
                                ?>
                            </td>
                            
                            <td class="text-secondary small font-monospace"><?= htmlspecialchars($data['no_hp']); ?></td>
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