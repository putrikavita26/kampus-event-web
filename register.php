<?php
include "config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa - Portal Event FST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --cobalt: #0021B3;
            --azure: #007BFF;
            --ocean: #00BFFF;
            --sapphire: #0F52BA;
            --seafoam: #708090;
            --bg-gradient: linear-gradient(135deg, #6b21a8 0%, #9333ea 60%, #c026d3 100%);
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
            overflow-x: hidden;
            padding: 30px 15px;
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

        .register-wrapper {
            width: 100%;
            max-width: 550px;
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

        .register-card { 
            border: none; 
            border-radius: 25px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            background: #ffffff;
        }
        .btn-register {
            background: transparent;
            border: 2px solid white;
            color: white;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: white;
            color: #6b21a8;
        }
        .input-group-text {
            background-color: #f8f9fa;
            color: var(--azure);
            border-right: none;
        }
        .form-control,
        .form-select {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 50px;
            color: white;
            padding: 12px 20px;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255,255,255,0.2);
            color: white;
            border-color: rgba(255,255,255,0.4);
            box-shadow: none;
        }

        .form-select option {
            color: black;
        }
        .text-azure { color: var(--azure); }
        .text-ocean { color: var(--ocean); }
    </style>
</head>
<body>
<div class="register-wrapper">

    <div class="avatar-circle">
        <img src="assets/img/logo.png" alt="Logo" class="avatar-img">
    </div>

    <h3 class="fw-bold text-center mb-1">REGISTER</h3>
    <p class="text-center mb-4" style="opacity:.8;">
        CREATE YOUR EVENT CAMPUS ACCOUNT
    </p>
                    <form action="process/register_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white">NIM</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="nim" class="form-control" placeholder="Contoh: A11.2023.XXXXX" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="nama@mhs.kampus.ac.id" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="********" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-white">Program Studi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                                <select name="id_prodi" class="form-select" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM prodi ORDER BY nama_prodi ASC");
                                    while($data = mysqli_fetch_assoc($query)){
                                    ?>
                                    <option value="<?= $data['id_prodi']; ?>">
                                        <?= htmlspecialchars($data['nama_prodi']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register w-100 py-3 rounded-3 fw-bold shadow-sm mb-3">
                            <i class="bi bi-check-circle me-2"></i>Daftar Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <span class="text-white small">Sudah memiliki akun? </span>
                        <a href="login.php" class="text-decoration-none small text-warning fw-semibold">Login di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>