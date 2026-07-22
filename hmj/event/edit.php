<?php

include "../../config/koneksi.php";

$id = $_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM event WHERE id_event='$id'"
);

$data = mysqli_fetch_assoc($query);

// Daftar kategori
$daftar_kategori = ['Lomba', 'Seminar', 'Open Recruitment', 'Workshop', 'Lainnya'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
</head>
<body>

    <?php include "../sidebar.php"; ?>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Edit Data Event</h4>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-secondary me-3 d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5 mb-4">
            <h5 class="fw-bold text-dark mb-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Ubah Informasi Kegiatan</h5>
            
            <form action="action.php" method="POST" enctype="multipart/form-data" class="row g-3">

                <input type="hidden" name="id_event" value="<?= $data['id_event']; ?>">

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Nama Event <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="nama_event"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nama_event']); ?>"
                        required
                    >
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-muted">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($daftar_kategori as $kat) { ?>
                        <option value="<?= $kat; ?>" <?= ($data['kategori'] == $kat) ? 'selected' : ''; ?>>
                            <?= $kat; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-muted">Tanggal Event <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        name="tanggal_event"
                        class="form-control"
                        value="<?= $data['tanggal_event']; ?>"
                        required
                    >
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Lokasi / Tempat</label>
                    <input
                        type="text"
                        name="lokasi"
                        class="form-control"
                        value="<?= htmlspecialchars($data['lokasi']); ?>"
                    >
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-muted">Kuota Peserta</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-people"></i></span>
                        <input
                            type="number"
                            name="kuota"
                            class="form-control"
                            value="<?= $data['kuota']; ?>"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-muted">Biaya Masuk (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted">Rp</span>
                        <input
                            type="number"
                            name="biaya"
                            class="form-control"
                            value="<?= $data['biaya']; ?>"
                        >
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Deskripsi Kegiatan</label>
                    <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Link Grup (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-success"><i class="bi bi-whatsapp"></i></span>
                        <input
                            type="text"
                            name="link_grup"
                            class="form-control"
                            value="<?= htmlspecialchars($data['link_grup'] ?? ''); ?>"
                            placeholder="https://chat.whatsapp.com/..."
                        >
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">Informasi Peserta (Tampil Saat Peserta Diterima)</label>
                    <textarea
                        name="informasi_peserta"
                        class="form-control"
                        rows="4"
                        placeholder="Tulis informasi atau link penting khusus peserta yang lolos..."
                    ><?= htmlspecialchars($data['informasi_peserta'] ?? ''); ?></textarea>
                </div>

                <?php if (!empty($data['poster'])) { ?>
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label fw-bold small text-muted d-block">Poster Saat Ini</label>
                    <div class="p-2 border rounded bg-light text-center">
                        <img src="/event_kampus/uploads/<?= htmlspecialchars($data['poster']); ?>" class="img-fluid rounded shadow-sm" alt="Poster Event" style="max-height: 220px; object-fit: cover;">
                    </div>
                </div>
                <?php } ?>

                <div class="col-12 <?= !empty($data['poster']) ? 'col-sm-8 col-md-9 d-flex flex-column justify-content-end' : '' ?>">
                    <label class="form-label fw-bold small text-muted">Unggah File Poster Baru (Opsional)</label>
                    <input type="file" name="poster" class="form-control" accept="image/*">
                    <div class="form-text text-muted small mt-1">Biarkan kosong jika tidak ingin mengubah poster saat ini.</div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end pt-3 border-top mt-4">
                    <a href="index.php" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" name="update" class="btn btn-primary px-4 fw-medium" style="background-color: var(--theme-purple); border:none;">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>