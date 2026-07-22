<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

if (isset($_GET['terima'])) {
    $id = (int) $_GET['terima'];

    // Verifikasi kepemilikan event
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT pendaftaran.id_pendaftaran
        FROM pendaftaran
        JOIN event ON pendaftaran.id_event = event.id_event
        WHERE pendaftaran.id_pendaftaran = '$id'
        AND event.id_hmj = '$id_hmj'
    "));

    if (!$cek) {
        header("Location: index.php");
        exit;
    }

    // Update status pendaftaran
    mysqli_query($conn, "
        UPDATE pendaftaran SET status='Diterima'
        WHERE id_pendaftaran='$id'
    ");

    header("Location: index.php");
    exit;
}

if (isset($_GET['tolak'])) {
    $id = (int) $_GET['tolak'];

    // Verifikasi kepemilikan event
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT pendaftaran.id_pendaftaran
        FROM pendaftaran
        JOIN event ON pendaftaran.id_event = event.id_event
        WHERE pendaftaran.id_pendaftaran = '$id'
        AND event.id_hmj = '$id_hmj'
    "));

    if (!$cek) {
        header("Location: index.php");
        exit;
    }

    // Update status pendaftaran
    mysqli_query($conn, "
        UPDATE pendaftaran SET status='Ditolak'
        WHERE id_pendaftaran='$id'
    ");

    header("Location: index.php");
    exit;
}