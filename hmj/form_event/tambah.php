<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_event = $_GET['id'];

$form = mysqli_query($conn,"
SELECT *
FROM form_event
WHERE id_event='$id_event'
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Form Event - HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
</head>
<body>

    <?php include "../sidebar.php"; ?>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Kelola Form Event</h4>
            </div>
            <a href="index.php" class="btn btn-sm btn-outline-secondary me-3 d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card card-custom p-4 sticky-lg-top" style="top: 30px; z-index: 10;">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-plus-circle-fill text-purple me-2"></i>Tambah Field Baru
                    </h5>
                    
                    <form action="action.php" method="POST">
                        <input type="hidden" name="id_event" value="<?= $id_event; ?>">

                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <input type="text" name="pertanyaan" class="form-control" placeholder="Contoh: Ukuran Baju / Link Drive" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipe Input Data</label>
                            <select name="tipe_input" class="form-select">
                                <option value="text">Text (Isian Pendek)</option>
                                <option value="textarea">Textarea (Isian Panjang)</option>
                                <option value="file">File (Berkas Umum)</option>
                                <option value="image">Gambar (Foto/Screenshot)</option>
                            </select>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary w-100 fw-bold" style="background-color: var(--theme-purple); border:none;">
                            <i class="bi bi-plus-lg me-1"></i> Tambahkan Field
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-list-check text-purple me-2"></i>Struktur Formulir Saat Ini
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3" style="width: 60px;">No</th>
                                    <th class="py-3">Pertanyaan</th>
                                    <th class="py-3" style="width: 150px;">Tipe Input</th>
                                    <th class="py-3 text-center" style="width: 160px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; while($data=mysqli_fetch_assoc($form)){ ?>
                                <tr>
                                    <td class="px-3 text-secondary fw-medium"><?= $no++; ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($data['pertanyaan']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace">
                                            <?= htmlspecialchars($data['tipe_input']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="edit.php?id=<?= $data['id_form']; ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="action.php?hapus=<?= $data['id_form']; ?>&id_event=<?= $id_event; ?>"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')"
                                               class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-trash3"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>

                                <?php if(mysqli_num_rows($form) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">Belum ada field form tambahan. Form hanya akan berisi field bawaan (Nama, NIM, Email, Prodi).</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>