<?php

    $host     = 'localhost';
    $user     = 'root';
    $password = '';
    $database = 'assesment1';

    $koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die(mysqli_connect_error());
}

    mysqli_set_charset($koneksi, 'utf8');
?>