<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[company-name] [subject]</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            background-color: #f3f3f3;
            max-width: 800px;
            float: none;
            margin: 0 auto;
        }
        .header {
            padding: 20px;
            text-align: center; background-color: aliceblue;
        }
        .header img {
            width: 100px;
        }
        .content {
            padding: 20px; background-color: #fff;
        }
        .footer {
            padding: 20px;
            text-align: center; background-color: antiquewhite;
        }
        .footer img {
            width: 50px;
        }
        a {
            color: cornflowerblue;
        }
        .content .footer{
            background-color: #f1f1f1;
        }
        h2{
            text-align: left;font-size: 1.2em;
        }
        li{text-align: left; line-height: 1.5;}
        .header,.content,.footer{max-width: 800px;float: none;margin: 0 auto;}
    </style>
</head>
<body>
<div class="header">
    <img src="[company-logo]" alt="[company-name]">
    <h1>[company-name]</h1>
</div>
<div class="content">
    <p>Merhaba [member-name-surname],</p>
    <p>Şifrenizi sıfırlanmıştır</p>
    <p>Yeni şifreniz: [password]</p>
    <p>Dilerseniz şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:</p>
    <a href="[password-reset-link]">[Şifre Sıfırlama Bağlantısı]</a>
    <div class="footer">
        <h2>Sorun yaşıyorsanız:</h2>
        <ul>
            <li><a href="#">Yardım merkezimizi ziyaret edin</a></li>
            <li><a href="#">Müşteri destek ekibimizle iletişime geçin</a></li>
        </ul>
    </div>

</div>
<div class="footer">
    <img src="[company-logo]" alt="[company-name]">
    <p>[company-name] | [company-address] | [company-phone] | [company-email]</p>
</div>
</body>
</html>
