<?php
/**
 * Table: resimgaleri
 * Columns:
 * resimgaleriid int AI PK
 * benzersizid varchar(20)
 * resimgaleritariholustur datetime
 * resimgaleritarihguncel datetime
 * resimgaleriad varchar(100)
 * resimgaleriaciklama longtext
 * resimgalerisira int
 * kategoridegoster tinyint(1)
 * resimgalerisil tinyint(1)
 * resimgalerisiralama tinyint(1)
 */

/**
 * Table: resimgaleriliste
 * Columns:
 * resimgalerilisteid int AI PK
 * resimgaleriid int
 * resimid int
 */

class Gallery{
    private Database $db;

    public function __construct(Database $db){
        $this->db = $db;
    }

    public function getGalleryList(){
        $sql = "
            SELECT 
                resimgaleriid as galleryID,
                benzersizid as galleryUniqID,
                resimgaleriad as galleryName,
                resimgaleriaciklama as galleryDescription,
                resimgaleritariholustur as galleryCreatedDate,
                resimgaleritarihguncel as galleryUpdatedDate,
                resimgalerisira as galleryOrder,
                kategoridegoster as galleryShowInCategory,
                resimgalerisil as galleryDeleted,
                resimgalerisiralama as galleryOrdering
            FROM 
                resimgaleri 
            WHERE 
                resimgalerisil=0 
            ORDER BY 
                resimgalerisira
        ";

        $result = $this->db->select($sql);

        if(!$result){
            return [];
        }

        return [
            "status" => "success",
            "data" => $result
        ];
    }

    public function getGallery(int $galleryID)
    {

        $sql = "
            SELECT 
                resimgaleriid as galleryID,
                benzersizid as galleryUniqID,
                resimgaleriad as galleryName,
                resimgaleriaciklama as galleryDescription,
                resimgalerisira as galleryOrder,
                kategoridegoster as galleryShowInCategory,
                resimgalerisil as galleryDeleted,
                resimgalerisiralama as galleryOrdering
            FROM 
                resimgaleri 
            WHERE 
                resimgaleriid=:galleryID
        ";

        return $this->db->select($sql, ['galleryID' => $galleryID]);
    }

    public function getGalleryImages(int $galleryID){
        $sql = "
            SELECT 
                resimgaleriid as galleryID,
                resimid as imageID
            FROM 
                resimgaleriliste 
            WHERE 
                resimgaleriid=:galleryID";
        return $this->db->select($sql, ['galleryID'=>$galleryID]);
    }


    public function searchGallery($searchText){
        $sql = "
            SELECT 
                resimgaleriid as galleryID,
                resimgaleriad as galleryName,
                resimgaleriaciklama as galleryDescription,
                resimgaleritariholustur as galleryCreatedDate,
                resimgaleritarihguncel as galleryUpdatedDate,
                resimgalerisira as galleryOrder,
                kategoridegoster as galleryShowInCategory,
                resimgalerisil as galleryDeleted,
                resimgalerisiralama as galleryOrdering
            FROM 
                resimgaleri 
            WHERE 
                resimgaleriad LIKE :searchText
            ORDER BY 
                resimgalerisira
        ";
        return $this->db->select($sql, ['searchText' => '%'.$searchText.'%']);
    }
}
