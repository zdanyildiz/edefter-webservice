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
class AdminSeo {

    private AdminDatabase $db;

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

    public function insertSeo($data) {

        $data["seoTitle"] = mb_substr($data["seoTitle"], 0, 100, 'UTF-8');
        $data["seoDescription"] = mb_substr($data["seoDescription"], 0, 355, 'UTF-8');
        $data["seoKeywords"] = mb_substr($data["seoKeywords"], 0, 255, 'UTF-8');


        $sql = "
            INSERT 
            INTO seo (benzersizid, baslik, aciklama, kelime, link, orjinallink, resim) 
            VALUES (:seoUniqID, :seoTitle, :seoDescription, :seoKeywords, :seoLink, :seoOriginalLink, :seoImage)
        ";

        $result = $this->db->insert($sql, $data);

        if ($result) {
            return [
                "status" => "success",
                "message" => "Seo eklendi"
            ];
        }
        else {
            return [
                "status" => "error",
                "message" => "Seo eklenemedi"
            ];
        }

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

    public function updateSeo($data) {

        $data["seoTitle"] = mb_substr($data["seoTitle"], 0, 100, 'UTF-8');
        $data["seoDescription"] = mb_substr($data["seoDescription"], 0, 355, 'UTF-8');
        $data["seoKeywords"] = mb_substr($data["seoKeywords"], 0, 255, 'UTF-8');


        $sql = "
            UPDATE 
                seo 
            SET 
                baslik = :seoTitle,
                aciklama = :seoDescription,
                kelime = :seoKeywords,
                link = :seoLink,
                resim = :seoImage
        ";

        // Eğer seoOriginalLink verisi gelmişse SQL sorgusuna ekleyin
        if (isset($data["seoOriginalLink"])) {
            $sql .= ", orjinallink = :seoOriginalLink";
        }

        $sql .= " WHERE benzersizid = :seoUniqID";

        $result = $this->db->update($sql, $data);

        if ($result>=0) {
            if($result>0){
                return [
                    "status" => "success",
                    "message" => "Seo güncellendi"
                ];
            }else{
                return [
                    "status" => "success",
                    "message" => "Seo verileri zaten güncel"
                ];

            }
        }
        else {
            return [
                "status" => "error",
                "message" => "Seo güncellenemedi"
            ];
        }
    }

    public function deleteSeo($seoUniqID)
    {
        $sql = "DELETE FROM seo WHERE benzersizid = :seoUniqID";
        $params = ["seoUniqID" => $seoUniqID];

        return $this->db->delete($sql, $params);
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

    public function createProductSeoLink($productName,$categoryHierarchy) {

        $categoryPath = '';
        foreach ($categoryHierarchy as $category) {
            $categoryPath .= '/' . $category['link'];
        }

        // Ürün adını URL dostu bir string'e dönüştürün
        $productName = strtolower($productName); // Küçük harfe çevirin
        $productName = preg_replace('/[^a-z0-9]+/', '-', $productName); // Alfanümerik olmayan karakterleri '-' ile değiştirin
        $productName = trim($productName, '-'); // Başındaki ve sonundaki '-' karakterlerini kaldırın

        // Ürünün SEO linkini oluşturun
        $productSeoLink = $categoryPath . '/' . $productName;

        return $productSeoLink;
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

    public function copyMainCategorySeo($newCategoryUniqID,$languageCode) {
        $sql = "
            SELECT 
                seo.baslik as seoTitle,
                seo.aciklama as seoDescription,
                seo.kelime as seoKeywords,
                seo.link as seoLink,
                seo.orjinallink as seoOriginalLink,
                seo.resim as seoImage
            FROM 
                seo 
                    INNER JOIN kategori on kategori.benzersizid = seo.benzersizid
            WHERE 
                dilid = 1 AND kategorisil = 0 and kategoriaktif = 1 and anasayfa = 1
        ";

        $seoData = $this->db->select($sql, []);

        if (empty($seoData)) {
            return [
                "status" => "error",
                "message" => "Ana kategori SEO verileri bulunamadı"
            ];
        }

        $seoData = $seoData[0];

        return [
            "status" => "success",
            "message" => "Ana kategori SEO verileri kopyalandı",
            "seoData" => $seoData
        ];

    }

    public function updateSeoByUniqId(string $uniqId, array $data)
    {
        // Bu fonksiyon tarafından güncellenmesine izin verilen alanları tanımlayın
        $allowedFields = ['seoTitle', 'seoDescription', 'seoKeywords'];

        // Gelen veri dizisini yalnızca izin verilen alanları içerecek şekilde filtreleyin
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($updateData)) {
            return [
                "status" => "error",
                "message" => "SEO için güncellenecek geçerli alan yok."
            ];
        }

        // Alan adlarını veritabanı sütun adlarıyla eşleştirin
        $fieldMapping = [
            'seoTitle' => 'baslik',
            'seoDescription' => 'aciklama',
            'seoKeywords' => 'kelime'
        ];

        $setClauses = [];
        $params = ['seoUniqID' => $uniqId];

        foreach ($updateData as $key => $value) {
            if (isset($fieldMapping[$key])) {
                $column = $fieldMapping[$key];
                $setClauses[] = "`{$column}` = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($setClauses)) {
            return [
                "status" => "error",
                "message" => "SEO için güncellenecek eşlenebilir alan yok."
            ];
        }

        $sql = "UPDATE seo SET " . implode(', ', $setClauses) . " WHERE benzersizid = :seoUniqID";

        $result = $this->db->update($sql, $params);

        if ($result >= 0) {
            return [
                "status" => "success",
                "message" => "Seo güncellendi"
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Seo güncellenemedi"
            ];
        }
    }
}