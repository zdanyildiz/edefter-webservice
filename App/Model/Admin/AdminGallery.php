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

class AdminGallery{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db){
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

    public function addGallery(array $data){
        $sql = "
            INSERT INTO 
                resimgaleri 
            SET 
                benzersizid=:galleryUniqID,
                resimgaleriad=:galleryName,
                resimgaleriaciklama=:galleryDescription,
                resimgaleritariholustur=:galleryCreatedDate,
                resimgaleritarihguncel=:galleryUpdatedDate,
                resimgalerisira=:galleryOrder,
                kategoridegoster=:galleryShowInCategory,
                resimgalerisil=:galleryDeleted,
                resimgalerisiralama=:galleryOrdering
        ";

        $result = $this->db->insert($sql, $data);

        if(!$result){
            return [
                "status" => "error",
                "message" => "Galeri eklenirken bir hata oluştu."
            ];
        }

        return [
            "status" => "success",
            "message" => "Galeri başarıyla eklendi.",
            "galleryID" => $result
        ];
    }

    public function updateGallery(array $data){
        $sql = "
            UPDATE 
                resimgaleri 
            SET 
                resimgaleriad=:galleryName,
                resimgaleriaciklama=:galleryDescription,
                resimgaleritarihguncel=:galleryUpdatedDate,
                resimgalerisira=:galleryOrder,
                kategoridegoster=:galleryShowInCategory,
                resimgalerisiralama=:galleryOrdering
            WHERE 
                resimgaleriid=:galleryID
        ";

        $result = $this->db->update($sql, $data);

        if(!$result){
            return [
                "status" => "error",
                "message" => "Galeri güncellenirken bir hata oluştu."
            ];
        }

        return [
            "status" => "success",
            "message" => "Galeri başarıyla güncellendi."
        ];
    }

    public function deleteGallery(int $galleryID)
    {
        $sql = "
            UPDATE 
                resimgaleri 
            SET 
                resimgalerisil=1
            WHERE 
                resimgaleriid=:galleryID
        ";

        $result = $this->db->update($sql, ['galleryID' => $galleryID]);

        if (!$result) {
            return [
                "status" => "error",
                "message" => "Galeri silinirken bir hata oluştu."
            ];
        }

        return [
            "status" => "success",
            "message" => "Galeri başarıyla silindi."
        ];

    }

    public function addGalleryImage(array $data)
    {

        $galleryID = $data['galleryID'];
        $imageIds = $data['imageIDs'];

        //imageIDs dizisini döngüye alalım
        foreach ($imageIds as $imageID) {
            $sql = "
                INSERT INTO 
                    resimgaleriliste 
                SET 
                    resimgaleriid=:galleryID,
                    resimid=:imageID
            ";

            $result = $this->db->insert($sql, ['galleryID' => $galleryID, 'imageID' => $imageID]);

            if (!$result) {
                return [
                    "status" => "error",
                    "message" => "Galeri resmi eklenirken bir hata oluştu."
                ];
            }
        }

        return [
            "status" => "success",
            "message" => "Galeri resmi başarıyla eklendi."
        ];
    }

    public function deleteGalleryImage(int $galleryID)
    {
        $sql = "
            DELETE FROM 
                resimgaleriliste 
            WHERE 
                resimgaleriid=:galleryID
        ";

        $result = $this->db->delete($sql, ['galleryID' => $galleryID]);

        if (!$result) {
            return [
                "status" => "error",
                "message" => "Galeri resmi silinirken bir hata oluştu."
            ];
        }

        return [
            "status" => "success",
            "message" => "Galeri resmi başarıyla silindi."
        ];
    }

    public function updateGalleryOrder($updateData)
    {
        $sql = "
            UPDATE 
                resimgaleri 
            SET 
                resimgalerisira=:galleryOrder
            WHERE 
                resimgaleriid=:galleryID
        ";

        $result = $this->db->update($sql, $updateData);

        if (!$result) {
            return [
                "status" => "error",
                "message" => "Galeri sırası güncellenirken bir hata oluştu."
            ];
        }

        return [
            "status" => "success",
            "message" => "Galeri sırası başarıyla güncellendi."
        ];

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
