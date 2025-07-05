<?php

class Member {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function login($email, $password) {
        $sql = "
            SELECT
                * 
            FROM 
                uye 
            WHERE 
                uyeeposta = :email AND uyesifre = :password
        ";
        return $this->db->select($sql, ['email' => $email, 'password' => $password]);
    }

    public function registerWithCheckout($memberPostData)
    {
        $registerSql ="
            INSERT INTO 
                uye 
                (benzersizid, uyeolusturmatarih, uyeguncellemetarih, uyetip, uyetcno, memberTitle, uyead, uyesoyad, uyetelefon, uyeeposta, uyesifre, uyefaturaad, uyefaturavergidairesi, uyefaturavergino, uyeaktif, uyesil) 
            VALUES 
                (:benzersizid, :uyeolusturmatarih, :uyeguncellemetarih, :uyetip, :uyetcno, :memberTitle, :uyead, :uyesoyad, :uyetelefon, :uyeeposta, :uyesifre, :uyefaturaad, :uyefaturavergidairesi, :uyefaturavergino, :uyeaktif, :uyesil)
        ";


        return $this->db->insert($registerSql, $memberPostData);

    }

    public function register($memberInfo) {
        $sql = "
            INSERT INTO 
            uye 
                (benzersizid, uyeolusturmatarih, uyeguncellemetarih, uyetip, uyetcno, memberTitle, uyead, uyesoyad, uyetelefon, uyeeposta, uyesifre, uyeaktif, uyesil) 
            VALUES 
                (:benzersizid, :uyeolusturmatarih, :uyeguncellemetarih, :uyetip, :uyetcno, :memberTitle, :uyead, :uyesoyad, :uyetelefon, :uyeeposta, :uyesifre, :uyeaktif, :uyesil)";
        return $this->db->insert($sql, $memberInfo);
    }

    public function update($memberInfo)
    {
        $sql = "
            UPDATE 
                uye 
            SET 
                uyeguncellemetarih = :uyeguncellemetarih, 
                uyetcno = :uyetcno, 
                memberTitle = :memberTitle, 
                uyead = :uyead, 
                uyesoyad = :uyesoyad, 
                uyeeposta = :uyeeposta,
                uyetelefon = :uyetelefon,
                uyefaturaad = :uyefaturaad,
                uyefaturavergidairesi = :uyefaturavergidairesi,
                uyefaturavergino = :uyefaturavergino
            WHERE 
                uyeid = :uyeid
        ";
        return $this->db->update($sql, $memberInfo);
    }
    public function getMemberInfo($memberId=0, $uniqueId = null) {
        if ($memberId == 0 && $uniqueId == null) {
            return false;
        }
        if ($memberId == 0) {
            $sql = "SELECT * FROM uye WHERE benzersizid = :uniqueId";
            return $this->db->select($sql, ['uniqueId' => $uniqueId]);
        }
        $sql = "SELECT * FROM uye WHERE uyeid = :uyeid";
        return $this->db->select($sql, ['uyeid' => $memberId]);
    }

    public function updatePassword($memberInfo) {
        $sql = "
            UPDATE 
                uye 
            SET 
                uyesifre = :newPassword, uyeguncellemetarih = NOW()
            WHERE 
                uyeid = :memberID AND uyesifre = :password
        ";
        $params = [
            'memberID' => $memberInfo['memberID'],
            'password' => $memberInfo['password'],
            'newPassword' => $memberInfo['newPassword']
        ];
        $this->db->beginTransaction();
        $result = $this->db->update($sql, $memberInfo);
        if ($result > 0) {
            $this->db->commit();
            $returnData = [
                'status' => 'success',
                'message' => _uye_sifre_guncellendi
            ];
        }
        else {

            $this->db->rollBack();
            $returnData = [
                'status' => 'error',
                'message' => _uye_sifre_guncellenemedi
            ];
        }
        return $returnData;
    }

    public function updatePasswordByEmail($email, $newPassword){
        $sql = "
            UPDATE 
                uye 
            SET 
                uyesifre = :newPassword , uyeguncellemetarih = NOW()
            WHERE 
                uyeeposta = :eposta 
        ";
        $params = [
            'eposta' => $email,
            'newPassword' => $newPassword
        ];

        return $this->db->update($sql, $params);
    }

    public function addAddress($addressInfo) {
        $sql = "
                INSERT 
                    INTO 
                        uyeadres (uyeid, adresbaslik, adrestcno, adresad, adressoyad, adresulke, adressehir, adresilce, adressemt, adresmahalle, postakod, adresacik, adrestelefon, adresulkekod, adressil) 
                    VALUES 
                        (:uyeid, :adresbaslik, :adrestcno, :adresad, :adressoyad, :adresulke, :adressehir, :adresilce, :adressemt, :adresmahalle, :postakod, :adresacik, :adrestelefon, :adresulkekod, 0)
        ";
        $this->db->beginTransaction();
        $result = $this->db->insert($sql, $addressInfo);
        if ($result > 0) {
            $this->db->commit();
            return [
                'status' => 'success',
                'message' => _uye_adres_eklendi
            ];
        }
        $this->db->rollBack();
        return [
            'status' => 'error',
            'message' => _uye_adres_eklenemedi
        ];
    }

    public function updateAddress($addressInfo) {
        $sql = "
            UPDATE 
                uyeadres 
            SET 
                adresbaslik = :adresbaslik, adrestcno = :adrestcno, adresad = :adresad, adressoyad = :adressoyad, adresulke = :adresulke, adressehir = :adressehir, adresilce = :adresilce, 
                adressemt = :adressemt, adresmahalle = :adresmahalle, postakod = :postakod, adresacik = :adresacik, adrestelefon = :adrestelefon, adresulkekod = :adresulkekod 
            WHERE adresid = :adresid";
        $this->db->beginTransaction();
        $result = $this->db->update($sql, $addressInfo);
        if ($result > 0) {
            $this->db->commit();
            return [
                'status' => 'success',
                'message' => _uye_adres_guncellendi
            ];
        }
        $this->db->rollBack();
        return [
            'status' => 'error',
            'message' => _uye_adres_guncellenemedi
        ];
    }

    public function deleteAddress($addressInfo) {
        $sql = "UPDATE uyeadres SET adressil = 1 WHERE uyeid = :memberID AND adresid = :addressID";
        $this->db->beginTransaction();
        $result = $this->db->update($sql, $addressInfo);
        if ($result > 0) {
            $this->db->commit();
            return [
                'status' => 'success',
                'message' => _uye_adres_silindi
            ];
        }
        $this->db->rollBack();
        return [
            'status' => 'error',
            'message' => _uye_adres_silinemedi
        ];
    }

    public function getAddress($memberId) {
        $sql = "SELECT * FROM uyeadres WHERE uyeid = :uyeid AND adressil = 0";
        return $this->db->select($sql, ['uyeid' => $memberId]);
    }

    public function getAddressByID($memberID,$addressID) {
        $sql = "SELECT * FROM uyeadres WHERE uyeid = :uyeid AND adresid = :adresid";
        $response = $this->db->select($sql, ['uyeid' => $memberID, 'adresid' => $addressID]);
        if (!empty($response)) {
            return $response[0];
        }
        return [];
    }

    public function getOrders($memberId, $orderType="") {

        $where ="";

        if(!empty($orderType)){
            $where = " AND siparisdurum = $orderType";
        }

        $sql = "
            SELECT 
                *,uyesiparisdurum.siparisdurumbaslik 
            FROM 
                uyesiparis 
                inner join uyesiparisdurum on uyesiparis.siparisdurum = uyesiparisdurum.siparisdurumid
            WHERE 
                uyeid = :uyeid AND siparissil = 0 $where
            ORDER BY 
                siparistariholustur DESC
        ";
        return $this->db->select($sql, ['uyeid' => $memberId]);
    }

    public function getOrder($orderId) {
        $sql = "SELECT * FROM uyesiparis WHERE siparisid = :siparisid";
        return $this->db->select($sql, ['siparisid' => $orderId]);
    }

    public function getOrderByOrderUniqID($orderUniqId) {
        $sql = "SELECT * FROM uyesiparis WHERE siparisbenzersizid = :siparisid";
        return $this->db->select($sql, ['siparisid' => $orderUniqId]);
    }

    public function getOrderStatus($statusID){
        $sql = "SELECT * FROM uyesiparisdurum WHERE siparisdurumid = :statusID";
        return $this->db->select($sql, ['statusID' => $statusID]);
    }

    public function getMemberLastOrder($memberID)
    {
        $sql = "Select * From uyesiparis Where uyeid =:memberID ORDER BY siparisid Desc";
        return $this->db->select($sql, ['memberID' => $memberID]);

    }

    public function getMessages($memberId) {
        $sql = "SELECT * FROM sorusor WHERE uyeid = :uyeid AND mesajsil = '0' AND cevapid = '0'";
        $messages = $this->db->select($sql, ['uyeid' => $memberId]);

        if (!empty($messages)) {
            foreach ($messages as $key => $message) {
                $answer = $this->getMessageAnswer($message['mesajid']);
                if (!empty($answer)) {
                    $messages[$key]['answer'] = $answer;
                }
            }
        }

        return $messages;
    }

    public function getMessageAnswer($messageid){
        $sql = "SELECT * FROM sorusor WHERE cevapid = :messageid AND mesajsil = '0'";
        $answers = $this->db->select($sql, ['messageid' => $messageid]);

        if (!empty($answers)) {
            foreach ($answers as $key => $answer) {
                $subAnswer = $this->getMessageAnswer($answer['mesajid']);
                if (!empty($subAnswer)) {
                    $answers[$key]['subAnswer'] = $subAnswer;
                }
            }
        }

        return $answers;
    }

    public function getOrdersForCancellationRefundExchangeResponse($memberId) {

        $bugundenOnceki15Gun = date('Y-m-d', strtotime('-15 days'));

        $sql = "
            SELECT 
                *,uyesiparisdurum.siparisdurumbaslik 
            FROM 
                uyesiparis 
                inner join uyesiparisdurum on uyesiparis.siparisdurum = uyesiparisdurum.siparisdurumid
            WHERE 
                uyeid = :uyeid AND siparissil = 0 AND siparistariholustur >= :bugundenOnceki15Gun
            ORDER BY 
                siparistariholustur DESC
        ";
        return $this->db->select($sql, ['uyeid' => $memberId, 'bugundenOnceki15Gun' => $bugundenOnceki15Gun]);
    }

    public function getCancellationRefundExchangeResponse($talepid) {
        $sql = "SELECT talepid, siparisid, degisimtur, iadenedeni, iadeaciklama, urunid, tarih, cevapid FROM iptaliadedegisim WHERE talepsil = '0' AND cevapid = :talepid";
        $answers = $this->db->select($sql, ['talepid' => $talepid]);

        if (!empty($answers)) {
            foreach ($answers as $key => $answer) {
                $answers[$key]['subAnswer'] = $this->getCancellationRefundExchangeResponse($answer['talepid']);
            }
        }

        return $answers;
    }

    public function getCancellationRefundExchangeRequest($uyeid) {
        $sql = "SELECT talepid, siparisid, degisimtur, iadenedeni, iadeaciklama, urunid, tarih, cevapid FROM iptaliadedegisim WHERE talepsil = '0' AND cevapid = '0' AND uyeid = :uyeid ORDER BY talepid ASC";
        $requests = $this->db->select($sql, ['uyeid' => $uyeid]);

        if (!empty($requests)) {
            foreach ($requests as $key => $request) {
                $requests[$key]['answer'] = $this->getCancellationRefundExchangeResponse($request['talepid']);
            }
        }

        return $requests;
    }

    public function addCancellationRefundExchangeRequest($requestData)
    {
        /**
         * Table: iptaliadedegisim
         * Columns:
         * talepid int AI PK
         * uyeid int
         * siparisid varchar(20)
         * degisimtur varchar(20)
         * iadenedeni varchar(45)
         * iadeaciklama longtext
         * talepsil tinyint(1)
         * urunid varchar(100)
         * tarih datetime
         * cevapid int
         * talepbildirim tinyint(1)
         */
        $sql = "
            INSERT INTO iptaliadedegisim (uyeid, siparisid, degisimtur, iadenedeni, iadeaciklama, talepsil, urunid, tarih, cevapid, talepbildirim)
            values(:uyeid, :siparisid, :degisimtur, :iadenedeni, :iadeaciklama, 0, :urunid, :tarih, 0, 1) 
        ";
        $params = [
            'uyeid' => $requestData['uyeid'],
            'siparisid' => $requestData['siparisid'],
            'degisimtur' => $requestData['degisimtur'],
            'iadenedeni' => $requestData['iadenedeni'],
            'iadeaciklama' => $requestData['iadeaciklama'],
            'urunid' => $requestData['urunid'],
            'tarih' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert($sql, $params);
    }

    public function getFavorites($visitor_unique) {
        $sql = "
            SELECT 
                ziyaretcisayfabenzersiz as productUniqID
            FROM 
                ziyaretcisayfa
                INNER JOIN sayfa ON sayfa.benzersizid=ziyaretcisayfa.ziyaretcisayfabenzersiz
            WHERE 
                ziyaretcibenzersiz = :visitor_unique AND ziyaretcisayfabegeni = '1' AND sayfasil = '0' AND sayfaaktif = '1' AND sayfatip=7
            GROUP BY
                ziyaretcisayfabenzersiz
        ";
        return $this->db->select($sql, ['visitor_unique' => $visitor_unique]);
    }

    public function addFavorite($visitor_unique, $productUniqID) {
        $sql = "
            INSERT INTO 
                ziyaretcisayfa 
                (ziyaretcibenzersiz, ziyaretcisayfatarih, ziyaretcisayfabenzersiz, ziyaretcisayfabegeni, ziyaretcisayfapaylasim) 
            VALUES 
                (:visitor_unique, NOW(), :productUniqID, 1, 0)
        ";

        return $this->db->insert($sql, ['visitor_unique' => $visitor_unique, 'productUniqID' => $productUniqID]);
    }

    public function getFavoritesControl($visitorUniqID, $productUniqID)
    {
        $sql = "
            SELECT 
                ziyaretcisayfabegeni as pageFavorite
            FROM 
                ziyaretcisayfa
            WHERE 
                ziyaretcibenzersiz = :visitor_unique AND ziyaretcisayfabenzersiz = :productUniqID AND ziyaretcisayfabegeni = '1' 
        ";
        return $this->db->select($sql, ['visitor_unique' => $visitorUniqID, 'productUniqID' => $productUniqID]);
    }

    public function deleteFavorite($visitorUniqID, $productUniqID){
        $sql = "
            UPDATE 
                ziyaretcisayfa 
            SET 
                ziyaretcisayfabegeni = '0' 
            WHERE 
                ziyaretcibenzersiz = :visitor_unique AND ziyaretcisayfabenzersiz = :productUniqID AND ziyaretcisayfabegeni = '1'
        ";
        return $this->db->update($sql, ['visitor_unique' => $visitorUniqID, 'productUniqID' => $productUniqID]);
    }

    public function checkFavorite($visitor_unique, $productUniqID) {
        $sql = "
            SELECT 
                ziyaretcisayfabenzersiz as productUniqID
            FROM 
                ziyaretcisayfa
                INNER JOIN sayfa ON sayfa.benzersizid=ziyaretcisayfa.ziyaretcisayfabenzersiz
            WHERE 
                ziyaretcibenzersiz = :visitor_unique AND ziyaretcisayfabenzersiz = :productUniqID AND ziyaretcisayfabegeni = '1' AND sayfasil = '0' AND sayfaaktif = '1' AND sayfatip=7
            GROUP BY
                ziyaretcisayfabenzersiz
        ";

        return $this->db->select($sql, ['visitor_unique' => $visitor_unique, 'productUniqID' => $productUniqID]);
    }

    public function getVisitorPages($visitorUniqID, $pageUniqID)
    {
        $sql = "
            SELECT
                ziyaretcisayfabegeni as pageFavorite
            FROM
                ziyaretcisayfa
            WHERE
                ziyaretcibenzersiz = :visitor_unique AND ziyaretcisayfabenzersiz = :pageUniqID
        ";
        return $this->db->select($sql, ['visitor_unique' => $visitorUniqID, 'pageUniqID' => $pageUniqID]);
    }

    public function  updateFavoriteByProductUniqID($visitorUniqID, $productID)
    {
        $sql = "
            UPDATE
                ziyaretcisayfa
            SET
                ziyaretcisayfabegeni = '1'
            WHERE
                ziyaretcibenzersiz = :visitor_unique AND ziyaretcisayfabenzersiz = :productID
        ";
        return $this->db->update($sql, ['visitor_unique' => $visitorUniqID, 'productID' => $productID]);
    }

    public function getMemberInfoByEmail($email) {
        $sql = "
            SELECT 
                * 
            FROM 
                uye 
            WHERE 
                uyeeposta = :email and uyesil = 0
        ";

        return $this->db->select($sql, ['email' => $email]);
    }

    public function getMemberInfoByTelephone($telephone) {
        $sql = "
            SELECT 
                * 
            FROM 
                uye 
            WHERE 
                uyetelefon = :telephone and uyesil = 0
        ";

        return $this->db->select($sql, ['telephone' => $telephone]);
    }

    public function verificationCode($email, $userId) {
        $sql = "
            SELECT 
                * 
            FROM 
                uye 
            WHERE 
                uyeeposta = :email and benzersizid = :userId and uyesil = 0
        ";
        return $this->db->select($sql, ['email' => $email, 'userId' => $userId]);

    }

    public function updateMemberStatus($userId, $isActive)
    {
        $sql = "
            UPDATE 
                uye 
            SET 
                uyeaktif = :isActive
            WHERE 
                benzersizid = :userId
        ";
        return $this->db->update($sql, ['userId' => $userId, 'isActive' => $isActive]);
    }

    public function beginTransaction($funcName="") {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName="") {
        $this->db->commit($funcName);
    }

    public function rollBack($funcName="") {
        $this->db->rollBack($funcName);
    }
}