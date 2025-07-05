<?php

/**
 * seo
 * seoid
 * benzersizid
 * baslik
 * aciklama
 * kelime
 * link
 * orjinallink
 * resim
 */
class SeoModel {

    private Database $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSeoByLink($link) {
        $sql = "SELECT * FROM seo WHERE link = :link";
        return $this->db->select($sql, ["link" => $link]);
    }

    public function getSeoByUniqId($uniqId) {
        $sql = "
            SELECT 
                baslik as seoTitle,
                aciklama as seoDescription,
                kelime as seoKeywords,
                link as seoLink,
                orjinallink as seoOriginalLink,
                resim as seoImage
            FROM 
                seo 
            WHERE 
                benzersizid = :uniqId
        ";
        $result = $this->db->select($sql, ["uniqId" => $uniqId]);

        if ($result) {
            return $result[0];
        } else {
            return false;
        }
    }

    public function getSeoBySeoId($seoId) {
        $sql = "SELECT * FROM seo WHERE seoid = :seoId";
        return $this->db->select($sql, ["seoId" => $seoId]);
    }

    public function validateSeoData($data) {
        $errors = [];

        // Benzersiz ID kontrolü
        if (!isset($data['seoUniqID']) || strlen($data['seoUniqID']) != 20) {
            $errors[] = "Benzersiz ID geçersiz";
        }

        // Başlık kontrolü
        if (!isset($data['seoTitle']) || strlen($data['seoTitle']) > 100) {
            $errors[] = "Başlık geçersiz veya çok uzun (100 karakterden fazla)";
        }

        // Açıklama kontrolü
        if (!isset($data['seoDescription']) || strlen($data['seoDescription']) > 355) {
            $errors[] = "Açıklama geçersiz veya çok uzun (355 karakterden fazla)";
        }

        // Anahtar kelime kontrolü
        if (!isset($data['seoKeywords']) || strlen($data['seoKeywords']) > 255) {
            $errors[] = "Anahtar kelimeler geçersiz veya çok uzun (255 karakterden fazla)";
        }

        // Link kontrolü
        if (!isset($data['seoLink']) || strlen($data['seoLink']) > 1000) {
            $errors[] = "Link geçersiz veya çok uzun (1000 karakterden fazla)";
        }

        // Orijinal Link kontrolü (boş olabilir)
        if (isset($data['seoOriginalLink']) && strlen($data['seoOriginalLink']) > 1000) {
            $errors[] = "Orijinal link çok uzun (1000 karakterden fazla)";
        }

        // Resim kontrolü (boş olabilir)
        if (isset($data['seoImage']) && strlen($data['seoImage']) > 4294967295) { // longtext limit
            $errors[] = "Resim verisi çok uzun (longtext sınırını aşıyor)";
        }

        return $errors;
    }

    public function getSeoKeywordsByCategoryID($categoryID){
        $sql = "
            Select 
                kelime 
            From 
                seo 
                    inner join sayfa on 
                        sayfa.benzersizid=seo.benzersizid 
                        inner join sayfalistekategori on 
                            sayfalistekategori.sayfaid=sayfa.sayfaid 
                            inner join kategori on 
                                kategori.kategoriid=sayfalistekategori.kategoriid
            Where 
                sayfalistekategori.kategoriid=:categoryID
            Group By 
                kelime
        ";

        $data = $this->db->select($sql, ['categoryID' => $categoryID]);

        if($data){
            $seoKeywords = [];
            foreach($data as $key => $value){
                $keywords = $value['kelime'];
                $keywords = trim($keywords, ",");
                $keywords = str_replace(",,", ",", $keywords);
                if(!empty($keywords)){
                    $allKeywords = explode(",", $keywords);
                    foreach($allKeywords as $keyword){
                        if(!in_array($keyword, $seoKeywords)){
                            $seoKeywords[] = $keyword;
                        }
                    }
                }
            }
            return $seoKeywords;
        } else {
            return [];
        }
    }

    public function getSeoOriginalLink($seoUniqID) {
        $sql = "SELECT link FROM seo WHERE benzersizid = :seoUniqID";
        $result = $this->db->select($sql, ["seoUniqID" => $seoUniqID]);

        if ($result) {
            return $result[0]['link'];
        } else {
            return false;
        }
    }

}