<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Üye Kaydı Bildirimi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .member-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .member-info h3 {
            color: #28a745;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .admin-link {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .admin-link:hover {
            background-color: #0056b3;
        }
        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Yeni Üye Kaydı</h1>
            <p>Sisteme yeni bir üye kaydı yapıldı</p>
        </div>

        <div class="content">
            <div class="alert">
                <strong>Bilgilendirme:</strong> [company-name] sistemine yeni bir üye kaydı gerçekleştirildi. Aşağıdaki bilgileri kontrol edebilirsiniz.
            </div>

            <div class="member-info">
                <h3>👤 Üye Bilgileri</h3>
                
                <div class="info-item">
                    <span class="info-label">Ad Soyad:</span>
                    <span class="info-value">[member-name]</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">E-posta:</span>
                    <span class="info-value">[member-email]</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Telefon:</span>
                    <span class="info-value">[member-phone]</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Kayıt Tarihi:</span>
                    <span class="info-value">[registration-date]</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Durum:</span>
                    <span class="info-value">E-posta doğrulaması bekleniyor</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="#" class="admin-link">🔧 Admin Panele Git</a>
            </div>

            <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                <strong>Not:</strong> Yeni üye henüz e-posta doğrulamasını tamamlamamıştır. 
                E-posta doğrulandıktan sonra aktif hale gelecektir.
            </p>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak sistem tarafından gönderilmiştir.</p>
            <p><strong>[company-name]</strong> - Üye Yönetim Sistemi</p>
            <hr style="border: none; border-top: 1px solid #dee2e6; margin: 15px 0;">
            <p style="font-size: 12px;">
                Bu bildirim e-postasını almak istemiyorsanız, sistem ayarlarından değiştirebilirsiniz.
            </p>
        </div>
    </div>
</body>
</html>
