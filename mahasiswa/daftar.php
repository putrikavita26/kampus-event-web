<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_event = (int) $_GET['id'];

// Ambil data event
$event = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM event
    WHERE id_event = '$id_event'
    AND   status   = 'Disetujui'
"));

if (!$event) {
    die("Event tidak ditemukan.");
}

// Cek data mahasiswa
$mhs = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT nim FROM mahasiswa
    WHERE id_user = '" . $_SESSION['id_user'] . "'
"));

// Cek apakah sudah pernah daftar
$cek = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id_pendaftaran, status FROM pendaftaran
    WHERE id_event = '$id_event'
    AND   nim      = '" . $mhs['nim'] . "'
"));

// Cek kuota
$terpakai = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS jml FROM pendaftaran
    WHERE id_event = '$id_event'
    AND   status IN ('Menunggu', 'Diterima')
"));

$is_full = ($terpakai['jml'] >= $event['kuota']);

// Ambil pertanyaan form
$form = mysqli_query($conn, "
    SELECT * FROM form_event
    WHERE id_event = '$id_event'
    ORDER BY id_form ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran - <?= htmlspecialchars($event['nama_event']); ?></title>
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
            min-height: 100vh; 
            color: var(--text-dark);
        }

        /* navbar */
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

        /* hero section */
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

        /* page card */
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
        .info-box { 
            background: #fcfaff; 
            padding: 20px; 
            border-radius: 12px; 
            border: 1px solid #ede7f6; 
            margin-bottom: 25px; 
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
            <i class="fa-solid fa-pen-to-square me-2"></i>Formulir Pendaftaran
        </div>
        <div class="text-white-50 mt-2" style="font-size: 0.95rem;">
            Silakan lengkapi data diri untuk mengikuti event ini.
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="page-card">
                <div class="mb-4 pb-3 border-bottom">
                    <a href="dashboard.php" class="text-decoration-none fw-semibold text-muted">
                        <i class="fa-solid fa-arrow-left-long me-2"></i> Batal & Kembali
                    </a>
                </div>

                <h3 class="fw-bold text-dark mb-4 text-center"><?= htmlspecialchars($event['nama_event']); ?></h3>

                <div class="info-box">
                    <div class="row g-3 text-dark">
                        <div class="col-md-4"><i class="fa-solid fa-location-dot text-primary me-2"></i> <?= htmlspecialchars($event['lokasi']); ?></div>
                        <div class="col-md-4"><i class="fa-solid fa-calendar text-primary me-2"></i> <?= date('d-m-Y', strtotime($event['tanggal_event'])); ?></div>
                        <div class="col-md-4"><i class="fa-solid fa-money-bill text-primary me-2"></i> <?= $event['biaya'] > 0 ? 'Rp '.number_format($event['biaya']) : 'Gratis'; ?></div>
                    </div>
                </div>

                <?php if ($cek) : ?>
                    <div class="alert alert-info border-0 shadow-sm text-center py-4 rounded-3">
                        <i class="fa-solid fa-circle-check fa-3x mb-3 text-info"></i>
                        <h5>Anda Sudah Terdaftar!</h5>
                        <p class="mb-3">Status pendaftaran kamu saat ini: <strong><?= $cek['status']; ?></strong></p>
                        <a href="event_saya.php" class="btn btn-purple">Lihat Riwayat Event</a>
                    </div>
                <?php elseif ($is_full) : ?>
                    <div class="alert alert-warning border-0 shadow-sm text-center py-4 rounded-3">
                        <i class="fa-solid fa-users-slash fa-3x mb-3 text-warning"></i>
                        <h5>Maaf, Kuota Penuh</h5>
                        <p class="mb-0">Kuota peserta untuk event ini sudah terpenuhi.</p>
                    </div>
                <?php else : ?>
                    <form action="simpan.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_event" value="<?= $id_event; ?>">
                        
                        <?php if ($event['biaya'] > 0) { ?>
                            <div class="mb-4 bg-light p-3 rounded-3 border">
                                <label class="fw-bold mb-2 text-dark"><i class="fa-solid fa-receipt me-2 text-success"></i>Bukti Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="bukti_bayar" required>
                                <small class="text-muted mt-1 d-block">Upload bukti transfer (JPG/PNG/PDF, maks 2MB)</small>
                            </div>
                        <?php } ?>

                        <hr class="my-4">
                        <h6 class="fw-bold text-dark mb-3">Isi Formulir Berikut:</h6>

                        <?php $file_counter = 0; while ($f = mysqli_fetch_assoc($form)) { ?>
                            <div class="mb-3">
                                <label class="fw-semibold mb-2 text-dark"><?= htmlspecialchars($f['pertanyaan']); ?> <span class="text-danger">*</span></label>
                                <input type="hidden" name="id_form[]" value="<?= $f['id_form']; ?>">
                                <input type="hidden" name="tipe_input[]" value="<?= $f['tipe_input']; ?>">
                                
                                <?php if ($f['tipe_input'] == 'text') { ?>
                                    <input type="text" name="jawaban[]" class="form-control bg-light" required>
                                <?php } elseif ($f['tipe_input'] == 'textarea') { ?>
                                    <textarea name="jawaban[]" class="form-control bg-light" rows="3" required></textarea>
                                <?php } else { ?>
                                    <input type="file" name="file_<?= $file_counter++; ?>" class="form-control bg-light" required>
                                    <input type="hidden" name="jawaban[]" value="">
                                    <small class="text-muted">Format menyesuaikan (maks 2MB-5MB)</small>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        
                        <button type="submit" class="btn btn-purple w-100 py-3 mt-4 fs-6">Kirim Pendaftaran Sekarang</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>