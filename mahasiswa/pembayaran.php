<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_pendaftaran = (int) $_GET['id'];

// Ambil data pendaftaran + event, pastikan milik user ini
$pendaftaran = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT
        pendaftaran.id_pendaftaran,
        pendaftaran.status,
        event.nama_event,
        event.biaya
    FROM pendaftaran
    JOIN mahasiswa ON pendaftaran.nim = mahasiswa.nim
    JOIN event ON pendaftaran.id_event = event.id_event
    WHERE pendaftaran.id_pendaftaran = '$id_pendaftaran'
    AND mahasiswa.id_user = '" . $_SESSION['id_user'] . "'
"));

if (!$pendaftaran) {
    die("Data pendaftaran tidak ditemukan.");
}

// Cek sudah ada pembayaran sebelumnya (selain yang Ditolak)
$cek = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id_pembayaran, status
    FROM pembayaran
    WHERE id_pendaftaran = '$id_pendaftaran'
    ORDER BY id_pembayaran DESC
    LIMIT 1
"));

if ($cek && $cek['status'] != 'Ditolak') {
    // Sudah upload dan belum ditolak, redirect kembali
    header("Location: event_saya.php");
    exit;
}

$error = '';

// Proses upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_FILES['bukti']['name'])) {
        $error = "Pilih file bukti pembayaran terlebih dahulu.";
    } else {

        $ekstensi_ok = ['jpg', 'jpeg', 'png', 'pdf'];
        $ekstensi    = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $ekstensi_ok)) {
            $error = "Format file harus JPG, PNG, atau PDF.";
        } elseif ($_FILES['bukti']['size'] > 2 * 1024 * 1024) {
            $error = "Ukuran file maksimal 2MB.";
        } else {

            $nama_file = time() . '_' . basename($_FILES['bukti']['name']);
            $tujuan    = "../uploads/" . $nama_file;

            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $tujuan)) {

                // Jika ada pembayaran ditolak sebelumnya, hapus dulu
                if ($cek && $cek['status'] == 'Ditolak') {
                    mysqli_query($conn, "
                        DELETE FROM pembayaran
                        WHERE id_pendaftaran = '$id_pendaftaran'
                    ");
                }

                mysqli_query($conn, "
                    INSERT INTO pembayaran
                    (id_pendaftaran, bukti_bayar, status)
                    VALUES
                    ('$id_pendaftaran', '$nama_file', 'Menunggu')
                ");

                header("Location: event_saya.php");
                exit;

            } else {
                $error = "Gagal mengupload file. Coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran - KampusEvent</title>
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
                    <a class="nav-link nav-pill" href="dashboard.php">
                        <i class="fa-solid fa-house me-1"></i>Utama
                    </a>
                </li>
                <li class="nav-item">
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
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Upload Pembayaran
        </div>
        <div class="text-white-50 mt-2" style="font-size: 0.95rem;">
            Selesaikan administrasi untuk mengamankan kursi pesertamu.
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            
            <div class="page-card">
                <div class="mb-4 pb-3 border-bottom">
                    <a href="event_saya.php" class="text-decoration-none fw-semibold text-muted">
                        <i class="fa-solid fa-arrow-left-long me-2"></i> Kembali ke Riwayat Event
                    </a>
                </div>

                <div class="info-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Nama Event</span>
                        <span class="fw-bold text-dark text-end"><?= htmlspecialchars($pendaftaran['nama_event']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Tagihan</span>
                        <span class="fw-bold fs-5 text-success">Rp <?= number_format($pendaftaran['biaya'], 0, ',', '.'); ?></span>
                    </div>
                </div>

                <?php if ($cek && $cek['status'] == 'Ditolak') { ?>
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
                    <i class="fa-solid fa-circle-xmark fs-3"></i>
                    <div>
                        <strong>Pembayaran sebelumnya ditolak!</strong><br>
                        <span class="small">Silakan unggah ulang bukti transfer yang valid dan jelas.</span>
                    </div>
                </div>
                <?php } ?>

                <?php if ($error) { ?>
                    <div class="alert alert-danger small py-2"><?= $error; ?></div>
                <?php } ?>

                <form action="pembayaran.php?id=<?= $id_pendaftaran; ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark">Pilih File Bukti Bayar <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-lg bg-light" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i>Format yang diizinkan: JPG, PNG, PDF (Maks. 2MB)</small>
                    </div>

                    <button type="submit" class="btn btn-purple w-100 fs-6 py-3"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Bukti Sekarang</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>