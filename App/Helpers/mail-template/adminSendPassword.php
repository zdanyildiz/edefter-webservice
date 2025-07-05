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
        .password{
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }
        .header,.content,.footer{max-width: 800px;float: none;margin: 0 auto;}
    </style>
</head>
<body>
<div class="header">
    <img src="[company-logo]" alt="[company-name]">
    <h1>[company-name]</h1>
</div>
<div class="content">
    <p>Merhaba [admin-name-surname],</p>
    <p>Site Yönetim Panelinize girmek istediğinizi belirtmek için bize bir istek gönderdiniz.</p>
    <p>Yeni şifreniz aşağıdaki gibidir:</p>
    <p class="password">[password]</p>
    <p>**Not:** Bu şifre 5 dakika sonra geçersiz hale gelecektir.</p>
</div>
<div class="footer">
    <img src="[company-logo]" alt="[company-name]">
    <p>[company-name] | [company-address] | [company-phone] | [company-email]</p>
</div>
</body>
</html>