<?php
$current_page  = basename($_SERVER['PHP_SELF']);
$directory_uri = $_SERVER['REQUEST_URI'];

$is_subfolder = strpos($directory_uri, '/event/')      !== false ||
                strpos($directory_uri, '/form_event/') !== false ||
                strpos($directory_uri, '/peserta/')    !== false ||
                strpos($directory_uri, '/pembayaran/') !== false ||
                strpos($directory_uri, '/rekap/')      !== false;

$base_path   = $is_subfolder ? "../"       : "";
$logout_path = $is_subfolder ? "../../logout.php" : "../logout.php";
?>

<style>
    :root {
        --theme-purple:    #9333ea; 
        --theme-blue:      #0d6efd;
        --theme-pink:      #c026d3; 

        --sidebar-gradient: linear-gradient(135deg, #6b21a8 0%, #9333ea 60%, #c026d3 100%);
    }

    * { box-sizing: border-box; }
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
        margin: 0;
    }

    /* SIDEBAR */
    .sidebar {
        position: fixed;
        top: 0; bottom: 0; left: 0;
        z-index: 100;
        width: 260px;
        background: var(--sidebar-gradient);
        box-shadow: 4px 0 10px rgba(0,0,0,0.1);
    }
    .sidebar-brand {
        padding: 24px;
        font-size: 1.25rem;
        font-weight: 700;
        color: #fff;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.7);
        padding: 12px 24px;
        font-weight: 500;
        margin: 4px 12px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .sidebar .nav-link:hover {
        color: #fff;
        background: rgba(255,255,255,0.1);
    }
    .sidebar .nav-link.active {
        color: #fff;
        background: rgba(255,255,255,0.2);
        box-shadow: inset 3px 0 0 #fff;
    }
    .sidebar .nav-link i {
        margin-right: 12px;
        font-size: 1.1rem;
    }

       /* LAYOUT UTAMA */
    .main-content {
        margin-left: 260px;
        padding: 40px;
    }
    .top-navbar {
        background: #fff;
        padding: 15px 30px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        margin-bottom: 30px;
    }

    /* card */
    .card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        background: #fff;
    }
    .stat-card {
        border: none;
        border-radius: 12px;
        color: #fff;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .bg-gradient-blue   { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
    .bg-gradient-purple { background: linear-gradient(135deg, #6f42c1 0%, #59359a 100%); }
    .bg-gradient-pink   { background: linear-gradient(135deg, #d63384 0%, #b11f6b 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #fd7e14 0%, #e0650d 100%); }


    /* status */
    /* Event */
    .badge-process  { background-color: rgba(253,126,20,0.15);  color: #fd7e14; }
    .badge-disetujui{ background-color: rgba(25,135,84,0.15);   color: #198754; }
    .badge-ditolak  { background-color: rgba(220,53,69,0.15);   color: #dc3545; }

    /* Pendaftaran / peserta */
    .badge-menunggu { background-color: rgba(255,193,7,0.15);   color: #ffc107; }
    .badge-diterima { background-color: rgba(25,135,84,0.15);   color: #198754; }

    /* Pembayaran */
    .badge-valid    { background-color: rgba(25,135,84,0.15);   color: #198754; }

    /* Kuota */
    .badge-sisa     { background-color: rgba(25,135,84,0.15);   color: #198754; }
    .badge-penuh    { background-color: rgba(220,53,69,0.15);   color: #dc3545; }

    /* btn */
    .btn-purple {
        background: var(--theme-purple);
        color: white;
        border: none;
        font-weight: 500;
    }
    .btn-purple:hover {
        background: #5a32a3;
        color: white;
    }

    /* profil */
    .avatar-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #f8f9fa;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        border: 4px solid #e9ecef;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    /* form event */
    .form-question-block {
        background-color: #f8f9fa;
        border-left: 4px solid var(--theme-purple);
        border-radius: 0 8px 8px 0;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #b692f6;
        box-shadow: 0 0 0 0.25rem rgba(111,66,193,0.25);
    }

    @media (max-width: 991.98px) {
        .sidebar     { left: -260px; }
        .main-content{ margin-left: 0; padding: 20px; }
    }
</style>

<!-- ===== SIDEBAR HTML ===== -->
 <button id="hamburger-btn" class="d-lg-none" style="position:fixed; top:10px; left:10px; z-index:101; background:transparent; border:none; color:white; font-size:24px;">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar">
    <div class="sidebar-brand d-flex align-items-center">
        <i class="bi bi-building-fill me-2 text-warning"></i>
        <span>Dashboard HMJ</span>
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>"
               href="<?= $base_path; ?>dashboard.php">
                <i class="bi bi-grid-1x2-fill"></i> Beranda Utama
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= (strpos($directory_uri, '/event/') !== false) ? 'active' : ''; ?>"
               href="<?= $base_path; ?>event/index.php">
                <i class="bi bi-calendar-event"></i> Kelola Event
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= (strpos($directory_uri, '/form_event/') !== false) ? 'active' : ''; ?>"
               href="<?= $base_path; ?>form_event/index.php">
                <i class="bi bi-file-earmark-plus"></i> Form Event
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= (strpos($directory_uri, '/peserta/') !== false) ? 'active' : ''; ?>"
               href="<?= $base_path; ?>peserta/index.php">
                <i class="bi bi-people"></i> Data Peserta
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= (strpos($directory_uri, '/pembayaran/') !== false) ? 'active' : ''; ?>"
               href="<?= $base_path; ?>pembayaran/index.php">
                <i class="bi bi-cash-coin"></i> Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= (strpos($directory_uri, '/rekap/') !== false) ? 'active' : ''; ?>"
               href="<?= $base_path; ?>rekap/index.php">
                <i class="bi bi-file-earmark-bar-graph"></i> Rekap Peserta
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'profil.php') ? 'active' : ''; ?>"
               href="<?= $base_path; ?>profil.php">
                <i class="bi bi-person-circle"></i> Profil HMJ
            </a>
        </li>
        <li class="nav-item mt-4 pt-3">
            <hr class="text-white-50 mx-3">
            <a class="nav-link text-warning"
               href="<?= $logout_path; ?>"
               onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </li>
    </ul>
</div>