<?php
session_start();
include "../config/koneksi.php";

// Pengecekan role Mahasiswa
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'MAHASISWA') {
    header("Location: ../login.php");
    exit;
}

// Jika tidak ada ID di URL, kembalikan ke dashboard
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_event = (int) $_GET['id'];

// Query untuk mengambil detail event
$event = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        event.*, 
        users.nama AS nama_hmj, 
        prodi.nama_prodi,
        COUNT(pendaftaran.id_pendaftaran) AS terpakai 
    FROM event 
    JOIN users ON event.id_hmj = users.id_user 
    LEFT JOIN prodi ON event.id_prodi = prodi.id_prodi
    LEFT JOIN pendaftaran 
        ON event.id_event = pendaftaran.id_event 
        AND pendaftaran.status IN ('Menunggu', 'Diterima')
    WHERE event.id_event = '$id_event' 
    AND event.status = 'Disetujui' 
    GROUP BY event.id_event
"));

// Jika event tidak ada atau ID salah
if (!$event) {
    echo "<script>alert('Event tidak ditemukan!'); window.location.href='dashboard.php';</script>";
    exit;
}

$sisa = $event['kuota'] - $event['terpakai'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['nama_event']); ?> - KampusEvent</title>

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
        
        .btn-purple { 
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500)); 
            color: white; 
            border: none; 
            font-weight: 700; 
            border-radius: var(--radius-sm);
            padding: 14px 20px;
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-purple:hover { 
            opacity: 0.88; 
            color: white; 
            transform: translateY(-1px); 
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
                    <!-- Class active diset di Utama karena diakses dari Dashboard -->
                    <a class="nav-link nav-pill active" href="dashboard.php">
                        <i class="fa-solid fa-house me-1"></i>Utama
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-pill" href="event_saya.php">
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
            <i class="fa-solid fa-circle-info me-2"></i>Detail Event
        </div>
        <div class="text-white-50 mt-2" style="font-size: 0.95rem;">
            Informasi lengkap mengenai kegiatan yang akan kamu ikuti.
        </div>
    </div>
</section>


<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="page-card">
                <!-- Tombol Kembali diletakkan dengan rapi di dalam kotak -->
                <div class="mb-4 pb-3 border-bottom">
                    <a href="dashboard.php" class="text-decoration-none fw-semibold text-muted">
                        <i class="fa-solid fa-arrow-left-long me-2"></i> Kembali ke Dashboard
                    </a>
                </div>

                <div class="row g-5">
                    <!-- Sisi Kiri: Poster Event -->
                    <div class="col-md-5">
                        <div class="rounded-4 overflow-hidden shadow-sm bg-light border">
                            <?php if (!empty($event['poster'])) { ?>
                                <img src="../uploads/<?= htmlspecialchars($event['poster']); ?>" class="img-fluid w-100" style="object-fit: cover;" alt="Poster Event">
                            <?php } else { ?>
                                <div class="d-flex align-items-center justify-content-center text-muted" style="height: 400px;">
                                    <i class="fa-regular fa-image fa-4x"></i>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Rincian Event -->
                    <div class="col-md-7">
                        <span class="badge px-3 py-2 rounded-pill mb-3" style="background-color: var(--purple-100); color: var(--purple-700);">
                            <?= htmlspecialchars($event['kategori']); ?>
                        </span>
                        
                        <h2 class="fw-bold text-dark mb-4"><?= htmlspecialchars($event['nama_event']); ?></h2>

                        <div class="meta-list-item">
                            <div class="meta-icon-wrapper"><i class="fa-solid fa-building-user"></i></div>
                            <div>
                                <small class="text-muted d-block">Penyelenggara</small>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($event['nama_hmj']); ?></span>
                            </div>
                        </div>

                        <div class="meta-list-item">
                            <div class="meta-icon-wrapper"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <small class="text-muted d-block">Lokasi</small>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($event['lokasi']); ?></span>
                            </div>
                        </div>

                        <div class="meta-list-item">
                            <div class="meta-icon-wrapper"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div>
                                <small class="text-muted d-block">Target Peserta</small>
                                <span class="fw-bold text-dark">
                                    <?php 
                                        if (!empty($event['nama_prodi'])) {
                                            echo "Khusus Prodi " . htmlspecialchars($event['nama_prodi']);
                                        } else {
                                            echo '<span class="badge bg-success">Terbuka untuk Umum</span>';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="meta-list-item">
                            <div class="meta-icon-wrapper"><i class="fa-solid fa-calendar-day"></i></div>
                            <div>
                                <small class="text-muted d-block">Tanggal Pelaksanaan</small>
                                <span class="fw-bold text-dark"><?= date('d F Y', strtotime($event['tanggal_event'])); ?></span>
                            </div>
                        </div>

                        <div class="meta-list-item">
                            <div class="meta-icon-wrapper"><i class="fa-solid fa-money-bill-wave"></i></div>
                            <div>
                                <small class="text-muted d-block">Biaya Pendaftaran</small>
                                <span class="fw-bold fs-5 text-dark">
                                    <?php 
                                        if ($event['biaya'] > 0) {
                                            echo "Rp " . number_format($event['biaya'], 0, ',', '.');
                                        } else {
                                            echo '<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Gratis</span>';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-top">
                            <h6 class="fw-bold text-dark">Deskripsi Kegiatan</h6>
                            <p class="text-muted small" style="line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($event['deskripsi'])); ?>
                            </p>
                        </div>

                        <div class="mt-4">
                            <?php if ($sisa > 0) { ?>
                                <a href="daftar.php?id=<?= $event['id_event']; ?>" class="btn btn-purple btn-lg w-100 py-3 rounded-3 shadow-sm d-flex justify-content-center align-items-center">
                                    <i class="fa-solid fa-pen-to-square me-2"></i>Daftar Sekarang
                                </a>
                            <?php } else { ?>
                                <button class="btn btn-secondary btn-lg w-100 py-3 rounded-3" disabled>
                                    <i class="fa-solid fa-user-slash me-2"></i>Kuota Penuh
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>