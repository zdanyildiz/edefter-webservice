<?php

require '../vendor/autoload.php';

use phpseclib3\Net\SSH2;

function executeGitClone($serverIp, $username, $password, $repositoryUrl, $targetDirectory)
{
    try {
        // SSH bağlantısı oluştur
        $ssh = new SSH2($serverIp);

        // Sunucuya giriş yap
        if (!$ssh->login($username, $password)) {
            throw new Exception('SSH oturumu açılamadı. Lütfen kullanıcı adı ve şifrenizi kontrol edin.');
        }

        // Git klonlama komutunu çalıştır
        $gitCloneCommand = "git clone $repositoryUrl $targetDirectory";
        $output = $ssh->exec($gitCloneCommand);

        // Çıktıları kontrol et
        if (strpos($output, 'fatal') !== false) {
            throw new Exception("Git klonlama sırasında hata oluştu: $output");
        }

        echo "Git klonlama işlemi başarıyla tamamlandı:\n";
        echo $output;

    } catch (Exception $e) {
        echo "Bir hata oluştu: " . $e->getMessage() . "\n";
    }
}

// Sunucu ve bağlantı bilgileri
$serverIp = getenv('SERVER_IP') ?: ''; // Sunucu IP adresi
$username = getenv('SSH_USERNAME') ?: ''; // SSH kullanıcı adı
$password = getenv('SSH_PASSWORD') ?: ''; // SSH şifresi

// Git deposu ve hedef dizin bilgileri
$repositoryUrl = 'https://' . getenv('GITHUB_TOKEN') . '@github.com/zdanyildiz/eticaret.git';
$domain = 'kiyafet.pozitifeticaret.com'; // Kullanıcı tarafından belirtilen domain
$targetDirectory = "/var/www/vhosts/$domain/httpdocs";

// Git klonlama işlemini çalıştır
executeGitClone($serverIp, $username, $password, $repositoryUrl, $targetDirectory);
