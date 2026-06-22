<?php defined('SECURE_ACCESS') or die(header("Location: index.php?action=login")); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental AM - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo-container">
                <img src="assets/images/logo.svg" alt="Rental AM Logo" class="login-logo">
            </div>
            <h1 class="login-title">RENTAL AM</h1>
            <p class="login-subtitle">Sistem Informasi Manajemen Rental Kendaraan</p>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=login" method="POST">
                <div class="form-group">
                    <label for="username"><i class="fa-solid fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk Ke Sistem
                </button>
            </form>
        </div>
    </div>
</body>
</html>
