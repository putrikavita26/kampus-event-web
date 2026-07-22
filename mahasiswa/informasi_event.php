<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../config/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: event_saya.php");
    exit;
}

$id_pendaftaran = (int) $_GET['id'];

$query = mysqli_query($conn, "
    SELECT
        pendaftaran.id_pendaftaran,
        pendaftaran.status AS status_pendaftaran,
        pendaftaran.tanggal_daftar,
        event.nama_event,
        event.lokasi,
        event.tanggal_event,
        event.biaya,
        event.link_grup,
        event.informasi_peserta,
        pembayaran.status AS status_pembayaran,
        pembayaran.bukti_bayar,
        mahasiswa.nim,
        users.nama AS nama_mahasiswa
    FROM pendaftaran
    JOIN mahasiswa ON pendaftaran.nim = mahasiswa.nim
    JOIN users ON mahasiswa.id_user = users.id_user
    JOIN event ON pendaftaran.id_event = event.id_event
    LEFT JOIN (
        SELECT * FROM pembayaran
        WHERE id_pembayaran IN (
            SELECT MAX(id_pembayaran) FROM pembayaran GROUP BY id_pendaftaran
        )
    ) pembayaran ON pendaftaran.id_pendaftaran = pembayaran.id_pendaftaran
    WHERE pendaftaran.id_pendaftaran = '$id_pendaftaran'
    AND mahasiswa.id_user = '" . $_SESSION['id_user'] . "'
");

if (!$query || mysqli_num_rows($query) === 0) {
    header("Location: event_saya.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

$biaya        = (float) $data['biaya'];
$status_daftar = $data['status_pendaftaran'];
$status_bayar  = $data['status_pembayaran'];

// Tentukan kondisi
$is_gratis        = $biaya == 0;
$is_diterima      = $status_daftar === 'Diterima';
$is_bayar_valid   = $status_bayar === 'Valid';
$is_bayar_ditolak = $status_bayar === 'Ditolak';
$belum_upload     = empty($status_bayar);

// Boleh lihat informasi penuh jika:
// - Gratis + Diterima, ATAU
// - Berbayar + Diterima + pembayaran Valid
$boleh_info_penuh = $is_diterima && ($is_gratis || $is_bayar_valid);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Event - KampusEvent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --purple-50:  #faf5ff;
            --purple-100: #f3e8ff;
            --purple-200: #e9d5ff;
            --purple-500: #a855f7;
            --purple-600: #9333ea;
            --purple-700: #7e22ce;
            --purple-800: #6b21a8;
            --purple-900: #581c87;
            --soft-bg:    #f8f5ff;
            --card-bg:    #ffffff;
            --text-dark:  #1a1a2e;
            --text-muted: #64748b;
            --border:     #ede9f6;
            --radius-lg:  20px;
            --radius-md:  14px;
            --radius-sm:  10px;
            --shadow-sm:  0 2px 8px rgba(139,92,246,0.06);
            --shadow-md:  0 8px 24px rgba(139,92,246,0.10);
            --shadow-lg:  0 20px 48px rgba(139,92,246,0.14);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-bg);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--purple-800) 0%, var(--purple-600) 100%);
            box-shadow: 0 4px 24px rgba(109,40,217,0.18);
            padding: 14px 0;
        }
        .navbar-brand-logo {
            font-size: 1.4rem;
            font-weight: 900;
            letter-spacing: -1px;
            color: #fff;
            text-decoration: none;
        }
        .navbar-brand-logo span { color: #d8b4fe; }
        .nav-pill {
            font-weight: 600;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.8) !important;
            padding: 8px 16px !important;
            border-radius: 50px !important;
            transition: all 0.2s;
        }
        .nav-pill:hover, .nav-pill.active {
            color: #fff !important;
            background: rgba(255,255,255,0.15) !important;
        }
        .btn-logout {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 8px 18px;
            border-radius: 50px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,0.3);
            color: #fff;
        }

      
        .hero-section {
            background: linear-gradient(135deg, var(--purple-800) 0%, var(--purple-600) 60%, #c026d3 100%);
            padding: 40px 0 100px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 320px; height: 320px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -40px;
            width: 240px; height: 240px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .hero-greeting {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .page-card { 
            background: var(--card-bg); 
            border-radius: var(--radius-lg); 
            border: 1px solid var(--border); 
            box-shadow: var(--shadow-lg); 
            padding: 40px; 
            margin-top: -60px; 
            position: relative;
            z-index: 10;
        }
        
        .meta-icon-wrapper { 
            width: 42px; 
            height: 42px; 
            background-color: var(--purple-100); 
            color: var(--purple-600); 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0; 
            font-size: 1.1rem; 
        }
        .meta-list-item { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            margin-bottom: 18px; 
        }
        .info-box { 
            background: #f8f6fc; 
            border-left: 4px solid var(--purple-600); 
            border-radius: 0 12px 12px 0; 
            padding: 20px; 
        }
        .btn-purple { 
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500)); 
            color: white; 
            border: none; 
            font-weight: 700; 
            border-radius: var(--radius-sm);
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-purple:hover { 
            opacity: 0.88; 
            color: white; 
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand navbar-brand-logo" href="dashboard.php">
            Kampus<span>Event</span>
        </a>
        <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-pill" href="dashboard.php">
                        <i class="fa-solid fa-house me-1"></i>Utama
                    </a>
                </li>
                <li class="nav-item">
                    <!-- Class active diset di Riwayat Event -->
                    <a class="nav-link nav-pill active" href="event_saya.php">
                        <i class="fa-solid fa-calendar-check me-1"></i>Riwayat Event
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-pill" href="profil.php">
                        <i class="fa-solid fa-user-gear me-1"></i>Profil
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn-logout" href="../logout.php">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center">
    <div class="container">
        <div class="hero-greeting">
            <i class="fa-solid fa-clipboard-check me-2"></i>Status Pendaftaran
        </div>
        <div class="text-white-50 mt-2" style="font-size: 0.95rem;">
            Informasi status kelulusan dan instruksi peserta event.
        </div>
    </div>
</section>


<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="page-card">

                <div class="mb-4 pb-3 border-bottom">
                    <a href="event_saya.php" class="text-decoration-none fw-semibold text-muted">
                        <i class="fa-solid fa-arrow-left-long me-2"></i> Kembali ke Riwayat Event
                    </a>
                </div>

                <!-- Header Event -->
                <div class="mb-4 pb-3 border-bottom">
                    <h3 class="fw-bold text-dark mb-1">
                        <?= htmlspecialchars($data['nama_event']); ?>
                    </h3>
                    <p class="text-muted small mb-0">
                        Terdaftar sejak <?= date('d F Y', strtotime($data['tanggal_daftar'])); ?>
                    </p>
                </div>

                <!-- Info Peserta -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <small class="text-muted d-block mb-1">Nama Peserta</small>
                        <p class="fw-bold text-dark mb-0">
                            <?= htmlspecialchars($data['nama_mahasiswa']); ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">NIM</small>
                        <p class="fw-bold text-dark mb-0 font-monospace">
                            <?= htmlspecialchars($data['nim']); ?>
                        </p>
                    </div>
                </div>

                <!-- Detail Event -->
                <div class="meta-list-item">
                    <div class="meta-icon-wrapper"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <small class="text-muted d-block">Lokasi</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($data['lokasi'] ?? '-'); ?></span>
                    </div>
                </div>

                <div class="meta-list-item">
                    <div class="meta-icon-wrapper"><i class="fa-solid fa-calendar-day"></i></div>
                    <div>
                        <small class="text-muted d-block">Tanggal Pelaksanaan</small>
                        <span class="fw-semibold text-dark"><?= date('d F Y', strtotime($data['tanggal_event'])); ?></span>
                    </div>
                </div>

                <div class="meta-list-item">
                    <div class="meta-icon-wrapper"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div>
                        <small class="text-muted d-block">Biaya</small>
                        <span class="fw-semibold text-dark">
                            <?= $biaya > 0 ? 'Rp ' . number_format($biaya, 0, ',', '.') : 'Gratis'; ?>
                        </span>
                    </div>
                </div>

                <hr class="my-4" style="border-color: var(--border);">

                <?php if ($boleh_info_penuh): ?>
                    <!-- KONDISI 1: Diterima + (Gratis ATAU Bayar Valid) -->
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-check fs-4"></i>
                            <div>
                                <strong>Selamat! Pendaftaranmu telah diterima.</strong>
                                <p class="mb-0 small mt-1">
                                    <?= $is_gratis
                                        ? 'Event ini gratis. Simak informasi penting di bawah ini.'
                                        : 'Pembayaranmu telah terverifikasi. Simak informasi penting di bawah ini.'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-circle-info me-2" style="color: var(--purple-600);"></i>
                            Informasi Peserta
                        </h5>
                        <div class="info-box">
                            <?php if (!empty($data['informasi_peserta'])): ?>
                                <?= nl2br(htmlspecialchars($data['informasi_peserta'])); ?>
                            <?php else: ?>
                                <em class="text-muted">Belum ada informasi tambahan dari penyelenggara.</em>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($data['link_grup'])): ?>
                    <div class="d-grid">
                        <a href="<?= htmlspecialchars($data['link_grup']); ?>"
                           target="_blank"
                           class="btn btn-purple btn-lg rounded-3 py-3">
                            <i class="fa-brands fa-whatsapp me-2"></i>
                            Gabung Grup WhatsApp Peserta
                        </a>
                    </div>
                    <?php endif; ?>

                <?php elseif ($is_diterima && !$is_gratis && $belum_upload): ?>
                    <!-- KONDISI 2: Diterima tapi belum upload bukti bayar -->
                    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-check fs-4"></i>
                            <div>
                                <strong>Pendaftaranmu telah diterima!</strong>
                                <p class="mb-0 small mt-1">
                                    Segera upload bukti pembayaran agar informasi peserta bisa ditampilkan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="pembayaran.php?id=<?= $data['id_pendaftaran']; ?>"
                           class="btn btn-warning btn-lg rounded-3 py-3 fw-bold text-dark">
                            <i class="fa-solid fa-upload me-2"></i>
                            Upload Bukti Pembayaran Sekarang
                        </a>
                    </div>

                <?php elseif ($is_diterima && !$is_gratis && $is_bayar_ditolak): ?>
                    <!-- KONDISI 3: Diterima tapi bukti bayar ditolak -->
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-xmark fs-4"></i>
                            <div>
                                <strong>Bukti pembayaran ditolak.</strong>
                                <p class="mb-0 small mt-1">
                                    Silakan upload ulang bukti pembayaran yang valid.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="pembayaran.php?id=<?= $data['id_pendaftaran']; ?>"
                           class="btn btn-danger btn-lg rounded-3 py-3 fw-bold">
                            <i class="fa-solid fa-upload me-2"></i>
                            Upload Ulang Bukti Pembayaran
                        </a>
                    </div>

                <?php else: ?>
                    <!-- KONDISI 4: Masih menunggu proses -->
                    <div class="alert alert-warning border-0 shadow-sm rounded-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-hourglass-half fs-4"></i>
                            <div>
                                <strong>Pendaftaran sedang diproses.</strong>
                                <p class="mb-0 small mt-1">
                                    Informasi akan muncul setelah pendaftaran dinyatakan diterima
                                    <?= !$is_gratis ? 'dan pembayaran telah diverifikasi.' : '.'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <span class="badge bg-secondary p-2 rounded-pill px-3">
                            Seleksi: <?= htmlspecialchars($status_daftar); ?>
                        </span>
                        <?php if (!$is_gratis): ?>
                        <span class="badge bg-secondary p-2 rounded-pill px-3">
                            Pembayaran: <?= htmlspecialchars($status_bayar ?: 'Belum upload bukti'); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>