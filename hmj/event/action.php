<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

include "../../config/koneksi.php";

// simpan
if (isset($_POST['simpan'])) {

    $id_hmj            = $_SESSION['id_user'];
    $id_prodi          = !empty($_POST['id_prodi']) ? (int) $_POST['id_prodi'] : 'NULL';
    $nama_event        = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $kategori          = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi         = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi            = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $tanggal_event     = mysqli_real_escape_string($conn, $_POST['tanggal_event']);
    $kuota             = (int) $_POST['kuota'];
    $biaya             = (float) $_POST['biaya'];
    $link_grup         = mysqli_real_escape_string($conn, $_POST['link_grup'] ?? '');
    $informasi_peserta = mysqli_real_escape_string($conn, $_POST['informasi_peserta'] ?? '');
    $poster            = '';

    // up poster
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__, 2) . '/uploads/';
        $filename  = basename($_FILES['poster']['name']);
        $filename  = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
        $targetFile = $uploadDir . time() . '_' . $filename;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            $poster = mysqli_real_escape_string($conn, basename($targetFile));
        }
    }

    mysqli_query($conn, "
        INSERT INTO event
            (id_hmj, id_prodi, nama_event, kategori, deskripsi, lokasi,
             tanggal_event, kuota, biaya, poster, link_grup, informasi_peserta)
        VALUES
            ('$id_hmj', $id_prodi, '$nama_event', '$kategori', '$deskripsi', '$lokasi',
             '$tanggal_event', '$kuota', '$biaya', '$poster', '$link_grup', '$informasi_peserta')
    ");

    header("Location: index.php");
    exit;
}

// edit
if (isset($_POST['update'])) {

    $id_event          = (int) $_POST['id_event'];
    $id_hmj            = $_SESSION['id_user'];
    $id_prodi          = !empty($_POST['id_prodi']) ? (int) $_POST['id_prodi'] : 'NULL';
    $nama_event        = mysqli_real_escape_string($conn, $_POST['nama_event']);
    $kategori          = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi         = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi            = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $tanggal_event     = mysqli_real_escape_string($conn, $_POST['tanggal_event']);
    $kuota             = (int) $_POST['kuota'];
    $biaya             = (float) $_POST['biaya'];
    $link_grup         = mysqli_real_escape_string($conn, $_POST['link_grup'] ?? '');
    $informasi_peserta = mysqli_real_escape_string($conn, $_POST['informasi_peserta'] ?? '');

    $cek = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT id_event FROM event
        WHERE id_event = '$id_event' AND id_hmj = '$id_hmj'
    "));

    if (!$cek) {
        header("Location: index.php");
        exit;
    }

    $poster_sql = '';
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $uploadDir  = dirname(__DIR__, 2) . '/uploads/';
        $filename   = basename($_FILES['poster']['name']);
        $filename   = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
        $targetFile = $uploadDir . time() . '_' . $filename;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            $poster_baru = mysqli_real_escape_string($conn, basename($targetFile));

            // Hapus poster lama dari folder
            $old = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT poster FROM event WHERE id_event = '$id_event'
            "));
            if (!empty($old['poster']) && file_exists($uploadDir . $old['poster'])) {
                @unlink($uploadDir . $old['poster']);
            }

            $poster_sql = ", poster = '$poster_baru'";
        }
    }

    mysqli_query($conn, "
        UPDATE event SET
            id_prodi          = $id_prodi,
            nama_event        = '$nama_event',
            kategori          = '$kategori',
            deskripsi         = '$deskripsi',
            lokasi            = '$lokasi',
            tanggal_event     = '$tanggal_event',
            kuota             = '$kuota',
            biaya             = '$biaya',
            link_grup         = '$link_grup',
            informasi_peserta = '$informasi_peserta'
            $poster_sql
        WHERE id_event = '$id_event'
    ");

    header("Location: index.php");
    exit;
}