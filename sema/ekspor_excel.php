<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'SEMA') {
    exit("Akses ditolak");
}

// Menangkap filter yang dikirim dari laporan.php
$filter_hmj = isset($_GET['id_hmj']) ? mysqli_real_escape_string($conn, $_GET['id_hmj']) : '';
$where_clause = !empty($filter_hmj) ? "WHERE event.id_hmj = '$filter_hmj'" : "";

// Header wajib agar browser mendownload sebagai file .xls
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Event.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query data
$query = mysqli_query($conn, "
    SELECT event.nama_event, event.tanggal_event, event.status, users.nama AS nama_hmj, prodi.nama_prodi
    FROM event
    JOIN users ON event.id_hmj = users.id_user
    LEFT JOIN prodi ON users.id_prodi = prodi.id_prodi
    $where_clause
    ORDER BY event.tanggal_event DESC
");
?>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Penyelenggara (HMJ)</th>
            <th>Program Studi</th>
            <th>Nama Event</th>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while($row = mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['nama_hmj']; ?></td>
            <td><?= $row['nama_prodi']; ?></td>
            <td><?= $row['nama_event']; ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal_event'])); ?></td>
            <td><?= $row['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>