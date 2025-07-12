<?php

/**
 * Table: uye
 * Columns:
 * uyeid int AI PK
 * benzersizid char(20)
 * uyeolusturmatarih datetime
 * uyeguncellemetarih datetime
 * uyetip tinyint(1)
 * uyetcno char(11)
 * uyead varchar(100)
 * uyesoyad varchar(100)
 * uyeeposta varchar(100)
 * uyesifre varchar(100)
 * uyetelefon varchar(50)
 * uyeaciklama varchar(255)
 * uyefaturaad varchar(255)
 * uyefaturavergidairesi varchar(255)
 * uyefaturavergino varchar(12)
 * uyeaktif tinyint(1)
 * uyesil tinyint(1)
 */

/**
 * Table: uyeadres
 * Columns:
 * adresid int AI PK
 * uyeid int
 * adresbaslik varchar(50)
 * adrestcno char(11)
 * adresad varchar(50)
 * adressoyad varchar(50)
 * adresulke varchar(50)
 * adressehir varchar(50)
 * adresilce varchar(50)
 * adressemt varchar(50)
 * adresmahalle varchar(50)
 * postakod varchar(10)
 * adresacik varchar(255)
 * adrestelefon varchar(10)
 * adresulkekod varchar(3)
 * adressil tinyint
 */
class AdminMember {
    private $db;

    public function __construct(AdminDatabase $db) {
        $this->db = $db;
    }

    public function addMember($memberInfo) {
        $sql = "
            INSERT INTO 
            uye 
                (benzersizid, uyeolusturmatarih, uyeguncellemetarih, uyetip, uyetcno, uyead, uyesoyad, uyeeposta, uyesifre, uyetelefon, uyeaciklama, uyefaturaad, uyefaturavergidairesi, uyefaturavergino, uyeaktif, uyesil) 
            VALUES 
                (:benzersizid, :uyeolusturmatarih, :uyeguncellemetarih, :uyetip, :uyetcno, :uyead, :uyesoyad, :uyeeposta, :uyesifre, :uyetelefon, :uyeaciklama, :uyefaturaad, :uyefaturavergidairesi, :uyefaturavergino, :uyeaktif, :uyesil)";
        $params = [
            'benzersizid' => $memberInfo['memberUniqID'],
            'uyeolusturmatarih' => $memberInfo['memberCreateDate'],
            'uyeguncellemetarih' => $memberInfo['memberUpdateDate'],
            'uyetip' => $memberInfo['memberType'],
            'uyetcno' => $memberInfo['memberIdentityNo'],
            'uyead' => $memberInfo['memberName'],
            'uyesoyad' => $memberInfo['memberSurname'],
            'uyeeposta' => $memberInfo['memberEmail'],
            'uyesifre' => $memberInfo['memberPassword'],
            'uyetelefon' => $memberInfo['memberPhone'],
            'uyeaciklama' => $memberInfo['memberDescription'],
            'uyefaturaad' => $memberInfo['memberInvoiceName'],
            'uyefaturavergidairesi' => $memberInfo['memberInvoiceTaxOffice'],
            'uyefaturavergino' => $memberInfo['memberInvoiceTaxNumber'],
            'uyeaktif' => $memberInfo['memberActive'],
            'uyesil' => 0
        ];
        return $this->db->insert($sql, $params);
    }

    public function updateMember($memberInfo)
    {
        $sql = "
            UPDATE 
                uye 
            SET 
                uyeguncellemetarih = :uyeguncellemetarih,
                uyetip = :uyetip,
                uyetcno = :uyetcno, 
                uyead = :uyead, 
                uyesoyad = :uyesoyad, 
                uyeeposta = :uyeeposta,
                uyesifre = :uyesifre,
                uyetelefon = :uyetelefon,
                uyeaciklama = :uyeaciklama,
                uyefaturaad = :uyefaturaad,
                uyefaturavergidairesi = :uyefaturavergidairesi,
                uyefaturavergino = :uyefaturavergino,
                uyeaktif = :uyeaktif
            WHERE 
                uyeid = :uyeid
        ";
        $params = [
            'uyeguncellemetarih' => $memberInfo['memberUpdateDate'],
            'uyetip' => $memberInfo['memberType'],
            'uyetcno' => $memberInfo['memberIdentityNo'],
            'uyead' => $memberInfo['memberName'],
            'uyesoyad' => $memberInfo['memberSurname'],
            'uyeeposta' => $memberInfo['memberEmail'],
            'uyesifre' => $memberInfo['memberPassword'],
            'uyetelefon' => $memberInfo['memberPhone'],
            'uyeaciklama' => $memberInfo['memberDescription'],
            'uyefaturaad' => $memberInfo['memberInvoiceName'],
            'uyefaturavergidairesi' => $memberInfo['memberInvoiceTaxOffice'],
            'uyefaturavergino' => $memberInfo['memberInvoiceTaxNumber'],
            'uyeaktif' => $memberInfo['memberActive'],
            'uyeid' => $memberInfo['memberID']
        ];
        return $this->db->update($sql, $params);
    }

    public function deleteMember($memberID)
    {
        // Önce üye bilgilerini al (benzersiz ID için)
        $memberInfo = $this->getMemberInfo($memberID);
        if(empty($memberInfo)){
            return 0; // Üye bulunamadı
        }
        
        $memberUniqID = $memberInfo['memberUniqID'];
        
        // 1. Üyenin tüm adreslerini sil (soft delete)
        $addressDeleteSql = "
            UPDATE 
                uyeadres 
            SET 
                adressil = 1
            WHERE 
                uyeid = :uyeid AND adressil = 0
        ";
        $this->db->update($addressDeleteSql, ['uyeid' => $memberID]);
        
        // 2. Üyenin sepetini sil (soft delete)
        $cartDeleteSql = "
            UPDATE 
                uyesepet 
            SET 
                sepetsil = 1
            WHERE 
                uyebenzersiz = :uyebenzersiz AND sepetsil = 0
        ";
        $this->db->update($cartDeleteSql, ['uyebenzersiz' => $memberUniqID]);
        
        // 3. Üyenin yorumlarını sil (soft delete)
        $commentDeleteSql = "
            UPDATE 
                yorum 
            SET 
                yorumsil = 0
            WHERE 
                uyeid = :uyeid AND yorumsil = 1
        ";
        $this->db->update($commentDeleteSql, ['uyeid' => $memberID]);
        
        // 4. Üyenin soru/mesajlarını sil (soft delete)
        $messageDeleteSql = "
            UPDATE 
                sorusor 
            SET 
                mesajsil = 0
            WHERE 
                uyeid = :uyeid AND mesajsil = 1
        ";
        $this->db->update($messageDeleteSql, ['uyeid' => $memberID]);
        
        // 5. Son olarak üyeyi sil (soft delete) - siparişler silinmez, gizlenir
        $memberDeleteSql = "
            UPDATE 
                uye 
            SET 
                uyesil = 1
            WHERE 
                uyeid = :uyeid
        ";
        return $this->db->update($memberDeleteSql, ['uyeid' => $memberID]);
    }
    public function updateMemberNameAndSurname($memberID,$memberName,$memberSurname){
        $sql = "
            UPDATE 
                uye 
            SET 
                uyead = :uyead, 
                uyesoyad = :uyesoyad
            WHERE 
                uyeid = :uyeid
        ";

        $params = [
            'uyeid' => $memberID,
            'uyead' => $memberName,
            'uyesoyad' => $memberSurname
        ];

        return $this->db->update($sql, $params);
    }
    public function getMemberInfo($memberId=0, $uniqueId = null) {

        if ($memberId == 0 && $uniqueId == null) return [];

        $condition = $memberId>0 ? "uyeid = :id" : "benzersizid = :id";
        $sql = "
            SELECT 
                uyeolusturmatarih as memberCreateDate,
                uyeid as memberID,
                uyetip as memberType,
                benzersizid as memberUniqID,
                uyetcno as memberIdentityNo,
                uyead as memberName,
                uyesoyad as memberSurname,
                uyeeposta as memberEmail,
                uyesifre as memberPassword,
                uyetelefon as memberPhone,
                uyeaciklama as memberDescription,
                uyefaturaad as memberInvoiceName,
                uyefaturavergino as memberInvoiceTaxNumber,
                uyefaturavergidairesi as memberInvoiceTaxOffice,
                uyeaciklama as memberDescription,
                uyeaktif as memberActive
            FROM 
                uye 
            WHERE $condition
        ";
        return $this->db->select($sql, ['id' => $memberId ?: $uniqueId])[0] ?? [];
    }

    public function getMemberByEmailOrPhone($email="", $phone="")
    {
        //her ikisi de boşsa hata veriyoruz
        if($email == "" && $phone == ""){
            return [];
        }

        $sql = "
            SELECT 
                uyeid as memberID,
                benzersizid as memberUniqID,
                uyetcno as memberIdentityNo,
                uyead as memberName,
                uyesoyad as memberSurname,
                uyeeposta as memberEmail,
                uyetelefon as memberPhone,
                uyefaturaad as memberInvoiceName,
                uyefaturavergino as memberInvoiceTaxOffice,
                uyefaturavergidairesi as memberInvoiceTaxNumber,
                uyeaciklama as memberDescription,
                uyefaturaad as memberInvoiceName,
                uyefaturavergino as memberInvoiceTaxOffice,
                uyefaturavergidairesi as memberInvoiceTaxNumber,
                uyeaktif as memberActive
            FROM 
                uye
            WHERE 
                uyesil = 0
        ";

        if($email != "" && $phone != ""){
            $sql .= " AND (uyeeposta = :email OR uyetelefon = :phone)";
            $params['email'] = $email;
            $params['phone'] = $phone;
        }
        elseif($email != ""){
            $sql .= " AND uyeeposta = :email";
            $params['email'] = $email;
        }
        elseif($phone != ""){
            $sql .= " AND uyetelefon = :phone";
            $params['phone'] = $phone;
        }

        return $this->db->select($sql, $params);
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
        return $this->db->update($sql, $memberInfo);
    }

    public function updatePassportByEmail($email, $newPassword){
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
                    (:memberID, :addressTitle, :addressContactIdentityNumber, :addressContactName, :addressContactSurname, :addressDeliveryCountryID, :addressDeliveryCityID, :addressDeliveryDistrictID, :addressDeliveryAreaID, :addressDeliveryNeighborhoodID, :addressDeliveryPostalCode, :addressDeliveryStreet, :addressContactPhone, :addressDeliveryCountryCode, :addressDeleted)
        ";
        return $this->db->insert($sql, $addressInfo);
    }

    public function updateAddress($addressInfo) {
        $sql = "
            UPDATE 
                uyeadres 
            SET 
                adresbaslik = :addressTitle, adrestcno = :addressContactIdentityNumber, adresad = :addressContactName, adressoyad = :addressContactSurname, adresulke = :addressDeliveryCountryID, adressehir = :addressDeliveryCityID, adresilce = :addressDeliveryDistrictID, 
                adressemt = :addressDeliveryAreaID, adresmahalle = :addressDeliveryNeighborhoodID, postakod = :addressDeliveryPostalCode, adresacik = :addressDeliveryStreet, adrestelefon = :addressContactPhone, adresulkekod = :addressDeliveryCountryCode, adressil = :addressDeleted
            WHERE adresid = :adresid";

        return $this->db->update($sql, $addressInfo);
    }

    public function deleteAddress($addressInfo) {
        $sql = "UPDATE uyeadres SET adressil = 1 WHERE uyeid = :memberID AND adresid = :addressID";
        return $this->db->update($sql, $addressInfo);
    }

    public function getAddresses($memberId) {
        $sql = "
            SELECT 
                adresid as addressID,
                adresbaslik as addressTitle,
                adrestcno as addressContactIdentityNumber,
                adresad as addressContactName,
                adressoyad as addressContactSurname,
                adresulke as addressDeliveryCountryID,
                adressehir as addressDeliveryCityID,
                adresilce as addressDeliveryDistrictID,
                adressemt as addressDeliveryAreaID,
                adresmahalle as addressDeliveryNeighborhoodID,
                postakod as addressDeliveryPostalCode,
                adresacik as addressDeliveryStreet,
                adrestelefon as addressContactPhone,
                adresulkekod as addressDeliveryCountryCode
            FROM 
                uyeadres 
            WHERE 
                uyeid = :uyeid AND adressil = 0";
        return $this->db->select($sql, ['uyeid' => $memberId]);
    }

    public function getAddressByID($memberID,$addressID) {
        $sql = "
            SELECT 
                adresid as addressID,
                adresbaslik as addressTitle,
                adrestcno as addressContactIdentityNumber,
                adresad as addressContactName,
                adressoyad as addressContactSurname,
                adresulke as addressDeliveryCountryID,
                adressehir as addressDeliveryCityID,
                adresilce as addressDeliveryDistrictID,
                adressemt as addressDeliveryAreaID,
                adresmahalle as addressDeliveryNeighborhoodID,
                postakod as addressDeliveryPostalCode,
                adresacik as addressDeliveryStreet,
                adrestelefon as addressContactPhone,
                adresulkekod as addressDeliveryCountryCode
            FROM 
                uyeadres 
            WHERE uyeid = :uyeid AND adresid = :adresid";
        return $this->db->select($sql, ['uyeid' => $memberID, 'adresid' => $addressID]);
    }

    public function getOrders($memberId) {
        $sql = "
            SELECT 
                *,uyesiparisdurum.siparisdurumbaslik 
            FROM 
                uyesiparis 
                inner join uyesiparisdurum on uyesiparis.siparisdurum = uyesiparisdurum.siparisdurumid
            WHERE 
                uyeid = :uyeid AND siparissil = 0
            ORDER BY 
                siparistariholustur DESC
        ";
        return $this->db->select($sql, ['uyeid' => $memberId]);
    }

    public function getOrder($orderId) {
        $sql = "SELECT * FROM uyesiparis WHERE siparisid = :siparisid";
        return $this->db->select($sql, ['siparisid' => $orderId]);
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
                uyetelefon = :telephone
        ";

        return $this->db->select($sql, ['telephone' => $telephone]);
    }

    public function memberSearch(array $searchData) {

        $nameText = $searchData['encryptSearchText'];
        $surnameText = $searchData['encryptSearchText'];
        $nameSurnameText = $searchData['searchText'];
        $mailText = $searchData['encryptSearchText'];
        $phoneText = $searchData['encryptSearchText'];
        $tcText = $searchData['encryptSearchText'];
        $invoiceNameText = $searchData['searchText'];
        $invoiceTaxNumberText = $searchData['searchText'];

        $sql = "
            SELECT 
                * 
            FROM 
                uye 
            WHERE 
                uyesil = 0 AND uyetip=1 AND
                (
                    uyead LIKE :nameText OR
                    uyesoyad LIKE :surnameText OR
                    uyeeposta LIKE :mailText OR
                    uyetelefon LIKE :phoneText OR
                    uyetcno LIKE :tcText OR
                    uyefaturaad LIKE :invoiceNameText OR
                    uyefaturavergino LIKE :invoiceTaxNumberText
                )
        ";

        $params = [
            'nameSurnameText' => "%$nameSurnameText%",
            'nameText' => "%$nameText%",
            'surnameText' => "%$surnameText%",
            'mailText' => "%$mailText%",
            'phoneText' => "%$phoneText%",
            'tcText' => "%$tcText%",
            'invoiceNameText' => "%$invoiceNameText%",
            'invoiceTaxNumberText' => "%$invoiceTaxNumberText%"
        ];

        return $this->db->select($sql, $params);
    }

    public function getMembers()
    {
        $sql = "
            SELECT 
                uyeid as memberID,
                benzersizid as memberUniqID,
                uyead as memberName,
                uyetcno as memberIdentityNo,
                uyetelefon as memberPhone,
                uyefaturaad as memberInvoiceName,
                uyefaturavergino as memberInvoiceTaxOffice,
                uyefaturavergidairesi as memberInvoiceTaxNumber,
                uyeaciklama as memberDescription,
                uyefaturaad as memberInvoiceName,
                uyefaturavergino as memberInvoiceTaxOffice,
                uyefaturavergidairesi as memberInvoiceTaxNumber,
                uyeaktif as memberActive
        
            FROM 
                uye
            WHERE 
                uyesil = 0 and uyetip=1
        ";
        return $this->db->select($sql);
    }

    public function getTotalMembersCount()
    {
        $sql = "SELECT COUNT(*) as total FROM uye WHERE uyesil = 0";
        $result = $this->db->select($sql);
        return $result[0]['total'] ?? 0;
    }

    public function getMembersPaginated($pageNumber = 1, $perPage = 20)
    {
        $offset = ($pageNumber - 1) * $perPage;

        $sql = "
        SELECT 
            uyeolusturmatarih as memberCreateDate,
            uyeid as memberID,
            benzersizid as memberUniqID,
            uyetip as memberType,
            uyetcno as memberIdentityNo,
            uyead as memberName,
            uyesoyad as memberSurname,
            uyeeposta as memberEmail,
            uyesifre as memberPassword,
            uyetelefon as memberPhone,
            uyeaciklama as memberDescription,
            uyefaturaad as memberInvoiceName,
            uyefaturavergino as memberInvoiceTaxOffice,
            uyefaturavergidairesi as memberInvoiceTaxNumber,
            uyeaktif as memberActive
        FROM 
            uye 
        WHERE 
            uyesil = 0 AND uyetip=1
        LIMIT :perPage OFFSET :offset
    ";

        $params = [
            'perPage' => $perPage,
            'offset' => $offset,
        ];

        return $this->db->select($sql, $params);
    }

    public function updateMemberPassword($memberID, $memberPassword)
    {
        $sql = "
            UPDATE 
                uye 
            SET 
                uyesifre = :memberPassword
            WHERE 
                uyeid = :memberID
        ";
        return $this->db->update($sql, ['memberID' => $memberID, 'memberPassword' => $memberPassword]);

    }

    public function beginTransaction($funcName="")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }
}
