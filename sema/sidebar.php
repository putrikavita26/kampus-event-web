<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
                /* Layout Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            width: 260px;
            background: linear-gradient(135deg, #6b21a8 0%, #9333ea 60%, #c026d3 100%);
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

        /* Main Workspace */
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
    </style>
</head>
<div class="sidebar">
    <div class="sidebar-brand d-flex align-items-center">
        <i class="bi bi-grid-1x2-fill me-2 text-warning"></i>
        <span>Dashboard SEMA</span>
    </div>
    <ul class="nav flex-column mt-4">
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'verifikasi.php') ? 'active' : ''; ?>" href="verifikasi.php">
                <i class="bi bi-shield-check"></i> Verifikasi Event
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'kelola_hmj.php') ? 'active' : ''; ?>" href="kelola_hmj.php">
                <i class="bi bi-person-workspace"></i> Kelola Data HMJ
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : ''; ?>" href="laporan.php">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan Event
            </a>
        </li>
        
        <li class="nav-item mt-5 pt-4">
            <hr class="text-white-50 mx-3">
            <a class="nav-link text-warning" href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </li>
    </ul>
</div>