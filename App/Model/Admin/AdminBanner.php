<?php

/**
 * banner
 * bannerid
 * dilid
 * bannerkategori
 * bannerad
 * bannergenislik
 * banneryukseklik
 * bannerlink
 * bannerresim
 * bannerslogan
 * banneryazi
 * banneraktif
 * bannersure
 * bannertarihbaslangic
 * bannertarihbitis
 */

/**
 * Table: banner_kategori
 * Columns:
 * id int AI PK
 * bannerid int
 * kategoriid int
 */

class AdminBanner
{
    private AdminDatabase $db;


    public function __construct($db)
    {
        $this->db = $db;
    }
    public function addBanner($data){
        $sql = "
            INSERT INTO banner 
            (
                dilid,
                bannerkategori,
                bannerad,
                bannerslogan,
                banneryazi,
                bannerlink,
                bannerresim,
                banner_button,
                bannergenislik,
                banneryukseklik,                
                banneraktif,
                bannersure,
                bannertarihbaslangic,
                bannertarihbitis,
                banner_button_location
            )
            VALUES
            (
                :languageID,
                :bannerCategory,
                :bannerName,
                :bannerSlogan,
                :bannerText,
                :bannerLink,
                :bannerImage,
                :bannerButton,
                :bannerWidth,
                :bannerHeight,
                :bannerActive,
                :bannerTime,
                :bannerStartDate,
                :bannerEndDate,
                :bannerButtonLocation
            )
        ";

        return $this->db->insert($sql, $data);
    }
    public function updateBannerLinkByLink($data)
    {
        $sql = "UPDATE banner SET bannerlink = :newBannerlink WHERE bannerlink = :oldBannerlink";
        $result = $this->db->update($sql, $data);

        if ($result>0) {
            return [
                "status" => "success",
                "message" => "Banner link güncellendi"
            ];
        }
        if ($result==0) {
            return [
                "status" => "success",
                "message" => "Banner link güncel"
            ];
        }
        else {
            return [
                "status" => "error",
                "message" => "Banner link güncellenemedi"
            ];
        }
    }
    public function getBannerByLanguageIDAndBannerCategoryAndCategoryID($languageID, $bannerCategory, $categoryID){
        $sql = "
            SELECT
                b.bannerid as bannerID,
                b.banner_group_id as bannerGroupID,
                b.dilid as bannerLanguageID,
                b.bannerkategori as bannerCategory,
                b.bannerad as bannerName,
                b.bannergenislik as bannerWidth,
                b.banneryukseklik as bannerHeight,
                b.bannerlink as bannerLink,
                b.bannerresim as bannerImage,
                b.bannerslogan as bannerSlogan,
                b.banneryazi as bannerText,
                b.banneraktif as bannerActive,
                b.bannersure as bannerDuration,
                b.bannertarihbaslangic as bannerStartDate,
                b.bannertarihbitis as bannerEndDate,
                b.banner_button as bannerButton,
                b.banner_button_location as bannerButtonLocation,
                bk.kategoriid as categoryID
            FROM 
                banner b
            JOIN 
                banner_kategori bk ON b.bannerid = bk.bannerid
            WHERE 
                b.dilid = :languageID AND b.bannerkategori = :bannerCategory AND bk.kategoriid = :categoryID
            ORDER BY 
                b.bannerid  
        ";

        $result = $this->db->select($sql, ["languageID" => $languageID, "bannerCategory" => $bannerCategory, "categoryID" => $categoryID]);
        return $result;
    }

    public function deleteBannerByLanguageIDAndBannerCategoryAndCategoryID($languageID, $bannerCategory, $categoryID){
        $sql = "
            DELETE b 
                FROM banner b
                INNER JOIN banner_kategori bk ON b.bannerid = bk.bannerid
                WHERE b.dilid = :languageID AND b.bannerkategori = :bannerCategory AND bk.kategoriid = :categoryID;
        ";
        return $this->db->delete($sql, ["languageID" => $languageID, "bannerCategory" => $bannerCategory, "categoryID" => $categoryID]);
    }

    public function addBannerCategoryRelation($bannerID, $categoryID, $bannerCategory){
        $sql = "INSERT INTO banner_kategori (bannerid, kategoriid, banner_category) VALUES (:bannerID, :categoryID, :bannerCategory)";
        return $this->db->insert($sql, ["bannerID" => $bannerID, "categoryID" => $categoryID, "bannerCategory" => $bannerCategory]);
    }

    public function deleteBannerRelationByCategoryID($bannerCategory,$categoryID){
        $sql = "DELETE FROM banner_kategori WHERE kategoriid = :categoryID and banner_category = :bannerCategory";
        return $this->db->delete($sql, ["categoryID" => $categoryID, "bannerCategory" => $bannerCategory]);
    }

    public function getBannerRelations($bannerID){
        $sql = "SELECT * FROM banner_kategori WHERE bannerid = :bannerID";
        return $this->db->select($sql, ["bannerID" => $bannerID]);
    }

    ###############################
    public function getBannerByLanguageIDAndBannerCategoryAndPageID($languageID, $bannerCategory, $pageID){
        $sql = "
            SELECT
                b.bannerid as bannerID,
                b.dilid as bannerLanguageID,
                b.bannerkategori as bannerCategory,
                b.bannerad as bannerName,
                b.bannergenislik as bannerWidth,
                b.banneryukseklik as bannerHeight,
                b.bannerlink as bannerLink,
                b.bannerresim as bannerImage,
                b.bannerslogan as bannerSlogan,
                b.banneryazi as bannerText,
                b.banneraktif as bannerActive,
                b.bannersure as bannerDuration,
                b.bannertarihbaslangic as bannerStartDate,
                b.bannertarihbitis as bannerEndDate,
                b.banner_button as bannerButton,
                b.banner_button_location as bannerButtonLocation,
                bs.sayfaid as pageID
            FROM 
                banner b
            INNER JOIN 
                banner_sayfa bs ON b.bannerid = bs.bannerid
            WHERE 
                b.dilid = :languageID AND bs.banner_category = :bannerCategory AND bs.sayfaid = :pageID
            ORDER BY 
                b.bannerid  
        ";

        return $this->db->select($sql, ["languageID" => $languageID, "bannerCategory" => $bannerCategory, "pageID" => $pageID]);
    }

    public function deleteBannerByLanguageIDAndBannerCategoryAndPageID($languageID, $bannerCategory, $pageID){
        $sql = "
            DELETE b 
                FROM banner b
                INNER JOIN banner_sayfa bs ON b.bannerid = bs.bannerid
                WHERE b.dilid = :languageID AND b.bannerkategori = :bannerCategory AND bs.sayfaid = :pageID;
        ";
        return $this->db->delete($sql, ["languageID" => $languageID, "bannerCategory" => $bannerCategory, "pageID" => $pageID]);
    }

    public function addBannerPageRelation($bannerID, $pageID, $bannerCategory){
        $sql = "INSERT INTO banner_sayfa (bannerid, sayfaid, banner_category) VALUES (:bannerID, :pageID, :bannerCategory)";
        return $this->db->insert($sql, ["bannerID" => $bannerID, "pageID" => $pageID, "bannerCategory" => $bannerCategory]);
    }

    public function deleteBannerRelationByPageID($bannerCategory,$pageID){
        $sql = "DELETE FROM banner_sayfa WHERE banner_category = :bannerCategory AND sayfaid = :pageID";
        return $this->db->delete($sql, ["bannerCategory" => $bannerCategory,"pageID" => $pageID]);
    }

    public function getBannerPageRelations($bannerID){
        $sql = "SELECT * FROM banner_sayfa WHERE bannerid = :bannerID";
        return $this->db->select($sql, ["bannerID" => $bannerID]);
    }

    public function getBannerPageBannerIds($pageID, $bannerCategory){
        $sql = "SELECT bannerid FROM banner_sayfa WHERE sayfaid = :pageID AND banner_category = :bannerCategory";
        return $this->db->select($sql, ["pageID" => $pageID, "bannerCategory" => $bannerCategory]);
    }

    public function deleteBannerByBannerID($bannerID){
        $sql = "DELETE FROM banner WHERE bannerid = :bannerID";
        return $this->db->delete($sql, ["bannerID" => $bannerID]);
    }

    public function addBannerGroup($bannerGroupData){
        $sql = "INSERT INTO banner_groups (name, style_type, created_at, updated_at)
            values(:name, :style_type, NOW(), NOW())";
        return $this->db->insert($sql, $bannerGroupData);
    }

    public function updateBannerGroup($bannerGroupData){
        $sql = "UPDATE banner_groups SET name = :name, style_type = :style_type, updated_at = NOW() WHERE id = :id";
        return $this->db->update($sql, $bannerGroupData);
    }

    public function getBannerGroup($bannerGroupID){
        $sql = "SELECT * FROM banner_groups WHERE id = :id";
        return $this->db->select($sql, ["id" => $bannerGroupID]);
    }

    public function getBannerGroupsByBannerType($bannerType){
        $sql = "SELECT * FROM banner_groups WHERE banner_type = :banner_type";
        return $this->db->select($sql, ["banner_type" => $bannerType]);
    }

    public function deleteBannerGroup($bannerGroupID){
        $sql = "DELETE FROM banner_groups WHERE id = :id";
        return $this->db->delete($sql, ["id" => $bannerGroupID]);
    }

    public function beginTransaction($funcName = ""){
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName = ""){
        $this->db->commit($funcName);
    }

    public function rollback($funcName = ""){
        $this->db->rollback($funcName);
    }
}
