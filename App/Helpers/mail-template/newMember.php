<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>[email-title]</title>
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
        <h1>[email-title]</h1>
        <p>[email-description]</p>
        <p><a href="[verificationLink]" class="button">[email-verification-button]</a></p>
        <p>[email-verification-button-description]</p>
        <p><a href="[verificationLink]">[verificationLink]</a></p>
        <p>[email-end-description]</p>
        <p class="footer">[company-name]</p>
    </div>
    <div class="footer">
        <img src="[company-logo]" alt="[company-name]">
        <p>[company-name] | [company-address] | [company-phone] | [company-email]</p>
    </div>
</body>
</html>