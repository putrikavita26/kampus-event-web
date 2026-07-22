<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    header("Location: ../login.php");
    exit;
}

$id_event = (int) $_GET['id'];

// Query mengambil data event dan nama HMJ
$query = mysqli_query($conn, "
    SELECT event.*, users.nama AS nama_hmj 
    FROM event 
    JOIN users ON event.id_hmj = users.id_user 
    WHERE event.id_event = '$id_event'
");
$event = mysqli_fetch_assoc($query);

if (!$event) {
    echo "Event tidak ditemukan.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Event - <?= htmlspecialchars($event['nama_event']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #6b21a8 0%, #9333ea 60%, #c026d3 100%);
            font-family: 'Segoe UI', sans-serif; }
        .detail-card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .poster-container {
            width: 100%;
            height: 300px;
            background-color: #f8f9fa;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 25px;
            border: 1px solid #eee;
        }

        .poster-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

    </style>
</head>
<body class="p-4">

    <div class="container my-5">
        <div class="card detail-card p-4">
            
            <div class="poster-container">
                <?php if (!empty($event['poster'])) { ?>
                    <img src="../uploads/<?= htmlspecialchars($event['poster']); ?>" class="poster-img" alt="Poster">
                <?php } else { ?>
                    <div class="text-muted"><i class="bi bi-image fs-1"></i><br>Tidak ada poster</div>
                <?php } ?>
            </div>

            <div class="row">
                <div class="col-12">
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($event['status']); ?></span>
                    <h2 class="fw-bold"><?= htmlspecialchars($event['nama_event']); ?></h2>
                    <p class="text-muted"><i class="bi bi-building"></i> Penyelenggara: <strong><?= htmlspecialchars($event['nama_hmj']); ?></strong></p>
                    
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6 col-md-4"><p><strong>Kategori:</strong><br><?= $event['kategori']; ?></p></div>
                        <div class="col-6 col-md-4"><p><strong>Tanggal:</strong><br><?= date('d-m-Y', strtotime($event['tanggal_event'])); ?></p></div>
                        <div class="col-6 col-md-4"><p><strong>Lokasi:</strong><br><?= $event['lokasi']; ?></p></div>
                        <div class="col-6 col-md-4"><p><strong>Kuota:</strong><br><?= $event['kuota']; ?> Peserta</p></div>
                        <div class="col-6 col-md-4"><p><strong>Biaya:</strong><br><?= ($event['biaya'] > 0) ? 'Rp ' . number_format($event['biaya'], 0, ',', '.') : 'Gratis'; ?></p></div>
                    </div>

                    <h5>Deskripsi Kegiatan</h5>
                    <p class="text-secondary"><?= nl2br(htmlspecialchars($event['deskripsi'])); ?></p>
                    <div class="text-end mt-4">
                        <a href="verifikasi.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>