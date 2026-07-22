<?php
session_start();
include "config/koneksi.php";
if(isset($_SESSION['id_user'])){
    if($_SESSION['role'] == 'SEMA') header("Location: sema/dashboard.php");
    elseif($_SESSION['role'] == 'HMJ') header("Location: hmj/dashboard.php");
    elseif($_SESSION['role'] == 'MAHASISWA') header("Location: mahasiswa/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Event FST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    rgba(76,29,149,0.65),
                    rgba(147,51,234,0.65)
                ),
                url('assets/img/background.jpg');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            filter: blur(4px);
            transform: scale(1.05);

            z-index: -1;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .avatar-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            margin-bottom: 20px;
            border-radius: 50%;
            background-color: #ffffff; /* warna background lingkaran */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 3px solid #e5e7eb;
        }

        .avatar-img {
            max-width: 120%;
            max-height: 120%;
            object-fit: contain;
        }

        .login-wrapper {
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
                0 0 40px rgba(168,85,247,0.25);

            position: relative;
            z-index: 10;

            margin: auto;
        }

        .login-container {
            text-align: center;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 12px 20px;
            color: white;
            text-align: center;
        }
        .form-control::placeholder { color: rgba(255, 255, 255, 0.7); }
        .btn-login {
            background: transparent;
            border: 2px solid var(--mauve);
            color: white;
            border-radius: 50px;
            padding: 10px 40px;
            width: 100%;
            transition: 0.3s;
            font-weight: 600;
        }
        .btn-login:hover {
            background: var(--mauve);
            color: #6b21a8;
        }
        a { color: var(--mauve); text-decoration: none; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-container">
        <div class="avatar-circle">
            <img src="assets/img/logo.png" alt="Profile" class="avatar-img">
        </div>
        <h3 class="fw-bold mb-1">LOGIN</h3>
        <p class="mb-4" style="opacity: 0.7;">WELCOME TO EVENT CAMPUS</p>

        <form action="process/login_process.php" method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="USER NAME" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="PASSWORD" required>
            </div>
            <div class="d-flex justify-content-between px-3 mb-4">
                <label><input type="checkbox"> Remember me</label>
                <a href="lupa_sandi.php" class="text-white">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-login mb-3">LOGIN</button>
            <div class="text-center mt-3">
                <span class="text-white small">Belum punya akun? </span>
                <a href="register.php" class="text-decoration-none small text-warning fw-semibold">Create Account</a>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
</body>
</html>