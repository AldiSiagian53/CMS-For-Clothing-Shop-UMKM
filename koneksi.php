<?php
session_start();

// Konfigurasi database
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'advcshop');
define('DB_PASSWORD', 'osttamvan123');
define('DB_NAME', 'advcshop_user');

// Mencoba melakukan koneksi ke database
try {
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Set karakter encoding
    mysqli_set_charset($conn, "utf8mb4");
    
} catch(Exception $e) {
    die("ERROR: Tidak dapat terhubung ke database. " . mysqli_connect_error());
}

// Fungsi untuk membersihkan input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Fungsi untuk verifikasi login
function verify_login($username, $password) {
    global $conn;
    
    $username = clean_input($username);
    $password = clean_input($password);
    
    // Gunakan prepared statement
    $stmt = mysqli_prepare($conn, "SELECT id, username FROM pembeli WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        return $user;
    }
    return false;
}
?>