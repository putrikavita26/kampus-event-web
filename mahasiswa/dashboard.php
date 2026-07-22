<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$mhs_user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT foto FROM users WHERE id_user = '$id_user'
"));

$query_hmj   = mysqli_query($conn, "SELECT id_user, nama FROM users WHERE role = 'HMJ' ORDER BY nama ASC");
$query_prodi = mysqli_query($conn, "SELECT * FROM prodi ORDER BY nama_prodi ASC");

$daftar_kategori = ['Lomba', 'Seminar', 'Open Recruitment', 'Workshop', 'Lainnya'];

$search          = isset($_GET['search'])   ? mysqli_real_escape_string($conn, $_GET['search'])   : '';
$filter_kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$filter_hmj      = isset($_GET['hmj'])      ? mysqli_real_escape_string($conn, $_GET['hmj'])      : '';
$filter_biaya    = isset($_GET['biaya'])    ? $_GET['biaya']    : '';
$filter_prodi    = isset($_GET['prodi'])    ? $_GET['prodi']    : '';

$where = "WHERE event.status = 'Disetujui'";

if (!empty($search)) {
    $where .= " AND (event.nama_event LIKE '%$search%' OR users.nama LIKE '%$search%')";
}
if (!empty($filter_kategori)) {
    $where .= " AND event.kategori = '$filter_kategori'";
}
if (!empty($filter_hmj)) {
    $where .= " AND event.id_hmj = '$filter_hmj'";
}
if ($filter_biaya === 'gratis') {
    $where .= " AND (event.biaya = 0 OR event.biaya IS NULL)";
} elseif ($filter_biaya === 'bayar') {
    $where .= " AND event.biaya > 0";
}
if ($filter_prodi === 'umum') {
    $where .= " AND event.id_prodi IS NULL";
} elseif ($filter_prodi === 'prodi') {
    $where .= " AND event.id_prodi IS NOT NULL";
}

$total_aktif = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM event WHERE status = 'Disetujui'
"))['total'];

$query = mysqli_query($conn, "
    SELECT
        event.*,
        users.nama AS hmj_nama,
        prodi2.nama_prodi AS prodi_hmj,
        prodi.nama_prodi  AS nama_prodi_target,
        COUNT(pendaftaran.id_pendaftaran) AS terpakai
    FROM event
    LEFT JOIN pendaftaran
        ON event.id_event = pendaftaran.id_event
        AND pendaftaran.status IN ('Menunggu', 'Diterima')
    LEFT JOIN users  ON event.id_hmj   = users.id_user
    LEFT JOIN prodi AS prodi2 ON users.id_prodi = prodi2.id_prodi
    LEFT JOIN prodi  ON event.id_prodi = prodi.id_prodi
    $where
    GROUP BY event.id_event
    ORDER BY event.tanggal_event ASC
");

$total_hasil = mysqli_num_rows($query);

$ada_filter = !empty($search) || !empty($filter_kategori) || !empty($filter_hmj) ||
              $filter_biaya !== '' || $filter_prodi !== '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kampus Event</title>
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
            text-decoration: none;
        }

        /* hero section */
        .hero-section {
            background: linear-gradient(135deg, var(--purple-800) 0%, var(--purple-600) 60%, #c026d3 100%);
            padding: 56px 0 80px;
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
        .hero-avatar {
            width: 72px; height: 72px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.4);
            object-fit: cover;
        }
        .hero-avatar-placeholder {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 3px solid rgba(255,255,255,0.3);
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.8);
            font-size: 1.6rem;
            flex-shrink: 0;
        }
        .hero-greeting {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .hero-sub {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            margin-top: 6px;
        }
        .hero-stat-box {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: var(--radius-md);
            padding: 16px 24px;
            text-align: center;
            min-width: 130px;
        }
        .hero-stat-number {
            font-size: 2.4rem;
            font-weight: 900;
            color: #fff;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.7);
            margin-top: 4px;
            font-weight: 500;
        }

        /* search */
        .floating-panel {
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            padding: 28px;
            margin-top: -40px;
            position: relative;
            z-index: 10;
        }

        .search-wrap { position: relative; }
        .search-wrap .search-icon {
            position: absolute;
            left: 18px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }
        .search-wrap input {
            padding-left: 48px;
            padding-right: 130px;
            height: 52px;
            border-radius: 50px !important;
            border: 2px solid var(--border) !important;
            font-size: 0.9rem;
            font-weight: 500;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-wrap input:focus {
            border-color: var(--purple-500) !important;
            box-shadow: 0 0 0 4px rgba(168,85,247,0.1) !important;
        }
        .search-wrap .btn-search-pill {
            position: absolute;
            right: 6px; top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500));
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 9px 22px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: opacity 0.2s;
        }
        .search-wrap .btn-search-pill:hover { opacity: 0.88; }

        .filter-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 20px 0;
        }

        /* Filter selects */
        .filter-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }
        .filter-select {
            border: 1.5px solid var(--border) !important;
            border-radius: var(--radius-sm) !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            height: 42px;
            color: var(--text-dark);
            transition: border-color 0.2s;
        }
        .filter-select:focus {
            border-color: var(--purple-500) !important;
            box-shadow: 0 0 0 3px rgba(168,85,247,0.1) !important;
        }
        .btn-apply {
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500));
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 10px 24px;
            font-weight: 700;
            font-size: 0.85rem;
            height: 42px;
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-apply:hover { opacity: 0.88; transform: translateY(-1px); color: #fff; }
        .btn-reset {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-muted);
            border-radius: var(--radius-sm);
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            height: 42px;
            transition: all 0.2s;
        }
        .btn-reset:hover { border-color: var(--purple-500); color: var(--purple-600); }

        /* Active filter chips */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--purple-100);
            color: var(--purple-700);
            border-radius: 50px;
            padding: 5px 12px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        /* section header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.3px;
        }
        .result-count {
            background: var(--purple-100);
            color: var(--purple-700);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        /* event card */
        .event-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }
        .poster-wrap {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, var(--purple-100), var(--purple-200));
            overflow: hidden;
        }
        .poster-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.4s;
        }
        .event-card:hover .poster-wrap img { transform: scale(1.05); }
        .poster-placeholder {
            display: flex; align-items: center; justify-content: center;
            height: 100%;
            color: var(--purple-300, #d8b4fe);
            font-size: 3rem;
        }

       /* Corner badge */
        .corner-badge {
            position: absolute;
            top: 12px; left: 12px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .corner-badge.gratis {
            background: rgba(16,185,129,0.9);
            color: #fff;
        }
        .corner-badge.bayar {
            background: rgba(239,68,68,0.9);
            color: #fff;
        }

        /* Kuota bar */
        .kuota-bar-wrap {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.55);
            padding: 8px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .kuota-bar {
            flex: 1;
            height: 5px;
            background: rgba(255,255,255,0.3);
            border-radius: 99px;
            overflow: hidden;
        }
        .kuota-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        .kuota-bar-fill.warn { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .kuota-bar-fill.full { background: linear-gradient(90deg, #ef4444, #f87171); }
        .kuota-text {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.9);
            font-weight: 600;
            white-space: nowrap;
        }

        /* Card body */
        .card-body-inner {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .ev-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.4;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8em;
        }
        .ev-hmj {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--purple-600);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ev-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
        .ev-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
        }
        .ev-badge.kategori {
            background: var(--purple-100);
            color: var(--purple-700);
        }
        .ev-badge.umum {
            background: #dcfce7;
            color: #166534;
        }
        .ev-badge.prodi {
            background: #dbeafe;
            color: #1d4ed8;
        }

        /* Meta row */
        .meta-row {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 6px;
        }
        .meta-row i { width: 14px; text-align: center; }

        /* Action buttons */
        .card-actions {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 8px;
        }
        .btn-detail {
            flex: 1;
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-muted);
            border-radius: var(--radius-sm);
            padding: 9px;
            font-size: 0.82rem;
            font-weight: 700;
            transition: all 0.2s;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-detail:hover {
            border-color: var(--purple-500);
            color: var(--purple-600);
        }
        .btn-daftar {
            flex: 1;
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500));
            color: #fff !important;
            border: none;
            border-radius: var(--radius-sm);
            padding: 9px;
            font-size: 0.82rem;
            font-weight: 700;
            transition: opacity 0.2s, transform 0.1s;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-daftar:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn-penuh {
            flex: 1;
            background: #f1f5f9;
            color: #94a3b8;
            border: none;
            border-radius: var(--radius-sm);
            padding: 9px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: not-allowed;
            text-align: center;
        }

        /* empty state */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        .empty-icon {
            width: 100px; height: 100px;
            background: var(--purple-100);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            color: var(--purple-400, #c084fc);
            margin: 0 auto 24px;
        }
        .empty-state h5 {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .empty-state p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        /* footer */
        .footer-custom {
            background: #fff;
            border-top: 1px solid var(--border);
            padding: 32px 0;
            margin-top: 60px;
        }
        .footer-brand {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text-dark);
            letter-spacing: -0.3px;
        }
        .footer-brand span { color: var(--purple-600); }
        .footer-social a {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--purple-100);
            color: var(--purple-600);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            margin-left: 8px;
        }
        .footer-social a:hover {
            background: var(--purple-600);
            color: #fff;
        }
    </style>
</head>
<body>

<!-- navbar -->
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
                        <i class="fa-solid fa-house me-1"></i>Home
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

<!-- hero -->
<section class="hero-section">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-4">
                <?php if (!empty($mhs_user['foto'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($mhs_user['foto']); ?>"
                         class="hero-avatar" alt="Foto">
                <?php else: ?>
                    <div class="hero-avatar-placeholder">
                        <i class="fa-solid fa-user"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="hero-greeting">
                        Halo, <?= htmlspecialchars($_SESSION['nama']); ?>! 👋
                    </div>
                    <div class="hero-sub">
                        Temukan dan ikuti event kemahasiswaan terbaikmu hari ini.
                    </div>
                </div>
            </div>
            <div class="hero-stat-box">
                <div class="hero-stat-number"><?= $total_aktif; ?></div>
                <div class="hero-stat-label">Event Aktif</div>
            </div>
        </div>
    </div>
</section>

<!-- search & filter -->
<div class="container">
    <div class="floating-panel">

        <!-- Search -->
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <form method="GET" action="dashboard.php" id="searchForm">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama event atau penyelenggara..."
                       value="<?= htmlspecialchars($search); ?>"
                       autocomplete="off">
                <button class="btn-search-pill" type="submit">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                </button>
            </form>
        </div>

        <hr class="filter-divider">

        <!-- Filter -->
        <form method="GET" action="dashboard.php" id="filterForm">
            <?php if (!empty($search)): ?>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
            <?php endif; ?>

            <div class="row g-3 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="filter-label">Kategori</label>
                    <select name="kategori" class="form-select filter-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($daftar_kategori as $kat): ?>
                            <option value="<?= $kat; ?>" <?= ($filter_kategori == $kat) ? 'selected' : ''; ?>>
                                <?= $kat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="filter-label">Penyelenggara</label>
                    <select name="hmj" class="form-select filter-select">
                        <option value="">Semua HMJ</option>
                        <?php while ($h = mysqli_fetch_assoc($query_hmj)): ?>
                            <option value="<?= $h['id_user']; ?>"
                                <?= ($filter_hmj == $h['id_user']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($h['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">Biaya</label>
                    <select name="biaya" class="form-select filter-select">
                        <option value="">Semua</option>
                        <option value="gratis" <?= ($filter_biaya == 'gratis') ? 'selected' : ''; ?>>Gratis</option>
                        <option value="bayar"  <?= ($filter_biaya == 'bayar')  ? 'selected' : ''; ?>>Berbayar</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">Target</label>
                    <select name="prodi" class="form-select filter-select">
                        <option value="">Semua</option>
                        <option value="umum"  <?= ($filter_prodi == 'umum')  ? 'selected' : ''; ?>>Umum</option>
                        <option value="prodi" <?= ($filter_prodi == 'prodi') ? 'selected' : ''; ?>>Prodi Tertentu</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn-apply">
                        <i class="fa-solid fa-sliders me-1"></i>Filter
                    </button>
                    <a href="dashboard.php" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Active chips -->
        <?php if ($ada_filter): ?>
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top" style="border-color: var(--border) !important;">
            <span class="text-muted small fw-600 me-1" style="line-height:2;">Filter aktif:</span>
            <?php if (!empty($search)): ?>
                <span class="chip"><i class="fa-solid fa-magnifying-glass"></i><?= htmlspecialchars($search); ?></span>
            <?php endif; ?>
            <?php if (!empty($filter_kategori)): ?>
                <span class="chip"><i class="fa-solid fa-tag"></i><?= htmlspecialchars($filter_kategori); ?></span>
            <?php endif; ?>
            <?php if (!empty($filter_hmj)): ?>
                <span class="chip"><i class="fa-solid fa-building"></i>HMJ dipilih</span>
            <?php endif; ?>
            <?php if ($filter_biaya == 'gratis'): ?>
                <span class="chip"><i class="fa-solid fa-gift"></i>Gratis</span>
            <?php elseif ($filter_biaya == 'bayar'): ?>
                <span class="chip"><i class="fa-solid fa-money-bill"></i>Berbayar</span>
            <?php endif; ?>
            <?php if ($filter_prodi == 'umum'): ?>
                <span class="chip"><i class="fa-solid fa-globe"></i>Umum</span>
            <?php elseif ($filter_prodi == 'prodi'): ?>
                <span class="chip"><i class="fa-solid fa-graduation-cap"></i>Prodi Tertentu</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- event grid -->
<div class="container mt-5">

    <div class="section-header">
        <div class="section-title">
            <i class="fa-solid fa-sparkles text-warning me-2"></i>
            <?= $ada_filter ? 'Hasil Pencarian' : 'Event Tersedia'; ?>
        </div>
        <div class="result-count">
            <?= $total_hasil; ?> event ditemukan
        </div>
    </div>

    <div class="row g-4">
        <?php
        $has_data = false;
        while ($event = mysqli_fetch_assoc($query)):
            $has_data  = true;
            $terpakai  = $event['terpakai'];
            $kuota     = $event['kuota'];
            $sisa      = $kuota - $terpakai;
            $pct       = $kuota > 0 ? min(100, round($terpakai / $kuota * 100)) : 0;
            $bar_class = $pct >= 90 ? 'full' : ($pct >= 60 ? 'warn' : '');
        ?>
        <div class="col-sm-6 col-lg-4">
            <div class="event-card">

                <!-- Poster -->
                <div class="poster-wrap">
                    <?php if (!empty($event['poster'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($event['poster']); ?>" alt="Poster">
                    <?php else: ?>
                        <div class="poster-placeholder">
                            <i class="fa-regular fa-image"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Gratis / Bayar badge -->
                    <?php if ($event['biaya'] > 0): ?>
                        <span class="corner-badge bayar">Berbayar</span>
                    <?php else: ?>
                        <span class="corner-badge gratis">Gratis</span>
                    <?php endif; ?>

                    <!-- Kuota progress bar -->
                    <div class="kuota-bar-wrap">
                        <div class="kuota-bar">
                            <div class="kuota-bar-fill <?= $bar_class; ?>"
                                 style="width: <?= $pct; ?>%"></div>
                        </div>
                        <span class="kuota-text">
                            <?= $sisa > 0 ? $sisa . ' kursi' : 'Penuh'; ?>
                        </span>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body-inner">
                    <div class="ev-title" title="<?= htmlspecialchars($event['nama_event']); ?>">
                        <?= htmlspecialchars($event['nama_event']); ?>
                    </div>

                    <div class="ev-hmj">
                        <i class="fa-solid fa-building-shield"></i>
                        <?= htmlspecialchars($event['hmj_nama']); ?>
                    </div>

                    <div class="ev-badges">
                        <?php if (!empty($event['kategori'])): ?>
                            <span class="ev-badge kategori">
                                <i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($event['kategori']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($event['id_prodi'])): ?>
                            <span class="ev-badge prodi">
                                <i class="fa-solid fa-graduation-cap me-1"></i>
                                <?= htmlspecialchars($event['nama_prodi_target']); ?>
                            </span>
                        <?php else: ?>
                            <span class="ev-badge umum">
                                <i class="fa-solid fa-globe me-1"></i>Umum
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="meta-row">
                        <i class="fa-solid fa-calendar-day" style="color: var(--purple-500);"></i>
                        <?= date('d M Y', strtotime($event['tanggal_event'])); ?>
                    </div>
                    <div class="meta-row">
                        <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i>
                        <?= htmlspecialchars($event['lokasi'] ?: '-'); ?>
                    </div>
                    <div class="meta-row">
                        <i class="fa-solid fa-users" style="color: #10b981;"></i>
                        Kuota <?= $kuota; ?> peserta
                    </div>

                    <!-- Actions -->
                    <div class="card-actions">
                        <a href="detail_event.php?id=<?= $event['id_event']; ?>" class="btn-detail">
                            <i class="fa-solid fa-circle-info"></i>Detail
                        </a>
                        <?php if ($sisa > 0): ?>
                            <a href="daftar.php?id=<?= $event['id_event']; ?>" class="btn-daftar">
                                <i class="fa-solid fa-pen-to-square"></i>Daftar
                            </a>
                        <?php else: ?>
                            <span class="btn-penuh">
                                <i class="fa-solid fa-ban me-1"></i>Penuh
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <?php endwhile; ?>

        <?php if (!$has_data): ?>
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <h5>
                    <?= $ada_filter
                        ? 'Tidak Ada Event yang Sesuai Filter'
                        : 'Belum Ada Event yang Tersedia'; ?>
                </h5>
                <p>
                    <?= $ada_filter
                        ? 'Coba ubah atau reset filter pencarian kamu.'
                        : 'Silakan kembali beberapa saat lagi.'; ?>
                </p>
                <?php if ($ada_filter): ?>
                    <a href="dashboard.php" class="btn-apply" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px; width:auto;">
                        <i class="fa-solid fa-rotate-left"></i> Reset Filter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- footer -->
<footer class="footer-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="footer-brand">Kampus<span>Event</span></div>
                <p class="text-muted small mb-0 mt-1">
                    Platform pusat informasi kegiatan kemahasiswaan terpadu.
                </p>
            </div>
            <div class="col-md-4 text-center">
                <p class="text-muted small mb-0">
                    &copy; <?= date('Y'); ?> KampusEvent. All rights reserved.
                </p>
            </div>
            <div class="col-md-4 text-md-end footer-social">
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#"><i class="fa-solid fa-envelope"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>