<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            // Abonelik kontrolü
            if (new DateTime() > new DateTime($user['subscription_end'])) {
                $_SESSION['error'] = "Abonelik süreniz dolmuş. Lütfen yenileyin.";
                header('Location: login.php');
                exit;
            }

            // "Beni Hatırla" için token oluştur
            if ($remember) {
                $token = bin2hex(random_bytes(50));
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);

                setcookie('remember_me', $token, time() + (86400 * 30), "/"); // 30 gün
            }

            header('Location: index.php'); // Ana sayfa (berat.php)
            exit;
        } else {
            $error = "E-posta veya şifre yanlış!";
        }
    } catch (PDOException $e) {
        $error = "Giriş sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - Global Pozitif Teknolojiler</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 0; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .checkbox { margin-bottom: 15px; }
        .button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .button:hover { background-color: #0056b3; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Giriş Yap</h1>
    <div class="container">
        <?php if (isset($_SESSION['error'])) echo "<p class='error'>" . $_SESSION['error'] . "</p>"; unset($_SESSION['error']); ?>
        <?php if (isset($_GET['registered'])) echo "<p style='color:green;'>Kayıt başarılı! Giriş yapabilirsiniz.</p>"; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="checkbox">
                <label><input type="checkbox" name="remember"> Beni Hatırla</label>
            </div>
            <button type="submit" class="button">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
    </div>
</body>
</html>