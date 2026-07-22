<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_form = $_GET['id'];

$data = mysqli_fetch_assoc(
mysqli_query(
$conn,
"SELECT * FROM form_event
WHERE id_form='$id_form'"
)
);


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pertanyaan Form - HMJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

</head>
<body>

    <?php include "../sidebar.php"; ?>

    <div class="main-content">
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 fw-bold text-dark">Edit Field Form</h4>
            </div>
            <a href="tambah.php?id=<?= $data['id_event']; ?>" class="btn btn-sm btn-outline-secondary me-3 d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row justify-content-start">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card card-custom p-4 p-md-5">
                    <div class="border-bottom pb-3 mb-4">
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-pencil-square text-purple me-2"></i>Edit Detail Pertanyaan
                        </h5>
                        <p class="text-muted small mb-0">Perbarui pertanyaan atau tipe data formulir di bawah ini</p>
                    </div>

                    <form action="action.php" method="POST">
                        <input type="hidden" name="id_form" value="<?= $data['id_form']; ?>">
                        <input type="hidden" name="id_event" value="<?= $data['id_event']; ?>">

                        <div class="mb-4">
                            <label class="form-label">Pertanyaan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-question-circle"></i></span>
                                <input type="text" name="pertanyaan" class="form-control" value="<?= htmlspecialchars($data['pertanyaan']); ?>" placeholder="Masukkan pertanyaan..." required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipe Isian Data</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-sliders"></i></span>
                                <select name="tipe_input" class="form-select">
                                    <option value="text" <?= $data['tipe_input'] == 'text' ? 'selected' : ''; ?>>Text (Isian Pendek)</option>
                                    <option value="textarea" <?= $data['tipe_input'] == 'textarea' ? 'selected' : ''; ?>>Textarea (Isian Panjang)</option>
                                    <option value="file" <?= $data['tipe_input'] == 'file' ? 'selected' : ''; ?>>File (Berkas Umum)</option>
                                    <option value="image" <?= $data['tipe_input'] == 'image' ? 'selected' : ''; ?>>Gambar (Foto/Screenshot)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                            <a href="tambah.php?id=<?= $data['id_event']; ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" name="update" class="btn btn-primary px-4 fw-bold" style="background-color: var(--theme-purple); border:none;">
                                <i class="bi bi-check-lg me-1"></i> Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>