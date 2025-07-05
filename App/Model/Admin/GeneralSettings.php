<?php
/**
 * ayargenel table
 * genelayarid
 * domain
 * ssldurum
 * sitetip
 * cokludil
 * uyelik
 */

class GeneralSettings{
    private AdminDatabase $db;
    public function __construct($db){
        $this->db = $db;
    }

    public function getGeneralSettings($languageID){
        $query = "
            SELECT
                *
            FROM
                ayargenel
            WHERE
                dilid = :languageID
        ";

        $param = [
            'languageID' => $languageID
        ];

        return $this->db->select($query,$param);
    }
    public function getDomains(){
        $query = "
            SELECT
                *
            FROM
                ayargenel
            WHERE
                ayargenelid = 1
        ";

        $result = $this->db->select($query);

        if(count($result) == 0){
            return [
                'status' => 'error',
                'message' => 'Domain ayarları bulunamadı.'
            ];
        }
        $result = $result[0];
        return [
            'status' => 'success',
            'data' => $result
        ];
    }

    public function getDomainByLanguageID($languageID)
    {
        $sql = "SELECT domain FROM ayargenel WHERE dilid = :languageID";

        $param = [
            'languageID' => $languageID
        ];

        $data = $this->db->select($sql, $param);

        if(count($data) == 0){
            return "";
        }

        return $data[0]['domain'];
    }

    //dil ve alan adı gönderelim, ilgili dil yoksa ekleyelim varsa güncelleyelim

    public function addGeneralSettings($languageID, $domain, $siteType, $isMemberRegistration){
        $sslStatus = 1;
        $multiLanguage = 1;

        $data = [
            'languageID' => $languageID,
            'domain' => $domain,
            'sslStatus' => $sslStatus,
            'siteType' => $siteType,
            'multiLanguage' => $multiLanguage,
            'membership' => $isMemberRegistration,

        ];

        $sql ="
            INSERT INTO 
                ayargenel SET
                    dilid = :languageID,
                    domain = :domain,
                    ssldurum = :sslStatus,
                    sitetip = :siteType,
                    cokludil = :multiLanguage,
                    uyelik = :membership
        ";

        $this->db->beginTransaction();

        $addOrUpdate = $this->db->insert($sql, $data);

        if(!$addOrUpdate){
            $this->db->rollBack();
            return [
                'status' => 'error',
                'message' => 'Genel ayarlar eklenirken bir hata oluştu.'
            ];
        }

        $this->db->commit();

        return [
            'status' => 'success',
            'message' => 'Genel ayarlar başarıyla eklendi.'
        ];
    }

    public function updateGeneralSettings($languageID, $domain, $siteType, $isMemberRegistration){
        $sslStatus = 1;
        $multiLanguage = 1;

        $data = [
            'languageID' => $languageID,
            'domain' => $domain,
            'sslStatus' => $sslStatus,
            'siteType' => $siteType,
            'multiLanguage' => $multiLanguage,
            'membership' => $isMemberRegistration,

        ];

        $sql ="
            UPDATE 
                ayargenel SET
                    domain = :domain,
                    ssldurum = :sslStatus,
                    sitetip = :siteType,
                    cokludil = :multiLanguage,
                    uyelik = :membership
            WHERE
                dilid = :languageID
        ";

        $this->db->beginTransaction();

        $addOrUpdate = $this->db->update($sql, $data);

        if(!$addOrUpdate){
            $this->db->rollBack();
            return [
                'status' => 'error',
                'message' => 'Genel ayarlar güncellenirken bir hata oluştu.'
            ];
        }

        $this->db->commit();

        return [
            'status' => 'success',
            'message' => 'Genel ayarlar başarıyla güncellendi.'
        ];
    }
}