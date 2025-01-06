<?php
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username dan password harus diisi!";
        header("Location: index.html");
        exit();
    }
    
    $user = verify_login($username, $password);
    
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        
        // Redirect ke halaman home
        header("Location: /Home/");
        exit();
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header("Location: index.html");
        exit();
    }
}
?>