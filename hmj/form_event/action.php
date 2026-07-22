<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

include "../../config/koneksi.php";

/* SIMPAN */

if(isset($_POST['simpan'])){

$id_event   = mysqli_real_escape_string($conn, $_POST['id_event']);
$pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
$tipe_input = mysqli_real_escape_string($conn, $_POST['tipe_input']);

mysqli_query($conn,"
INSERT INTO form_event
(
id_event,
pertanyaan,
tipe_input
)
VALUES
(
'$id_event',
'$pertanyaan',
'$tipe_input'
)
");

header("Location:tambah.php?id=".$id_event);
exit;
}

/* UPDATE */

if(isset($_POST['update'])){

$id_form = mysqli_real_escape_string($conn, $_POST['id_form']);
$id_event = mysqli_real_escape_string($conn, $_POST['id_event']);
$pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
$tipe_input = mysqli_real_escape_string($conn, $_POST['tipe_input']);

mysqli_query($conn,"
UPDATE form_event
SET
pertanyaan='$pertanyaan',
tipe_input='$tipe_input'
WHERE id_form='$id_form'
");

header("Location:tambah.php?id=".$id_event);
exit;
}

/* HAPUS */

if(isset($_GET['hapus'])){
    $id_form  = mysqli_real_escape_string($conn, $_GET['hapus']);
    $id_event = mysqli_real_escape_string($conn, $_GET['id_event']);

    // Hapus jawaban dulu sebelum hapus form
    mysqli_query($conn, "DELETE FROM jawaban_form WHERE id_form='$id_form'");

    // Baru hapus form
    mysqli_query($conn, "DELETE FROM form_event WHERE id_form='$id_form'");

    header("Location:tambah.php?id=".$id_event);
    exit;
}