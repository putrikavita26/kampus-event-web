<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

include "../../config/koneksi.php";

$id_hmj = $_SESSION['id_user'];

$filter_status   = isset($_GET['status'])   ? $_GET['status']   : '';
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$where_status   = '';
$where_kategori = '';

if ($filter_status !== '') {
    $s = mysqli_real_escape_string($conn, $filter_status);
    $where_status = "AND pendaftaran.status = '$s'";
}

if ($filter_kategori !== '') {
    $k = mysqli_real_escape_string($conn, $filter_kategori);
    $where_kategori = "AND event.kategori = '$k'";
}

$nama_file = 'rekap_peserta';
if ($filter_kategori !== '') $nama_file .= '_' . strtolower(str_replace(' ', '_', $filter_kategori));
if ($filter_status !== '')   $nama_file .= '_' . strtolower($filter_status);
$nama_file .= '.xls';

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");

$query = mysqli_query($conn, "
    SELECT
        pendaftaran.id_pendaftaran,
        users.nama,
        mahasiswa.nim,
        prodi.nama_prodi,
        event.nama_event,
        pendaftaran.status AS status_pendaftaran,
        pendaftaran.tanggal_daftar,
        pembayaran.status  AS status_pembayaran

    FROM pendaftaran

    JOIN mahasiswa  ON pendaftaran.nim      = mahasiswa.nim
    JOIN users      ON mahasiswa.id_user    = users.id_user
    LEFT JOIN prodi ON users.id_prodi       = prodi.id_prodi
    JOIN event      ON pendaftaran.id_event = event.id_event
    LEFT JOIN pembayaran ON pendaftaran.id_pendaftaran = pembayaran.id_pendaftaran

    WHERE event.id_hmj = '$id_hmj'
    $where_status
    $where_kategori

    ORDER BY event.nama_event ASC, pendaftaran.tanggal_daftar DESC
");

// Ambil semua data peserta dulu
$semua_peserta = [];
while ($r = mysqli_fetch_assoc($query)) {
    $semua_peserta[] = $r;
}

// Ambil semua jawaban sekaligus 
$jawaban_map = [];
if (!empty($semua_peserta)) {
    $ids = implode(',', array_column($semua_peserta, 'id_pendaftaran'));
    $q_jawaban = mysqli_query($conn, "
        SELECT
            jawaban_form.id_pendaftaran,
            form_event.pertanyaan,
            jawaban_form.jawaban,
            jawaban_form.file_upload
        FROM jawaban_form
        JOIN form_event ON jawaban_form.id_form = form_event.id_form
        WHERE jawaban_form.id_pendaftaran IN ($ids)
    ");
    while ($j = mysqli_fetch_assoc($q_jawaban)) {
        $jawaban_map[$j['id_pendaftaran']][] = $j;
    }
}
?>

<table border="1">
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>NIM</th>
    <th>Prodi</th>
    <th>Event</th>
    <th>Tanggal Daftar</th>
    <th>Status Seleksi</th>
    <th>Status Pembayaran</th>
    <th>Jawaban Form</th>
</tr>

<?php $no = 1; foreach ($semua_peserta as $peserta) {
    $jawaban = '';
    if (isset($jawaban_map[$peserta['id_pendaftaran']])) {
        foreach ($jawaban_map[$peserta['id_pendaftaran']] as $j) {
            $isi = !empty($j['jawaban']) ? $j['jawaban'] : $j['file_upload'];
            $jawaban .= $j['pertanyaan'] . ': ' . $isi . ' | ';
        }
    }
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= htmlspecialchars($peserta['nama']); ?></td>
    <td><?= htmlspecialchars($peserta['nim']); ?></td>
    <td><?= htmlspecialchars($peserta['nama_prodi'] ?? '-'); ?></td>
    <td><?= htmlspecialchars($peserta['nama_event']); ?></td>
    <td><?= date('d-m-Y H:i', strtotime($peserta['tanggal_daftar'])); ?></td>
    <td><?= htmlspecialchars($peserta['status_pendaftaran']); ?></td>
    <td><?= !empty($peserta['status_pembayaran']) ? htmlspecialchars($peserta['status_pembayaran']) : '-'; ?></td>
    <td><?= htmlspecialchars($jawaban); ?></td>
</tr>
<?php } ?>
</table>