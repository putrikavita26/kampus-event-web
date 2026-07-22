<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "event_kampus";

$conn = mysqli_connect($host, $user, $password, $database);

if(!$conn){
    die("Koneksi gagal");
}

?>