<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Şifreyi bcrypt ile hash'le
    $subscription_end = date('Y-m-d H:i:s', strtotime('+1 year')); // 1 yıllık abonelik

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, subscription_end) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $subscription_end]);
        header('Location: login.php?registered=true');
        exit;
    } catch (PDOException $e) {
        $error = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - Global Pozitif Teknolojiler</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 0; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .button:hover { background-color: #0056b3; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Kayıt Ol</h1>
    <div class="container">
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Kayıt Ol</button>
        </form>
        <p>Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
    </div>
</body>
</html>