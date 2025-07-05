<?php
class FTPClient
{
    private $connection;

    public function __construct($host, $user, $password)
    {
        $this->connection = ftp_connect($host);
        if (!$this->connection) {
            throw new Exception("FTP sunucusuna bağlanılamadı: $host");
        }

        $loginResult = ftp_login($this->connection, $user, $password);
        if (!$loginResult) {
            throw new Exception("FTP giriş başarısız: $user");
        }

        ftp_pasv($this->connection, true); // Pasif modu etkinleştir
    }

    public function uploadFile($localFilePath, $remoteFilePath)
    {
        //klasör yoksa oluştur
        $path = pathinfo($remoteFilePath, PATHINFO_DIRNAME);
        if (!@ftp_chdir($this->connection, $path)) {
            $dirs = explode('/', $path);
            foreach ($dirs as $dir) {
                if (!@ftp_chdir($this->connection, $dir)) {
                    @ftp_mkdir($this->connection, $dir);
                    @ftp_chdir($this->connection, $dir);
                }
            }
        }
        if (@ftp_put($this->connection, $remoteFilePath, $localFilePath, FTP_BINARY)) {
            return "$localFilePath dosyası başarıyla yüklendi.<br>";
        } else {
            Log::write("FTP: $localFilePath dosyası yüklenemedi.", "error");
            return false;
        }
    }

    public function deleteFile($filePath)
    {
        if (ftp_delete($this->connection, $filePath)) {
            return "$filePath başarıyla silindi.";
        } else {
            throw new Exception("$filePath silinemedi.");
        }
    }

    public function deleteAllFilesFTP($directory)
    {
        $files = ftp_nlist($this->connection, $directory);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $filePath = $directory . '/' . $file;

            // Dosya mı dizin mi kontrol et
            if (@ftp_chdir($this->connection, $filePath)) {
                // Dizin ise, içeriğini sil ve dizini kaldır
                ftp_chdir($this->connection, '..');
                $this->deleteAllFilesFTP($filePath);
                ftp_rmdir($this->connection, $filePath);
            } else {
                // Dosya ise, sil
                ftp_delete($this->connection, $filePath);
            }
        }
    }

    public function close()
    {
        ftp_close($this->connection);
    }
}
