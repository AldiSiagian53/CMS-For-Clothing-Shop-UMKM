<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADVC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <!--Header-->
    <div class="header">
        <div class="logo">
            <a href="">ADVC</a>
        </div>
        <div class="nav">
            <a href="/Project PBL Kelompok 3/Home/" onclick="showAlert(event)">Home</a>
            <a href="/About us/" onclick="showAlert(event)">About Us</a>
            <a href="/Project PBL Kelompok 3/Produk/"onclick="showAlert(event)">Product</a>
        </div>
        <div class="icons">
        </div>
    </div>

    <!-- Pesan Error/Sukses -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="main">
        <div class="text">
            <p>Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>!</p>
            <h1>Browse our latest Hoodie and Crewneck products</h1>
            <div class="buttons">
                <button class="register" id="register">Register</button>  
                <button class="login" id="login">Login</button>

                <!--Form Login-->
                <div class="overlay2"></div>
                <div class="container-login">
                    <div class="login-header">
                        <h2>Login</h2>
                        <form action="login.php" method="POST" id="loginForm">
                        <div class="close-btn">&times;</div>
                    </div>
                    <div class="form-group">
                        <label for="login-username">Username</label>
                        <input type="text" name="username" id="login-username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" name="password" id="login-password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="submit-btn">Login</button>
                    <div class="forgot-password">
                        <a href="#">Forgot Password?</a>
                    </div>
                    <button type="button" class="register-btn">Register</button>
                </form>
                </div>

                <!--Form Register-->
                <div class="overlay"></div>
                <div class="container-register">
                    <div class="register-header">
                        <h2>Register</h2>
                        <form action="register.php" method="POST" id="registerForm">
                        <div class="close-btn2">&times;</div>
                    </div>
                    <div class="form-group">
                        <label for="reg-username">Username</label>
                        <input type="text" name="username" id="reg-username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-password">Password</label>
                        <input type="password" name="password" id="reg-password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-confirm-password">Confirm Password</label>
                        <input type="password" id="reg-confirm-password" placeholder="Confirm Password" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="no_telp">No. Telepon</label>
                        <input type="tel" name="no_telp" id="no_telp" placeholder="No. Telepon">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" placeholder="Alamat"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Register</button>
                </form>
                    <div class="login-link">
                        <a href="#">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan script validasi -->
    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        var password = document.getElementById('reg-password').value;
        var confirmPassword = document.getElementById('reg-confirm-password').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password dan Confirm Password harus sama!');
        }
    });

    // Tampilkan pesan error/success
    <?php if(isset($_SESSION['error']) || isset($_SESSION['success'])): ?>
    setTimeout(function() {
        var alerts = document.getElementsByClassName('alert');
        for(var i = 0; i < alerts.length; i++) {
            alerts[i].style.display = 'none';
        }
    }, 3000);
    <?php endif; ?>
    </script>

    <script src="script.js"></script>
</body>
</html>