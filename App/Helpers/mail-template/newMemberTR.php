<?php
$newMemberTemplate='
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Üyelik Doğrulama</title>
    <style>
        /* CSS kodları */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            font-size: 15px;
        }
        body a{
            color: #fff;
            font-size: 15px;
        }

        .ii a[href] {
            color: #fff;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #ccc;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        p {
            margin-top: 20px;
            color: #666;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Üyelik Doğrulama</h1>
        <p>Merhaba, [adsoyad] yeni üyelik işleminiz için teşekkür ederiz. Üyeliğinizi tamamlamak için aşağıdaki butona tıklayın:</p>
        <p><a href="[verificationLink]" class="button">Üyeliği Doğrula</a></p>
        <p>Eğer yukarıdaki butona tıklama imkanınız yoksa, aşağıdaki bağlantıyı tarayıcınıza yapıştırabilirsiniz:</p>
        <p><a href="[verificationLink]">[verificationLink]</a></p>
        <p>İyi günler dileriz!</p>
        <p class="footer">[companyShortName]</p>
    </div>
</body>
</html>

';
