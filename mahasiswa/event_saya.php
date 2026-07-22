<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$query = mysqli_query($conn, "
    SELECT
        pendaftaran.id_pendaftaran,
        pendaftaran.status AS status_pendaftaran,
        pendaftaran.tanggal_daftar,
        event.nama_event,
        event.biaya,
        event.link_grup,
        event.informasi_peserta,
        pembayaran.status AS status_pembayaran,
        pembayaran.bukti_bayar
    FROM pendaftaran
    JOIN mahasiswa ON pendaftaran.nim = mahasiswa.nim
    JOIN event ON pendaftaran.id_event = event.id_event
    LEFT JOIN (
        SELECT * FROM pembayaran
        WHERE id_pembayaran IN (
            SELECT MAX(id_pembayaran) FROM pembayaran GROUP BY id_pendaftaran
        )
    ) pembayaran ON pendaftaran.id_pendaftaran = pembayaran.id_pendaftaran
    WHERE mahasiswa.id_user = '" . $_SESSION['id_user'] . "'
    ORDER BY pendaftaran.tanggal_daftar DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Saya - Kampus Event</title>
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

        .btn-purple { 
            background: linear-gradient(135deg, var(--purple-700), var(--purple-500)); 
            color: white; 
            border: none; 
            font-weight: 700; 
            border-radius: var(--radius-sm);
            padding: 8px 16px;
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-purple:hover { 
            opacity: 0.88; 
            color: white; 
            transform: translateY(-1px); 
        }

        thead { background-color: var(--purple-50); border-bottom: 2px solid var(--purple-100); }
        thead th { 
            color: var(--purple-800); 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 0.80rem; 
            padding: 16px !important; 
            letter-spacing: 0.5px;
        }
        tbody td { 
            vertical-align: middle; 
            padding: 18px 16px !important; 
            font-size: 0.95rem; 
            border-bottom: 1px solid var(--border);
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
            <i class="fa-solid fa-list-check me-2"></i>Riwayat Pendaftaran
        </div>
        <div class="text-white-50 mt-2" style="font-size: 0.95rem;">
            Pantau status pendaftaran dan pembayaran event kamu di sini.
        </div>
    </div>
</section>

<div class="container mb-5">
        <div class="col-lg-11">
            
            <div class="page-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>Tanggal Daftar</th>
                                <th>Status Seleksi</th>
                                <th>Status Pembayaran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $rows = [];
                        while ($data = mysqli_fetch_assoc($query)) {
                            $rows[] = $data;
                        }

                        if (empty($rows)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: var(--purple-100); color: var(--purple-500);">
                                        <i class="fa-regular fa-folder-open fa-2x"></i>
                                    </div>
                                    <span class="fw-semibold">Kamu belum mendaftar ke event manapun.</span>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($rows as $data):

                            $biaya         = (float) $data['biaya'];
                            $status_daftar = $data['status_pendaftaran'];
                            $status_bayar  = $data['status_pembayaran'];

                            $bisa_detail = $status_daftar === 'Diterima' &&
                                           ($biaya == 0 || $status_bayar === 'Valid');

                            $perlu_upload = $biaya > 0 &&
                                           ($status_daftar === 'Menunggu' || $status_daftar === 'Diterima') &&
                                           (empty($status_bayar) || $status_bayar === 'Ditolak');
                        ?>
                        <tr>
                            <td class="fw-bold text-dark" style="min-width: 200px;">
                                <?= htmlspecialchars($data['nama_event']); ?>
                            </td>

                            <td class="text-muted">
                                <?= date('d M Y', strtotime($data['tanggal_daftar'])); ?>
                            </td>

                            <td>
                                <?php
                                $badge_seleksi = 'bg-secondary';
                                if ($status_daftar == 'Diterima') $badge_seleksi = 'bg-success';
                                elseif ($status_daftar == 'Ditolak') $badge_seleksi = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_seleksi; ?> rounded-pill px-3 py-2">
                                    <?= htmlspecialchars($status_daftar); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($biaya == 0): ?>
                                    <span class="text-success fw-bold small">
                                        <i class="fa-solid fa-tag me-1"></i>Gratis
                                    </span>

                                <?php elseif (empty($status_bayar)): ?>
                                    <span class="text-warning fw-bold small">
                                        <i class="fa-solid fa-clock me-1"></i>Belum Upload
                                    </span>

                                <?php else: ?>
                                    <?php
                                    $badge_bayar = 'bg-secondary';
                                    if ($status_bayar == 'Valid')    $badge_bayar = 'bg-success';
                                    if ($status_bayar == 'Ditolak')  $badge_bayar = 'bg-danger';
                                    if ($status_bayar == 'Menunggu') $badge_bayar = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badge_bayar; ?> rounded-pill px-3 py-2">
                                        <?= htmlspecialchars($status_bayar); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($data['bukti_bayar'])): ?>
                                    <div class="mt-2">
                                        <a href="../uploads/<?= htmlspecialchars($data['bukti_bayar']); ?>"
                                           target="_blank"
                                           class="text-decoration-none small" style="color: var(--purple-600); font-weight: 600;">
                                            <i class="fa-solid fa-file-image me-1"></i>Lihat Bukti
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">

                                    <?php if ($bisa_detail): ?>
                                        <a href="informasi_event.php?id=<?= $data['id_pendaftaran']; ?>"
                                           class="btn btn-sm btn-purple px-3 py-2">
                                            <i class="fa-solid fa-circle-info me-1"></i>Detail Event
                                        </a>

                                    <?php elseif ($perlu_upload): ?>
                                        <a href="pembayaran.php?id=<?= $data['id_pendaftaran']; ?>"
                                           class="btn btn-sm <?= $status_bayar === 'Ditolak' ? 'btn-danger' : 'btn-warning text-dark'; ?> px-3 py-2 rounded-3 fw-bold">
                                            <i class="fa-solid fa-upload me-1"></i>
                                            <?= $status_bayar === 'Ditolak' ? 'Upload Ulang' : 'Upload Bukti'; ?>
                                        </a>

                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">
                                            <i class="fa-solid fa-hourglass-half me-1"></i>Menunggu...
                                        </span>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>