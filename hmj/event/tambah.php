<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$prodi_query = mysqli_query($conn, "SELECT * FROM prodi ORDER BY nama_prodi");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event - HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
</head>
<body>

    <?php include "../sidebar.php"; ?>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Form Input Kegiatan Baru</h4>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-secondary me-3 d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5">
            <div class="border-bottom pb-3 mb-4">
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-medical me-2 text-purple"></i>Detail Informasi Kegiatan</h5>
                <p class="text-muted small mb-0">Isi formulir di bawah ini dengan lengkap untuk mengajukan event ke pihak SEMA</p>
            </div>

            <form action="action.php" method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-12 col-md-8">
                        <label class="form-label">Nama Event</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-fonts"></i></span>
                            <input type="text" name="nama_event" class="form-control" placeholder="Contoh: Webinar Nasional Teknologi 2026" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-tags"></i></span>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Lomba">Lomba</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Open Recruitment">Open Recruitment</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi Event</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tuliskan latar belakang, tujuan, atau detail acara secara singkat..."></textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Lokasi / Tempat Pelaksanaan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Aula Gedung H.4 atau Zoom Meeting">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal Pelaksanaan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" name="tanggal_event" class="form-control">
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Kuota Peserta</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-people"></i></span>
                            <input type="number" name="kuota" class="form-control" placeholder="0">
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Biaya Pendaftaran (IDR)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">Rp</span>
                            <input type="number" name="biaya" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Link Lanjutan / Grup WhatsApp (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" name="link_grup" class="form-control" placeholder="https://chat.whatsapp.com/...">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Program Studi (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-mortarboard"></i></span>
                            <select name="id_prodi" class="form-select">
                                <option value="">-- Semua Prodi --</option>
                                <?php while($p = mysqli_fetch_assoc($prodi_query)) { ?>
                                    <option value="<?= $p['id_prodi']; ?>">
                                        <?= htmlspecialchars($p['nama_prodi']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Informasi Khusus Peserta <span class="text-muted font-monospace small">(Tampil otomatis setelah verifikasi pendaftaran disetujui)</span></label>
                        <textarea name="informasi_peserta" class="form-control" rows="4" placeholder="Contoh: Silakan masuk ke grup melalui tautan berikut atau instruksi lainnya..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Unggah Poster Kegiatan</label>
                        <div class="card p-3 bg-light border-dashed">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white rounded p-2 border text-purple fs-3">
                                    <i class="bi bi-image-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="poster" class="form-control form-control-sm" accept="image/*">
                                    <div class="form-text small text-muted mt-1">Gunakan berkas format gambar (.jpg, .jpeg, .png)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-5 pt-3 border-top">
                    <a href="index.php" class="btn btn-light px-4">Batal</a>
                    <button type="submit" name="simpan" class="btn btn-primary px-5 fw-bold" style="background-color: var(--theme-purple); border:none;">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i>Simpan & Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>