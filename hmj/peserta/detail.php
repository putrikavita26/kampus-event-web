<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$id = (int) $_GET['id'];

$query = mysqli_query($conn, "
    SELECT
        form_event.pertanyaan,
        form_event.tipe_input,
        jawaban_form.jawaban,
        jawaban_form.file_upload

    FROM jawaban_form

    JOIN form_event
    ON jawaban_form.id_form = form_event.id_form

    WHERE jawaban_form.id_pendaftaran = '$id'

    ORDER BY form_event.id_form ASC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Jawaban Form Peserta - HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --theme-purple: #6f42c1;
            --theme-blue: #0d6efd;
            --theme-pink: #d63384;
            --sidebar-gradient: linear-gradient(180deg, #4c1d95 0%, #6f42c1 60%, #d63384 100%);
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        /* Layout Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
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
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            background: #fff;
        }
        .form-question-block {
            background-color: #f8f9fa;
            border-left: 4px solid var(--theme-purple);
            border-radius: 0 8px 8px 0;
        }
        @media (max-width: 991.98px) {
            .sidebar { left: -260px; }
            .main-content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>

    <?php include "../sidebar.php"; ?>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Detail Jawaban Formulir Kustom</h4>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-secondary me-3 d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5">
            <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        <i class="bi bi-card-checklist text-purple me-2"></i>Kuesioner Tambahan Peserta
                    </h5>
                    <p class="text-muted small mb-0">Berikut adalah data isian formulir kustom yang diisi oleh calon peserta saat mendaftar.</p>
                </div>
                <span class="badge bg-purple-subtle text-purple px-3 py-2 font-monospace small">Reg-ID: #<?= $id; ?></span>
            </div>

            <?php if (mysqli_num_rows($query) == 0) { ?>
                <div class="text-center py-5">
                    <div class="fs-1 text-muted mb-3"><i class="bi bi-folder-x"></i></div>
                    <p class="text-secondary italic mb-0">Tidak ada jawaban form kustom untuk pendaftaran ini.</p>
                </div>
            <?php } ?>

            <div class="d-flex flex-column gap-4">
                <?php while ($data = mysqli_fetch_assoc($query)) { ?>
                    <div class="form-question-block p-4 shadow-sm border-end border-top border-bottom">
                        <label class="form-label fw-bold text-dark mb-2 d-block" style="font-size: 1rem;">
                            <i class="bi bi-question-circle text-purple me-1"></i> <?= htmlspecialchars($data['pertanyaan']); ?>
                        </label>
                        
                        <div class="bg-white p-3 rounded border mt-2">
                            <?php
                            if ($data['tipe_input'] == 'image' && !empty($data['file_upload'])) {
                                $image_url = '../../uploads/' . htmlspecialchars($data['file_upload']);
                            ?>
                                <div class="mt-1">
                                    <a href="<?= $image_url; ?>" target="_blank" title="Klik untuk memperbesar gambar">
                                        <img
                                            src="<?= $image_url; ?>"
                                            class="img-fluid rounded border shadow-sm"
                                            style="max-width: 100%; max-height: 380px; object-fit: contain; display: block;"
                                            alt="Jawaban gambar"
                                        >
                                    </a>
                                    <div class="form-text small text-muted mt-2">
                                        <i class="bi bi-zoom-in"></i> Klik pada gambar untuk melihat ukuran penuh di tab baru.
                                    </div>
                                </div>
                            <?php
                            } elseif ($data['tipe_input'] == 'file' && !empty($data['file_upload'])) {
                                $file_url = '../../uploads/' . htmlspecialchars($data['file_upload']);
                            ?>
                                <a
                                    href="<?= $file_url; ?>"
                                    class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 mt-1 shadow-sm"
                                    target="_blank"
                                >
                                    <i class="bi bi-cloud-arrow-down-fill fs-6"></i> Download / Lihat Berkas File
                                </a>
                            <?php
                            } else {
                            ?>
                                <p class="mb-0 text-dark" style="line-height: 1.6;">
                                    <?= nl2br(htmlspecialchars($data['jawaban'] ?? '-')); ?>
                                </p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>