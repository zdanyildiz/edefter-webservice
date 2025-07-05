<?php

/**
 * Table: yoneticiler
 * Columns:
 * yoneticiid int AI PK
 * yoneticianahtar varchar(20)
 * olusturmatarihi datetime
 * guncellemetarihi datetime
 * yoneticiyetki tinyint(1)
 * yoneticiadsoyad varchar(50)
 * yoneticieposta varchar(100)
 * yoneticiceptelefon varchar(50)
 * yoneticiresim char(255)
 * yoneticisifre char(5)
 * yoneticisifretarih datetime
 * yoneticipin int
 * yoneticiaktif tinyint(1)
 * yoneticisil tinyint(1)
 */
class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addAdmin($adminData) {
        $sql = "
            INSERT INTO 
                yoneticiler 
            SET 
                yoneticianahtar = :adminKey,
                olusturmatarihi = :createDate,
                guncellemetarihi = :updateDate,
                yoneticiyetki = :adminAuth,
                yoneticiadsoyad = :adminNameSurname,
                yoneticieposta = :adminEmail,
                yoneticiceptelefon = :adminPhone,
                yoneticiresim = :adminImage,
                yoneticisifre = :adminPassword,
                yoneticisifretarih = :adminPasswordDate,
                yoneticipin = :adminPIN,
                yoneticiaktif = :adminActive,
                yoneticisil = :adminDeleted
        ";

        $this->db->beginTransaction();

        $createAdmin = $this->db->insert($sql, $adminData);

        if ($createAdmin) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateAdmin($adminData)
    {
        $sql = "
            UPDATE 
                yoneticiler 
            SET 
                guncellemetarihi = :updateDate,
                yoneticiyetki = :adminAuth,
                yoneticiadsoyad = :adminNameSurname,
                yoneticieposta = :adminEmail,
                yoneticiceptelefon = :adminPhone,
                yoneticiresim = :adminImage,
                yoneticipin = :adminPIN,
                yoneticiaktif = :adminActive
            WHERE 
                yoneticiid = :adminID
        ";

        $this->db->beginTransaction();

        $updateAdmin = $this->db->update($sql, $adminData);

        if ($updateAdmin) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteAdmin($adminID) {
        $sql = "
            UPDATE 
                yoneticiler 
            SET 
                yoneticisil = 1
            WHERE 
                yoneticiid = :adminID
        ";

        $this->db->beginTransaction();

        $deleteAdmin = $this->db->update($sql, [
            'adminID' => $adminID
        ]);

        if ($deleteAdmin) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function updatePassword($adminID, $adminData) {
        // Yönetici güncelleme işlemleri
        $sql = "
            UPDATE 
                yoneticiler 
            SET 
                yoneticisifre = :yoneticisifre,
                yoneticisifretarih = :yoneticisifretarih
            WHERE 
                yoneticiid = :yoneticiid
        ";

        $this->db->beginTransaction();

        $updateAdmin = $this->db->update($sql, [
            'yoneticisifre' => $adminData['yoneticisifre'],
            'yoneticisifretarih' => $adminData['yoneticisifretarih'],
            'yoneticiid' => $adminID
        ]);

        if ($updateAdmin) {
            $this->db->commit();
            return true;
        } else {
            $this->db->rollBack();
            return false;
        }


    }

    public function login($adminEmailOrPhone, $adminPassword) {

        $sql = "
            SELECT 
                * 
            FROM 
                yoneticiler 
            WHERE 
                (yoneticieposta = :adminEmailOrPhone OR yoneticiceptelefon = :adminEmailOrPhone1) 
                AND 
                yoneticisifre = :adminPassword
        ";

        $adminResult = $this->db->select($sql, [
            'adminEmailOrPhone' => $adminEmailOrPhone,
            'adminEmailOrPhone1' => $adminEmailOrPhone,
            'adminPassword' => $adminPassword
        ]);

        if ($adminResult) {
            $admin = $adminResult[0];

            $adminIsActive = $admin['yoneticiaktif'];
            if ($adminIsActive == 0){
                return json_encode([
                    'status' => 'error',
                    'message' => 'Yönetici hesabınız aktif değil.'
                ]);
            }

            $adminIsDeleted = $admin['yoneticisil'];
            if ($adminIsDeleted == 1){
                return json_encode([
                    'status' => 'error',
                    'message' => 'Yönetici hesabınız silinmiş.'
                ]);
            }

            //yoneticisifretarih kontrolü yapalım, şifre üzerinden 5 dakika geçmişse hata dönelim

            $passwordUpdateDate = $admin['yoneticisifretarih'];
            $passwordUpdateDate = strtotime($passwordUpdateDate);
            $currentDate = strtotime(date('Y-m-d H:i:s'));
            $diff = $currentDate - $passwordUpdateDate;
            $diff = $diff / 60;
            if($diff > 5){
                return [
                    'status' => 'error',
                    'message' => 'Şifreniz gönderileli 5 dakikadan fazla olmuş. Lütfen tekrar şifre isteyiniz.'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Giriş başarılı.',
                'admin' => $admin
            ];

        } else {
            return [
                'status' => 'error',
                'message' => 'Şifre hatası.'
            ];
        }
    }

    public function checkAdmin($adminEmailOrPhone) {

        $sql = "
            SELECT 
                * 
            FROM 
                yoneticiler 
            WHERE 
                (yoneticieposta = :adminEmailOrPhone OR yoneticiceptelefon = :adminEmailOrPhone1) 
        ";

        $adminResult = $this->db->select($sql, [
            'adminEmailOrPhone' => $adminEmailOrPhone,
            'adminEmailOrPhone1' => $adminEmailOrPhone
        ]);

        if (!empty($adminResult)) {
            $admin = $adminResult[0];

            $adminIsActive = $admin['yoneticiaktif'];
            if ($adminIsActive == 0){
                return [
                    'status' => 'error',
                    'message' => 'Yönetici hesabınız aktif değil.'
                ];
            }

            $adminIsDeleted = $admin['yoneticisil'];
            if ($adminIsDeleted == 1){
                return [
                    'status' => 'error',
                    'message' => 'Yönetici hesabınız silinmiş.'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Giriş başarılı.',
                'admin' => $admin
            ];

        } else {
            return [
                'status' => 'error',
                'message' => 'Yönetici bulunamadı.'
            ];
        }
    }

    public function getAdmin($adminID)
    {
        $sql = "
            SELECT 
                yoneticiid as adminID,
                yoneticianahtar as adminKey,
                olusturmatarihi as createDate,
                guncellemetarihi as updateDate,
                yoneticiyetki as adminAuth,
                yoneticiadsoyad as adminNameSurname,
                yoneticieposta as adminEmail,
                yoneticiceptelefon as adminPhone,
                yoneticiresim as adminImage,
                yoneticisifre as adminPassword,
                yoneticisifretarih as adminPasswordDate,
                yoneticipin as adminPIN,
                yoneticiaktif as adminActive,
                yoneticisil as adminDeleted
            FROM 
                yoneticiler 
            WHERE 
                yoneticiid = :yoneticiid
        ";

        $adminResult = $this->db->select($sql, [
            'yoneticiid' => $adminID
        ]);

        if (!empty($adminResult)) {
            return $adminResult[0];
        } else {
            return [];
        }
    }

    public function getAdminWithPIN($adminID,$pin)
    {
        $sql = "
            SELECT 
                * 
            FROM 
                yoneticiler 
            WHERE 
                yoneticiid = :yoneticiid AND yoneticipin = :yoneticipin
        ";

        $adminResult = $this->db->select($sql, [
            'yoneticiid' => $adminID,
            'yoneticipin' => $pin
        ]);

        if (!empty($adminResult)) {
            return $adminResult[0];
        } else {
            return [];
        }
    }

    public function getAdmins(){
        $sql = "
            SELECT 
                yoneticiid as adminID,
                yoneticianahtar as adminKey,
                olusturmatarihi as createDate,
                guncellemetarihi as updateDate,
                yoneticiyetki as adminAuth,
                yoneticiadsoyad as adminNameSurname,
                yoneticieposta as adminEmail,
                yoneticiceptelefon as adminPhone,
                yoneticiresim as adminImage,
                yoneticisifre as adminPassword,
                yoneticisifretarih as adminPasswordDate,
                yoneticipin as adminPIN,
                yoneticiaktif as adminActive
            FROM 
                yoneticiler 
            WHERE 
                yoneticisil = 0
        ";

        $adminResult = $this->db->select($sql);

        if (!empty($adminResult)) {
            return $adminResult;
        } else {
            return [];
        }
    }

}