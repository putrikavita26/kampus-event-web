<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

$query = mysqli_query($conn, "
    SELECT
        pendaftaran.id_pendaftaran,
        pendaftaran.status,
        pendaftaran.tanggal_daftar,
        users.nama,
        mahasiswa.nim,
        event.nama_event,
        event.kategori
    FROM pendaftaran

    JOIN mahasiswa
    ON pendaftaran.nim = mahasiswa.nim

    JOIN users
    ON mahasiswa.id_user = users.id_user

    JOIN event
    ON pendaftaran.id_event = event.id_event

    WHERE event.id_hmj = '$id_hmj'

    ORDER BY pendaftaran.tanggal_daftar DESC
");

$total_peserta = mysqli_num_rows($query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta Event - HMJ</title>
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
                <h4 class="mb-0 fw-bold text-dark">Data Validasi Pendaftaran Peserta</h4>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-people-fill me-2 text-purple"></i>Data Pendaftar
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width: 60px;">No</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">NIM</th>
                            <th class="py-3">Event</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Waktu Daftar</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center" style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_peserta == 0) { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted small">Belum ada mahasiswa yang mendaftar ke event Anda</td>
                        </tr>
                        <?php } ?>

                        <?php $no = 1; while ($data = mysqli_fetch_assoc($query)) { 
                            $status_clean = htmlspecialchars($data['status']);
                            $badge_class = 'badge-menunggu';
                            if ($status_clean == 'Diterima') { $badge_class = 'badge-diterima'; }
                            elseif ($status_clean == 'Ditolak') { $badge_class = 'badge-ditolak'; }
                        ?>
                        <tr>
                            <td class="px-3 text-secondary fw-medium"><?= $no++; ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama']); ?></td>
                            <td class="font-monospace small text-secondary"><?= htmlspecialchars($data['nim']); ?></td>
                            <td class="text-dark fw-medium"><?= htmlspecialchars($data['nama_event']); ?></td>
                            <td><span class="badge bg-light text-dark border px-2 py-1.5"><?= htmlspecialchars($data['kategori'] ?? '-'); ?></span></td>
                            <td class="text-muted small">
                                <i class="bi bi-clock me-1"></i><?= date('d-m-Y H:i', strtotime($data['tanggal_daftar'])); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badge_class; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem; font-weight: 700;">
                                    <?= $status_clean; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="detail.php?id=<?= $data['id_pendaftaran']; ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>

                                    <?php if ($data['status'] == 'Menunggu') { ?>
                                    <a href="action.php?terima=<?= $data['id_pendaftaran']; ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin MENERIMA peserta ini?')" 
                                       class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                        <i class="bi bi-check-circle"></i> Terima
                                    </a>
                                    <a href="action.php?tolak=<?= $data['id_pendaftaran']; ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin MENOLAK peserta ini?')" 
                                       class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </a>
                                    <?php } ?>
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