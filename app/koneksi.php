<?php
// Konfigurasi Database
$host = "db";
$user = "user";
$pass = "password";
$db   = "bakuliner_db";

// Koneksi ke database dengan error handling yang lebih baik
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Log error untuk debugging
    error_log("Database connection failed: " . mysqli_connect_error());
    die(json_encode([
        "error" => "Koneksi database gagal. Silakan coba lagi nanti.",
        "details" => mysqli_connect_error()
    ]));
}

// Set charset ke UTF-8 untuk support karakter Indonesia
mysqli_set_charset($conn, "utf8mb4");
?>
