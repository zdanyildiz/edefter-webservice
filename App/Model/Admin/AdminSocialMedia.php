<?php
/**
 * Table: ayarsosyalmedya
 * Columns:
 * ayarsosyalmedyaid tinyint AI PK
 * dilid tinyint(1)
 * facebook varchar(100)
 * twitter varchar(100)
 * googleplus varchar(100)
 * instagram varchar(100)
 * linkedin varchar(100)
 * youtube varchar(100)
 * pinterest varchar(100)
 * skype varchar(100)
 * blog varchar(100)
 * benzersizid varchar(20)
 */

class AdminSocialMedia{

    private AdminDatabase $db;

    public function __construct(AdminDatabase $db){
        $this->db = $db;
    }

    public function getSocialMedia(int $languageID){
        $query = "SELECT * FROM ayarsosyalmedya WHERE dilid = :languageID";
        $params = ["languageID" => $languageID];

        $result = $this->db->select($query, $params);

        if (!empty($result)){
            return $result[0];
        }

        return [];
    }

    public function addSocialMedia($socialMediaData)
    {
        //languageID'ye göre varsa güncelleyelim, yoksa ekleyelim

        $query = "
            INSERT 
                INTO ayarsosyalmedya (dilid, facebook, twitter, googleplus, instagram, linkedin, youtube, pinterest, skype, blog, benzersizid) 
                VALUES (:languageID, :facebook, :twitter, :googleplus, :instagram, :linkedin, :youtube, :pinterest, :skype, :blog, :socialMediaUniqID)
            
        ";

        $params = [
            "languageID" => $socialMediaData["languageID"],
            "facebook" => $socialMediaData["facebook"],
            "twitter" => $socialMediaData["twitter"],
            "googleplus" => $socialMediaData["googleplus"],
            "instagram" => $socialMediaData["instagram"],
            "linkedin" => $socialMediaData["linkedin"],
            "youtube" => $socialMediaData["youtube"],
            "pinterest" => $socialMediaData["pinterest"],
            "skype" => $socialMediaData["skype"],
            "blog" => $socialMediaData["blog"],
            "socialMediaUniqID" => $socialMediaData["socialMediaUniqID"]
        ];

        return $this->db->insert($query, $params);

    }

    public function updateSocialMedia($updateData){
        $query = "
            UPDATE ayarsosyalmedya 
            SET 
                facebook = :facebook, 
                twitter = :twitter, 
                googleplus = :googleplus, 
                instagram = :instagram, 
                linkedin = :linkedin, 
                youtube = :youtube, 
                pinterest = :pinterest, 
                skype = :skype, 
                blog = :blog 
            WHERE 
                dilid = :languageID
        ";

        $params = [
            "facebook" => $updateData["facebook"],
            "twitter" => $updateData["twitter"],
            "googleplus" => $updateData["googleplus"],
            "instagram" => $updateData["instagram"],
            "linkedin" => $updateData["linkedin"],
            "youtube" => $updateData["youtube"],
            "pinterest" => $updateData["pinterest"],
            "skype" => $updateData["skype"],
            "blog" => $updateData["blog"],
            "languageID" => $updateData["languageID"]
        ];

        return $this->db->update($query, $params);
    }

    public function beginTransaction(){
        $this->db->beginTransaction();
    }

    public function commit(){
        $this->db->commit();
    }

    public function rollback(){
        $this->db->rollback();
    }
}