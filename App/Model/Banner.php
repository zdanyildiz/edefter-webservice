<?php


class Banner
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Kategoriye göre bannerları al
    public function getBannersByCategory($categoryID)
    {
        $sql = "SELECT * FROM banner 
                INNER JOIN banner_kategori ON banner.bannerid = banner_kategori.bannerid
                WHERE banner_kategori.kategoriid = :categoryID";
        return $this->db->select($sql, ['categoryID' => $categoryID]);
    }

    // Sayfaya göre bannerları al
    public function getBannersByPage($pageID)
    {
        $sql = "SELECT * FROM banner 
                INNER JOIN banner_sayfa ON banner.bannerid = banner_sayfa.bannerid
                WHERE banner_sayfa.sayfaid = :pageID";
        return $this->db->select($sql, ['pageID' => $pageID]);
    }
}
