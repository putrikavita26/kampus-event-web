<?php
session_start();
include "../config/koneksi.php";

if (isset($_POST['update_profil'])) {
    $id_user      = $_SESSION['id_user'];
    $role         = $_SESSION['role'];
    $nama         = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password_lama = trim($_POST['password_lama'] ?? '');
    $password_baru = trim($_POST['password_baru'] ?? '');
    $password_baru2 = trim($_POST['password_baru2'] ?? '');

    // Cek apakah email baru sudah dipakai oleh user lain
    $cek_email = mysqli_query($conn, "SELECT id_user FROM users WHERE email = '$email' AND id_user != '$id_user'");
    if (mysqli_num_rows($cek_email) > 0) {
        $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
        echo "<script>alert('Email sudah digunakan oleh akun lain!'); window.location='$redirect';</script>";
        exit;
    }

    // Pastikan kolom foto ada sebelum digunakan
    $foto_column = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'foto'");
    if ($foto_column && mysqli_num_rows($foto_column) === 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN foto VARCHAR(255) NULL");
    }

    // Ambil data pengguna untuk validasi password dan foto lama
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password, foto FROM users WHERE id_user = '$id_user'"));
    if (!$user) {
        die('Pengguna tidak ditemukan.');
    }

    $update_fields = [
        "nama = '$nama'",
        "email = '$email'"
    ];

    // Ganti password jika diminta
    if ($password_baru !== '' || $password_baru2 !== '') {
        if ($password_baru !== $password_baru2) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Password baru dan konfirmasi tidak cocok!'); window.location='$redirect';</script>";
            exit;
        }

        if ($password_lama === '') {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Masukkan password lama untuk mengganti password.'); window.location='$redirect';</script>";
            exit;
        }

        $login_valid = password_verify($password_lama, $user['password']) || $password_lama === $user['password'];
        if (!$login_valid) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Password lama tidak cocok.'); window.location='$redirect';</script>";
            exit;
        }

        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $update_fields[] = "password = '$password_hash'";
    }

    // Upload foto profil jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $fileError = $_FILES['foto']['error'];
        if ($fileError !== UPLOAD_ERR_OK) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            $message = 'Gagal mengunggah foto profil.';
            switch ($fileError) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message = 'Ukuran foto melebihi batas maksimum.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = 'File hanya terunggah sebagian. Coba lagi.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = 'Folder sementara tidak ditemukan di server.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = 'Gagal menyimpan file di server.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = 'Upload dibatalkan oleh ekstensi server.';
                    break;
            }
            echo "<script>alert('$message'); window.location='$redirect';</script>";
            exit;
        }

        $tmp_name = $_FILES['foto']['tmp_name'];
        if (!is_uploaded_file($tmp_name)) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('File foto tidak valid.'); window.location='$redirect';</script>";
            exit;
        }

        $original_name = basename($_FILES['foto']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed_ext)) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Format foto harus JPG, JPEG, atau PNG.'); window.location='$redirect';</script>";
            exit;
        }

        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Ukuran foto maksimal 2MB.'); window.location='$redirect';</script>";
            exit;
        }

        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Gagal membuat folder upload.'); window.location='$redirect';</script>";
            exit;
        }

        $foto_name = 'profile_' . $id_user . '_' . time() . '.' . $ext;
        $target = $upload_dir . $foto_name;

        if (!move_uploaded_file($tmp_name, $target)) {
            $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
            echo "<script>alert('Gagal mengunggah foto profil. Coba lagi.'); window.location='$redirect';</script>";
            exit;
        }

        if (!empty($user['foto'])) {
            $old_path = $upload_dir . $user['foto'];
            if (file_exists($old_path)) {
                @unlink($old_path);
            }
        }

        $update_fields[] = "foto = '$foto_name'";
    }

    if ($role === 'MAHASISWA') {
        $id_prodi = mysqli_real_escape_string($conn, $_POST['id_prodi']);
        $angkatan = mysqli_real_escape_string($conn, trim($_POST['angkatan']));
        $kelas    = mysqli_real_escape_string($conn, trim($_POST['kelas']));
        $no_hp    = mysqli_real_escape_string($conn, trim($_POST['no_hp']));

        $update_fields[] = "id_prodi = '$id_prodi'";
    }

    $update_query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id_user = '$id_user'";
    $success = mysqli_query($conn, $update_query);

    if ($success && $role === 'MAHASISWA') {
        $update_mhs  = "UPDATE mahasiswa SET 
                        angkatan = ".($angkatan ? "'$angkatan'" : "NULL").", 
                        kelas = ".($kelas ? "'$kelas'" : "NULL").", 
                        no_hp = ".($no_hp ? "'$no_hp'" : "NULL")." 
                        WHERE id_user = '$id_user'";
        $success = $success && mysqli_query($conn, $update_mhs);
    }

    if ($success) {
        $_SESSION['nama'] = $nama;
        $redirect = ($role === 'HMJ') ? '../hmj/profil.php' : '../mahasiswa/profil.php';
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='$redirect';</script>";
        exit;
    } else {
        echo "Gagal memperbarui profil: " . mysqli_error($conn);
    }
} else {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'HMJ') {
        header("Location: ../hmj/profil.php");
    } else {
        header("Location: ../mahasiswa/profil.php");
    }
    exit;
}
?>