<?php
session_start();
include "config/koneksi.php";

$message = "";
$message_type = "";

if (isset($_POST['btn_reset'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $new_password = mysqli_real_escape_string($conn, trim($_POST['new_password']));
    
    // Cek apakah alamat email tersebut terdaftar di tabel users
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        // Jika email terdaftar, enkripsi password baru menggunakan algoritma BCRYPT (Standard Keamanan PHP)
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password baru ke database (CRUD - UPDATE)
        $update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        $eksekusi_update = mysqli_query($conn, $update_query);
        
        if ($eksekusi_update) {
            $message = "<strong>Berhasil!</strong> Kata sandi Anda telah berhasil diperbarui. Silakan kembali ke halaman login.";
            $message_type = "success";
        } else {
            $message = "<strong>Gagal!</strong> Terjadi kesalahan sistem saat memperbarui kata sandi.";
            $message_type = "danger";
        }
    } else {
        // Jika email mahasiswa tidak ditemukan
        $message = "<strong>Email tidak ditemukan!</strong> Pastikan email yang Anda masukkan sudah terdaftar.";
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - Portal Event FST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #6b21a8 0%, #9333ea 60%, #c026d3 100%);
            --mauve: #ffffff;
        }

        body {
            min-height: 100vh;
            margin: 0;

            display: flex;
            justify-content: center;
            align-items: center;

            font-family: 'Plus Jakarta Sans', sans-serif;
            color: white;

            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;

            background:
                linear-gradient(
                    rgba(107,33,168,0.7),
                    rgba(147,51,234,0.7)
                ),
                url('assets/img/background.jpg');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            filter: blur(4px);
            transform: scale(1.05);

            z-index: -1;
        }

        .reset-wrapper {
            width: 100%;
            max-width: 500px;

            background: rgba(255,255,255,0.08);

            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);

            border: 1px solid rgba(255,255,255,0.15);

            border-radius: 25px;

            padding: 40px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.2),
                0 0 40px rgba(147,51,234,0.15);

            position: relative;
            z-index: 10;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: white;

            display: flex;
            justify-content: center;
            align-items: center;

            overflow: hidden;
            border: 3px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .avatar-img {
            max-width: 120%;
            max-height: 120%;
            object-fit: contain;
        }

        .form-control {
            background: rgba(255,255,255,0.15);

            border: 1px solid rgba(255,255,255,0.25);

            border-radius: 50px;

            color: white;

            padding: 12px 20px;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .form-control:focus {
            background: rgba(255,255,255,0.2);
            color: white;
            border-color: rgba(255,255,255,0.4);
            box-shadow: none;
        }

        .btn-reset {
            background: transparent;
            border: 2px solid var(--mauve);
            color: white;
            border-radius: 50px;
            padding: 12px;
            width: 100%;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-reset:hover {
            background: var(--mauve);
            color: #6b21a8;
        }

        .form-label,
        .form-text,
        .text-muted {
            color: rgba(255,255,255,.8) !important;
        }

        a {
            color: var(--mauve);
            text-decoration: none;
        }

        a:hover {
            color: white;
        }
    </style>
</head>
<body>

<div class="reset-wrapper">

    <div class="avatar-circle">
        <img src="assets/img/logo.png" alt="Logo Event" class="avatar-img">
    </div>

    <h3 class="fw-bold text-center mb-1">
        RESET PASSWORD
    </h3>

    <p class="text-center mb-4" style="opacity:.8;">
        Masukkan email Anda dan buat kata sandi baru untuk mereset akun
    </p>
                    <!-- Notifikasi Status Penggantian Kata Sandi -->
                    <?php if($message != ""): ?>
                        <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                            <?= $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form Pengiriman Data (Self-Submission ke halaman yang sama) -->
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Terdaftar</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nama@mahasiswa.ac.id" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Kata Sandi Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="new_password" class="form-control" placeholder="Buat password baru" required>
                            </div>
                            <div id="passwordHelp" class="form-text text-muted small">Gunakan minimal 6 karakter unik.</div>
                        </div>
                        
                        <button type="submit" name="btn_reset" class="btn btn-warning w-100 py-2 rounded-3 fw-bold text-white shadow-sm mb-3">
                            <i class="bi bi-arrow-repeat me-2"></i>Reset Kata Sandi
                        </button>
                        
                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none small text-muted">
                                <i class="bi bi-chevron-left text-muted"></i> Kembali ke Halaman Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>