<?php

session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$id_event = (int) $_POST['id_event'];

// Ambil nim mahasiswa
$mhs = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT nim FROM mahasiswa
    WHERE id_user = '" . $_SESSION['id_user'] . "'
"));

if (!$mhs) {
    die("Data mahasiswa tidak ditemukan.");
}

$nim = $mhs['nim'];

// Cek duplikat pendaftaran
$cek = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id_pendaftaran FROM pendaftaran
    WHERE id_event = '$id_event' AND nim = '$nim'
"));

if ($cek) {
    header("Location: event_saya.php");
    exit;
}

// Cek kuota masih tersedia
$event = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT kuota, biaya FROM event WHERE id_event = '$id_event'
"));

$terpakai = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS jml FROM pendaftaran
    WHERE id_event = '$id_event'
    AND status IN ('Menunggu', 'Diterima')
"));

if ($event['biaya'] > 0) {
    if (!isset($_FILES['bukti_bayar']) || empty($_FILES['bukti_bayar']['name'])) {
        header("Location: daftar.php?id=$id_event&error=Pilih bukti pembayaran terlebih dahulu.");
        exit;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext = strtolower(pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        header("Location: daftar.php?id=$id_event&error=Format bukti pembayaran harus JPG, PNG, atau PDF.");
        exit;
    }

    if ($_FILES['bukti_bayar']['size'] > 2 * 1024 * 1024) {
        header("Location: daftar.php?id=$id_event&error=Ukuran bukti pembayaran maksimal 2MB.");
        exit;
    }
}

if ($terpakai['jml'] >= $event['kuota']) {
    die("Maaf, kuota event sudah penuh.");
}

// Aturan validasi per tipe
$aturan = [
    'file'  => [
        'ekstensi' => ['pdf', 'doc', 'docx'],
        'max_size' => 5 * 1024 * 1024,
        'label'    => 'PDF/DOC/DOCX, maks 5MB',
    ],
    'image' => [
        'ekstensi' => ['jpg', 'jpeg', 'png'],
        'max_size' => 2 * 1024 * 1024,
        'label'    => 'JPG/PNG, maks 2MB',
    ],
];

// Validasi semua file sebelum insert apapun
$file_counter = 0;
foreach ($_POST['tipe_input'] as $key => $tipe) {

    if ($tipe == 'file' || $tipe == 'image') {

        $field = 'file_' . $file_counter;
        $rule  = $aturan[$tipe];

        if (empty($_FILES[$field]['name'])) {
            $pertanyaan_no = $key + 1;
            header("Location: daftar.php?id=$id_event&error=File pada pertanyaan ke-$pertanyaan_no wajib diisi.");
            exit;
        }

        $ekstensi = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $rule['ekstensi'])) {
            header("Location: daftar.php?id=$id_event&error=Format file harus " . $rule['label']);
            exit;
        }

        if ($_FILES[$field]['size'] > $rule['max_size']) {
            header("Location: daftar.php?id=$id_event&error=Ukuran file melebihi batas. Maks " . $rule['label']);
            exit;
        }

        $file_counter++;
    }
}

// Insert pendaftaran
mysqli_query($conn, "
    INSERT INTO pendaftaran (id_event, nim, status)
    VALUES ('$id_event', '$nim', 'Menunggu')
");

$id_pendaftaran = mysqli_insert_id($conn);

if ($event['biaya'] > 0) {
    $nama_bukti = time() . '_bukti_' . basename($_FILES['bukti_bayar']['name']);
    $tujuan = "../uploads/" . $nama_bukti;

    if (!move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $tujuan)) {
        die('Gagal mengunggah bukti pembayaran. Silakan coba lagi.');
    }

    $nama_bukti_esc = mysqli_real_escape_string($conn, $nama_bukti);
    mysqli_query($conn, "
        INSERT INTO pembayaran
        (id_pendaftaran, bukti_bayar, status)
        VALUES
        ('$id_pendaftaran', '$nama_bukti_esc', 'Menunggu')
    ");
}

// Simpan jawaban form
$file_counter = 0;

foreach ($_POST['id_form'] as $key => $id_form) {

    $id_form    = (int) $id_form;
    $tipe       = $_POST['tipe_input'][$key];
    $jawaban    = mysqli_real_escape_string($conn, $_POST['jawaban'][$key] ?? '');
    $file_upload = '';

    if ($tipe == 'file' || $tipe == 'image') {

        $field     = 'file_' . $file_counter;
        $nama_file = time() . '_' . $file_counter . '_' . basename($_FILES[$field]['name']);
        $tujuan    = "../uploads/" . $nama_file;

        if (move_uploaded_file($_FILES[$field]['tmp_name'], $tujuan)) {
            $file_upload = $nama_file;
        }

        $file_counter++;
    }

    $file_upload_esc = mysqli_real_escape_string($conn, $file_upload);

    mysqli_query($conn, "
        INSERT INTO jawaban_form
        (id_pendaftaran, id_form, jawaban, file_upload)
        VALUES
        ('$id_pendaftaran', '$id_form', '$jawaban', '$file_upload_esc')
    ");
}

header("Location: event_saya.php");
exit;