<?php

session_start();
include "../config/koneksi.php";

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

$query = mysqli_query($conn,"
SELECT *
FROM users
WHERE email='$email'
");

if(mysqli_num_rows($query) == 0){

    echo "
    <script>
        alert('Email tidak ditemukan');
        window.location='../login.php';
    </script>
    ";
    exit;
}

$user = mysqli_fetch_assoc($query);

// password lama dan baru bisa sama, karena kita akan otomatis mengubah ke hash setelah login berhasil

$login_berhasil = false;

if(password_verify($password, $user['password'])){

    $login_berhasil = true;

}
elseif($password == $user['password']){

    $login_berhasil = true;

    // otomatis ubah ke hash setelah login berhasil

    $password_baru = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    mysqli_query($conn,"
    UPDATE users
    SET password='$password_baru'
    WHERE id_user='".$user['id_user']."'
    ");
}

if($login_berhasil){

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    if($user['role'] == 'SEMA'){

        header("Location: ../sema/dashboard.php");

    }elseif($user['role'] == 'HMJ'){

        header("Location: ../hmj/dashboard.php");

    }elseif($user['role'] == 'MAHASISWA'){

        header("Location: ../mahasiswa/dashboard.php");

    }

    exit;

}else{

    echo "
    <script>
        alert('Password Salah');
        window.location='../login.php';
    </script>
    ";
}