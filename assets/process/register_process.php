<?php
session_start();
include "../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Amankan data yang diinput pengguna
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nim      = mysqli_real_escape_string($conn, trim($_POST['nim']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $id_prodi = mysqli_real_escape_string($conn, $_POST['id_prodi']);

    // 2. Validasi: Cek apakah NIM atau Email sudah terdaftar sebelumnya
    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    $cek_nim   = mysqli_query($conn, "SELECT nim FROM mahasiswa WHERE nim = '$nim'");

    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah digunakan oleh akun lain!'); window.location='../register.php';</script>";
        exit;
    }
    if (mysqli_num_rows($cek_nim) > 0) {
        echo "<script>alert('NIM sudah terdaftar dalam sistem!'); window.location='../register.php';</script>";
        exit;
    }

    // 3. Kueri Pertama: Simpan ke tabel 'users' (Nama, Email, Password, Role, dan ID Prodi)
    $query_user = "INSERT INTO users (nama, email, password, role, id_prodi) 
                   VALUES ('$nama', '$email', '$password', 'MAHASISWA', '$id_prodi')";
    
    if (mysqli_query($conn, $query_user)) {
        // 4. Ambil id_user yang baru saja dibuat oleh sistem
        $id_user_baru = mysqli_insert_id($conn);

        // 5. Kueri Kedua: Simpan NIM dan id_user ke tabel 'mahasiswa'
        // Kolom angkatan, kelas, dan no_hp akan otomatis terisi NULL di database
        $query_mhs = "INSERT INTO mahasiswa (nim, id_user) VALUES ('$nim', '$id_user_baru')";
        
        if (mysqli_query($conn, $query_mhs)) {
            echo "<script>alert('Registrasi Mahasiswa Berhasil! Silakan Login.'); window.location='../login.php';</script>";
            exit;
        } else {
            // Jika kueri tabel mahasiswa gagal, hapus akun di tabel users agar data tidak pincang (rollback manual)
            mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id_user_baru'");
            echo "Gagal menyimpan data spesifik mahasiswa: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal membuat akun user: " . mysqli_error($conn);
    }
} else {
    header("Location: ../register.php");
    exit;
}
?>