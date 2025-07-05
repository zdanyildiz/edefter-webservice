<?php
/**
 * Table: ayarfirma
 * Columns:
 * ayarfirmaid tinyint(1) AI PK
 * dilid tinyint(1)
 * ayarfirmaad varchar(255)
 * ayarfirmakisaad varchar(50)
 * ayarfirmavergidairesi varchar(50)
 * ayarfirmavergino varchar(11)
 * ayarfirmaulke varchar(50)
 * ayarfirmasehir varchar(50)
 * ayarfirmailce varchar(50)
 * ayarfirmasemt varchar(50)
 * ayarfirmamahalle varchar(50)
 * ayarfirmapostakod varchar(5)
 * ayarfirmaadres varchar(255)
 * ayarfirmaeposta varchar(50)
 * ayarfirmagsm varchar(10)
 * ayarfirmatelefon varchar(10)
 * ayarfirmafaks varchar(10)
 * ayarfirmaenlem varchar(10)
 * ayarfirmaboylam varchar(10)
 * ayarfirmaharita longtext
 * ayarfirmaulkekod varchar(3)
 * benzersizid varchar(20)
 * ayarfirmasil tinyint(1)
 */

/**
 * Table: ayarlogo
 * Columns:
 * logoid int AI PK
 * dilid tinyint(1)
 * logoyazi varchar(50)
 * resimid int
 */

class AdminCompany{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db){
        $this->db = $db;
    }

    public function getCompany($id){
        $query = "
            SELECT 
                dilid as languageId,
                ayarfirmaid as companyID,
                ayarfirmaad as companyName,
                ayarfirmakisaad as companyShortName,
                ayarfirmavergidairesi as taxOffice,
                ayarfirmavergino as taxNumber,
                ayarfirmaulke as country,
                ayarfirmasehir as city,
                ayarfirmailce as district,
                ayarfirmasemt as area,
                ayarfirmamahalle as neighborhood,
                ayarfirmapostakod as postalCode,
                ayarfirmaadres as address,
                ayarfirmaeposta as email,
                ayarfirmagsm as gsm,
                ayarfirmatelefon as phone,
                ayarfirmafaks as fax,
                ayarfirmaenlem as latitude,
                ayarfirmaboylam as longitude,
                ayarfirmaharita as map,
                ayarfirmaulkekod as countryCode,
                benzersizid as uniqueId,
                ayarfirmasil as deleted
            FROM 
                ayarfirma 
            WHERE 
                ayarfirmaid = :id
        ";

        $params = array(":id" => $id);

        $result = $this->db->select($query, $params);

        if ($result){
            return $result[0];
        }

        return [];
    }

    public function getCompanyByLanguageID($languageId){
        $query = "
            SELECT 
                ayarfirmaid as companyID,
                ayarfirmaad as companyName,
                ayarfirmakisaad as companyShortName,
                ayarfirmavergidairesi as taxOffice,
                ayarfirmavergino as taxNumber,
                ayarfirmaulke as country,
                ayarfirmasehir as city,
                ayarfirmailce as district,
                ayarfirmasemt as area,
                ayarfirmamahalle as neighborhood,
                ayarfirmapostakod as postalCode,
                ayarfirmaadres as address,
                ayarfirmaeposta as email,
                ayarfirmagsm as gsm,
                ayarfirmatelefon as phone,
                ayarfirmafaks as fax,
                ayarfirmaenlem as latitude,
                ayarfirmaboylam as longitude,
                ayarfirmaharita as map,
                ayarfirmaulkekod as countryCode,
                benzersizid as uniqueId,
                parent_company_id as parentCompanyID
            FROM 
                ayarfirma 
            WHERE 
                dilid = :languageId and ayarfirmasil = 0 and parent_company_id = 0
        ";

        $params = array(":languageId" => $languageId);

        $result = $this->db->select($query, $params);

        if ($result){
            return $result[0];
        }

        return [];
    }

    public function saveCompany($companyData, $isUpdate = false) {
        // Ortak parametreler
        $query = "
            SET 
                dilid = :languageId,
                ayarfirmaad = :companyName,
                ayarfirmakisaad = :companyShortName,
                ayarfirmavergidairesi = :taxOffice,
                ayarfirmavergino = :taxNumber,
                ayarfirmaulke = :country,
                ayarfirmasehir = :city,
                ayarfirmailce = :district,
                ayarfirmasemt = :area,
                ayarfirmamahalle = :neighborhood,
                ayarfirmapostakod = :postalCode,
                ayarfirmaadres = :address,
                ayarfirmaeposta = :email,
                ayarfirmagsm = :gsm,
                ayarfirmatelefon = :phone,
                ayarfirmafaks = :fax,
                ayarfirmaenlem = :latitude,
                ayarfirmaboylam = :longitude,
                ayarfirmaharita = :map,
                ayarfirmaulkekod = :countryCode,
                parent_company_id = :parentCompanyId 
        ";

        // Ortak parametreler
        $params = array(
            ":languageId" => $companyData["languageID"],
            ":companyName" => $companyData["companyName"],
            ":companyShortName" => $companyData["companyShortName"],
            ":taxOffice" => $companyData["companyTaxOffice"],
            ":taxNumber" => $companyData["companyTaxNumber"],
            ":country" => $companyData["companyCountryID"],
            ":city" => $companyData["companyCityID"],
            ":district" => $companyData["companyCountyID"],
            ":area" => $companyData["companyAreaID"],
            ":neighborhood" => $companyData["companyNeighbourhoodID"],
            ":postalCode" => $companyData["companyPostalCode"],
            ":address" => $companyData["companyAddress"],
            ":email" => $companyData["companyEmail"],
            ":gsm" => $companyData["companyGsm"],
            ":phone" => $companyData["companyPhone"],
            ":fax" => $companyData["companyFax"],
            ":latitude" => $companyData["latitude"],
            ":longitude" => $companyData["longitude"],
            ":map" => $companyData["companyMap"],
            ":countryCode" => $companyData["companyCountryCode"],
            ":parentCompanyId" => isset($companyData["parentCompanyID"]) ? $companyData["parentCompanyID"] : 0 // Ana firma ID, eğer yoksa 0
        );

        if ($isUpdate) {
            // Güncelleme sorgusu oluşturma
            $query = "UPDATE ayarfirma " . $query . " WHERE ayarfirmaid = :id";
            $params[":id"] = $companyData["companyID"];
            return $this->db->update($query, $params);
        }
        else {
            // Ekleme sorgusu oluşturma
            $query = "INSERT INTO ayarfirma " . $query . ", benzersizid = :uniqueId, ayarfirmasil = 0";
            $params[":uniqueId"] = $companyData["uniqueId"];
            return $this->db->insert($query, $params);
        }
    }

    public function deleteCompany($id){
        $query = "
            UPDATE 
                ayarfirma 
            SET 
                ayarfirmasil = 1 
            WHERE 
                ayarfirmaid = :id
        ";

        $params = array(":id" => $id);

        return $this->db->update($query, $params);
    }

    public function getCompanyLogo($languageID){
        $query = "
            SELECT 
                logoid as logoID,
                dilid as languageID,
                logoyazi as logoText,
                resim.resimid as imageID,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as imagePath
            FROM 
                ayarlogo 
                    inner join resim on ayarlogo.resimid = resim.resimid
                        inner join resimklasor on resim.resimklasorid = resimklasor.resimklasorid
            WHERE 
                dilid = :languageID
        ";

        $params = array(":languageID" => $languageID);

        $result = $this->db->select($query, $params);

        if ($result){
            return $result[0];
        }

        return [];
    }

    public function addCompanyLogo($languageID, $logoText, $imageID){
        $query = "
            INSERT INTO 
                ayarlogo 
            SET 
                dilid = :languageID,
                logoyazi = :logoText,
                resimid = :imageID
        ";

        $params = array(
            ":languageID" => $languageID,
            ":logoText" => $logoText,
            ":imageID" => $imageID
        );

        return $this->db->insert($query, $params);
    }

    public function updateCompanyLogo($languageID, $logoText, $imageID){
        $query = "
            UPDATE 
                ayarlogo 
            SET 
                logoyazi = :logoText,
                resimid = :imageID
            WHERE 
                dilid = :languageID
        ";

        $params = array(
            ":languageID" => $languageID,
            ":logoText" => $logoText,
            ":imageID" => $imageID
        );

        return $this->db->update($query, $params);
    }

    public function getCompanyFavicon($imageUniqID){
        $query = "
            SELECT 
                resim.resimid as imageID,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as imagePath
            FROM 
                resim
                    inner join resimklasor on resim.resimklasorid = resimklasor.resimklasorid
            WHERE 
                benzersizid = :imageUniqID
        ";

        $params = array(":imageUniqID" => $imageUniqID);

        $result = $this->db->select($query, $params);

        if ($result){
            return $result[0];
        }

        return [];
    }

    // Yeni şube ekleme işlemi (saveCompany metodunu kullanarak)
    public function saveBranch($branchData, $isUpdate = false)
    {
        // Ortak parametreler
        $query = "
            SET 
                parent_company_id = :parentCompanyId,
                dilid = :languageId,
                ayarfirmaad = :companyName,
                ayarfirmakisaad = :companyShortName,
                ayarfirmavergidairesi = :taxOffice,
                ayarfirmavergino = :taxNumber,
                ayarfirmaulke = :country,
                ayarfirmasehir = :city,
                ayarfirmailce = :district,
                ayarfirmasemt = :area,
                ayarfirmamahalle = :neighborhood,
                ayarfirmapostakod = :postalCode,
                ayarfirmaadres = :address,
                ayarfirmaeposta = :email,
                ayarfirmagsm = :gsm,
                ayarfirmatelefon = :phone,
                ayarfirmafaks = :fax,
                ayarfirmaenlem = :latitude,
                ayarfirmaboylam = :longitude,
                ayarfirmaharita = :map,
                ayarfirmaulkekod = :countryCode
        ";

        $params = array(
            ":parentCompanyId" => $branchData["parentCompanyID"],  // Yeni eklenen alan
            ":languageId" => $branchData["languageID"],
            ":companyName" => $branchData["companyName"],
            ":companyShortName" => $branchData["companyShortName"],
            ":taxOffice" => $branchData["companyTaxOffice"],
            ":taxNumber" => $branchData["companyTaxNumber"],
            ":country" => $branchData["companyCountryID"],
            ":city" => $branchData["companyCityID"],
            ":district" => $branchData["companyCountyID"],
            ":area" => $branchData["companyAreaID"],
            ":neighborhood" => $branchData["companyNeighbourhoodID"],
            ":postalCode" => $branchData["companyPostalCode"],
            ":address" => $branchData["companyAddress"],
            ":email" => $branchData["companyEmail"],
            ":gsm" => $branchData["companyGsm"],
            ":phone" => $branchData["companyPhone"],
            ":fax" => $branchData["companyFax"],
            ":latitude" => $branchData["latitude"],
            ":longitude" => $branchData["longitude"],
            ":map" => $branchData["companyMap"],
            ":countryCode" => $branchData["companyCountryCode"]
        );

        if ($isUpdate) {
            // Şubeyi güncelleme sorgusu oluşturma
            $query = "UPDATE ayarfirma " . $query . " WHERE ayarfirmaid = :id";
            $params[":id"] = $branchData["companyID"];
            return $this->db->update($query, $params);
        } else {
            // Şube ekleme sorgusu oluşturma
            $query = "INSERT INTO ayarfirma " . $query . ", benzersizid = :uniqueId, ayarfirmasil = 0";
            $params[":uniqueId"] = $branchData["uniqueId"];
            return $this->db->insert($query, $params);
        }
    }

    // Ana firma ID'ye göre şubeleri getirir
    public function getBranchesByCompanyID($parentCompanyID)
    {
        $query = "
            SELECT 
                ayarfirmaid as branchID,
                ayarfirmaad as branchName,
                ayarfirmakisaad as branchShortName,
                ayarfirmasehir as city,
                ayarfirmailce as district,
                ayarfirmaadres as address,
                ayarfirmaeposta as email,
                ayarfirmagsm as gsm,
                ayarfirmatelefon as phone,
                ayarfirmafaks as fax,
                ayarfirmasil as deleted
            FROM 
                ayarfirma
            WHERE 
                parent_company_id = :parentCompanyID AND ayarfirmasil = 0
        ";

        $params = array(":parentCompanyID" => $parentCompanyID);
        return $this->db->select($query, $params);
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
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