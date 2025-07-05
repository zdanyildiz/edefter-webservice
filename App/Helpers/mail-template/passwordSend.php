<?php
// şifre göndereceğimiz bir eposta taslağı yapalım. Mobil görünüme uyumlu olmasına dikkat edelim

$passwordSendTemplate = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Gönderme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100px;
            height: auto;
        }
        .content {
            text-align: center;
            margin-bottom: 20px;
        }
        .content h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .content p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button {
            text-align: center;
            margin-bottom: 20px;
        }
        .button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        .footer p {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://globalpozitif.com.tr/m/r/logo/logo-GU7RQ.png" width="100px" height="75" alt="Global Pozitif">
        </div>
        <div class="content">
            <h1>Panel Giriş Şifreniz:</h1>
            <p>[password]</p>
        </div>
        <div class="button">
            Şifrenizi 5 dk içinde girmeniz gerekmektedir.
        </div>
        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen cevaplamayınız.</p>
        </div>
    </div>
</body>
</html>';