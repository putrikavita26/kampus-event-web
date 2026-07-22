<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    die("Akses ditolak: Anda bukan SEMA.");
}

// Cek apakah data ID dikirim
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';

    // Jalankan query update
    $update = mysqli_query($conn, "UPDATE event SET status = '$status' WHERE id_event = '$id'");

    if ($update) {
        header("Location: $redirect");
    } else {
        echo "Error Database: " . mysqli_error($conn); // Munculkan error jika query gagal
    }
} else {
    echo "ID Event tidak ditemukan.";
}
?>