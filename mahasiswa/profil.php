<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'MAHASISWA') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "
    SELECT u.nama, u.email, u.foto, u.id_prodi, m.nim, m.angkatan, m.kelas, m.no_hp 
    FROM users u
    JOIN mahasiswa m ON u.id_user = m.id_user
    WHERE u.id_user = '$id_user'
");

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa - KampusEvent</title>
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
            z-index: 10;
        }
        
        .btn-purple { 
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500)); 
            color: white; 
            border: none; 
            font-weight: 700; 
            border-radius: var(--radius-sm);
            padding: 12px 20px;
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-purple:hover { 
            opacity: 0.88; 
            color: white; 
            transform: translateY(-1px); 
        }
        
        .profile-img { 
            width: 120px; 
            height: 120px; 
            object-fit: cover; 
            border-radius: 50%; 
            border: 4px solid var(--purple-100); 
            box-shadow: var(--shadow-sm);
            margin-bottom: 15px; 
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
                    <a class="nav-link nav-pill" href="event_saya.php">
                        <i class="fa-solid fa-calendar-check me-1"></i>Riwayat Event
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-pill active" href="profil.php">
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
            <i class="fa-solid fa-user-gear me-2"></i>Pengaturan Profil
        </div>
        <div class="text-white-50 mt-2" style="font-size: 0.95rem;">
            Kelola informasi pribadi dan data akademik kamu di sini.
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="page-card">
                <form action="../process/profil_process.php" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <?php if (!empty($data['foto'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($data['foto']); ?>" class="profile-img">
                        <?php else: ?>
                            <div class="profile-img bg-light d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="fa-solid fa-user fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="foto" class="form-control form-control-sm w-50 mx-auto" accept=".jpg,.jpeg,.png">
                        <small class="text-muted d-block mt-2"><i class="fa-solid fa-circle-info me-1"></i>JPG/PNG, maks 2MB</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark small mb-1">NIM</label>
                            <input type="text" class="form-control bg-light text-muted" value="<?= htmlspecialchars($data['nim']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark small mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-dark small mb-1">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-dark small mb-1">Program Studi</label>
                        <select name="id_prodi" class="form-select" required>
                            <?php
                            $q_prodi = mysqli_query($conn, "SELECT * FROM prodi ORDER BY nama_prodi ASC");
                            while($prodi = mysqli_fetch_assoc($q_prodi)){
                                $selected = ($prodi['id_prodi'] == $data['id_prodi']) ? 'selected' : '';
                                echo "<option value='".$prodi['id_prodi']."' $selected>".$prodi['nama_prodi']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <hr class="my-4" style="border-color: var(--border);">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i>Data Akademik</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold text-dark small mb-1">Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($data['angkatan']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold text-dark small mb-1">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($data['kelas']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold text-dark small mb-1">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($data['no_hp']); ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_profil" class="btn btn-purple w-100 mt-3 fs-6">
                        <i class="fa-solid fa-save me-2"></i> Simpan Perubahan Profil
                    </button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>