<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_event = (int) $_GET['id'];
$id_hmj   = $_SESSION['id_user'];

// Pastikan event ini milik HMJ yang login
$cek = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id_event FROM event
    WHERE id_event = '$id_event'
    AND   id_hmj   = '$id_hmj'
"));

if (!$cek) {
    header("Location: index.php");
    exit;
}

// Ambil semua id_pendaftaran dari event ini
$q_pend = mysqli_query($conn, "
    SELECT id_pendaftaran FROM pendaftaran
    WHERE id_event = '$id_event'
");

while ($pend = mysqli_fetch_assoc($q_pend)) {
    $id_pend = $pend['id_pendaftaran'];

    // 1. Hapus tiket
    mysqli_query($conn, "DELETE FROM tiket WHERE id_pendaftaran = '$id_pend'");

    // 2. Hapus pembayaran
    mysqli_query($conn, "DELETE FROM pembayaran WHERE id_pendaftaran = '$id_pend'");

    // 3. Hapus jawaban form
    mysqli_query($conn, "DELETE FROM jawaban_form WHERE id_pendaftaran = '$id_pend'");
}

// 4. Hapus semua pendaftaran event ini
mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_event = '$id_event'");

// 5. Hapus pertanyaan form event
mysqli_query($conn, "DELETE FROM form_event WHERE id_event = '$id_event'");

// 6. Hapus event
mysqli_query($conn, "DELETE FROM event WHERE id_event = '$id_event'");

header("Location: index.php");
exit;