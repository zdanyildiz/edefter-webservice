<?php
/**
 * Class Category
 *
 * Veritabanındaki 'kategori' tablosunu temsil eder.
 *
 * @property int $kategoriid
 * @property int $dilid
 * @property datetime $kategoritariholustur
 * @property datetime $kategoritarihguncel
 * @property int $ustkategoriid
 * @property int $kategorikatman
 * @property string $kategoriad
 * @property int|null $resimid
 * @property string|null $kategoriicerik
 * @property string|null $kategorilink
 * @property int $kategorisira
 * @property int $kategorisiralama
 * @property int $kategorigrup
 * @property int $anasayfa
 * @property int $kategoriaktif
 * @property int $kategorisil
 * @property string $benzersizid
 */

class Category
{
    private $db;
    private $json;

    public function __construct(Database $db,$json)
    {
        $this->db = $db;
        $this->json = $json;
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM kategori";
        return $this->db->select($sql);
    }

    public function getCategoryByIdOrUniqId($id=0,$uniqID="")
    {
        if($id==0 && $uniqID==""){
            return [];
        }
        //id boş değilse veritabanından uniqID alalım ve json varsa veri çekelim
        if($id!=0){
            //die("$id");
            $sql = "
                SELECT 
                    benzersizid 
                FROM 
                    kategori 
                WHERE kategorisil=0 and kategoriaktif=1 and kategoriid = :id";
            $result = $this->db->select($sql,["id" => $id]);

            if(empty($result)){
                return[];
            }
            $uniqID = $result[0]['benzersizid'];

        }


        // eğer uniqID boş değilse
        if (!empty($uniqID)) {
            $jsonData = $this->json->readJson(["Category", $uniqID]);
            if (!empty($jsonData)) {
                return $jsonData;
            }
        }

        $sql = "
            SELECT 
                kategori.*,
                seo.link,
                seo.baslik,
                seo.aciklama,
                dil.dilid,dilkisa,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as resim_url 
            FROM kategori
                LEFT JOIN resim ON kategori.resimid = resim.resimid
                LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                INNER JOIN dil ON kategori.dilid = dil.dilid
                INNER JOIN seo ON kategori.benzersizid = seo.benzersizid
        ";

        if ($id != 0 && $uniqID != "") {
            $sql .= "WHERE (kategori.kategoriid = :id OR kategori.benzersizid = :uniqID)";
            $param = ['id' => $id, 'uniqID' => $uniqID];
        } elseif ($id != 0) {
            $sql .= "WHERE kategori.kategoriid = :id";
            $param = ['id' => $id];
        } else {
            $sql .= "WHERE kategori.benzersizid = :uniqID";
            $param = ['uniqID' => $uniqID];
        }

        $data = $this->db->select($sql,$param);
        if (!empty($data)) {
            $this->json->createJson(["Category", $data[0]['benzersizid']], $data);
        }
        return $data;
    }

    public function getCategoryIdByUniqId($uniqID="")
    {
        if($uniqID==""){
            return [];
        }
        //id boş değilse veritabanından uniqID alalım

        $sql = "
            SELECT 
                kategoriid 
            FROM 
                kategori 
            WHERE 
                kategorisil=0 and kategoriaktif=1 and benzersizid = :uniqID";
        $result = $this->db->select($sql,["uniqID" => $uniqID]);

        if(empty($result)){
            return[];
        }

        return $result[0]['kategoriid'];
    }

    public function getPagesOfCategory($categoryId,$filter,$sortBy)
    {
        if ($categoryId == 0) {
            return [];
        }
        $orderByClause = "";
        $sortMap = [
            0 => "sayfa.sayfatariholustur ASC, sayfa.sayfaid ASC",  // İlk Eklenen En Üste
            1 => "sayfa.sayfatariholustur DESC, sayfa.sayfaid DESC", // Son Eklenen En Üste
            2 => "sayfa.sayfatarihguncel ASC",                      // Güncelleme Tarihi Eskiden Yeniye
            3 => "sayfa.sayfatarihguncel DESC",                     // Güncelleme Tarihi Yeniden Eskiye
            4 => "sayfa.sayfasira ASC",                             // Sayfa Sırası Küçükten Büyüğe
            5 => "sayfa.sayfasira DESC",                            // Sayfa Sırası Büyükten Küçüğe
            6 => "sayfa.sayfaad ASC",                               // Sayfa Adı A-Z
            7 => "sayfa.sayfaad DESC",                              // Sayfa Adı Z-A
        ];

        if ($sortBy !== null && isset($sortMap[(int)$sortBy])) {
            $orderByClause = "ORDER BY " . $sortMap[(int)$sortBy];
        }

        $where = "";
        $executeParams = ['categoryId' => $categoryId];
        $innerJoin = "";

        if(!empty($filter)){

            $i=0;
            foreach ($filter as $key => $value) {
                $i++;
                $where .= " AND JSON_SEARCH(variantProperties, 'one', :key$i, NULL, '$[*].variantProperties[*].attribute.name') IS NOT NULL AND JSON_SEARCH(variantProperties, 'one', :value$i, NULL, '$[*].variantProperties[*].attribute.value') IS NOT NULL";
                $executeParams[":key$i"] = $key;
                $executeParams[":value$i"] = rawurldecode($value);
            }

            //print_r($executeParams);exit();

            $innerJoin = "INNER JOIN urunozellikleri ON sayfa.sayfaid = urunozellikleri.sayfaid";
        }


        $sql = "
            SELECT 
                sayfa.sayfaid
            FROM sayfa
                JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                $innerJoin
            WHERE 
                sayfalistekategori.kategoriid = :categoryId AND sayfa.sayfaaktif = 1 AND sayfa.sayfasil = 0 $where
            GROUP BY 
                sayfa.sayfaid
            $orderByClause
        ";

        $executeParamsJson = json_encode($executeParams);
        $uniqSql = md5($sql.$where.$executeParamsJson);

        $jsonData = $this->json->readJson(["Category/Pages", $categoryId."-".$uniqSql]);

        if (!empty($jsonData)) {
            return $jsonData;
        }

        $data = $this->db->select($sql,$executeParams);

        if (!empty($data)&&empty($filterNames)) {
            $this->json->createJson(["Category/Pages", $categoryId."-".$uniqSql], $data);
        }
        else
        {
            // kategorinin sayfaları yoksa üst kategori olabilir,
            // bu kategorinin altındaki kategorilerin sayfalarını çekelim

            /*$sql = "
                SELECT 
                    kategori.kategoriid
                FROM 
                    kategori
                WHERE 
                    ustkategoriid = :categoryId
            ";

            $categoryData = $this->db->select($sql,['categoryId' => $categoryId]);

            foreach ($categoryData as $category){
                // $categrory['kategoriid'] alt kategorinin id'sini alıp getPagesOfCategory metodunu tekrar çalıştıralım
                $jsonData = $this->getPagesOfCategory($category['kategoriid'],$filter);
                //dönen sonuçları toplayıp data değişkenine atayalım
                if (!empty($jsonData)) {
                    if (is_array($data)) {
                        $data = array_merge($data, $jsonData);
                    } else {
                        $data = $jsonData;
                    }
                }
            }*/
        }
        return is_array($data) ? $data : [];
    }

    public function getCategoryHierarchy($categoryId, $fromTopToBottom = true)
    {
        $categoryHierarchy = [];
        $currentCatId = $categoryId;

        do {
            $sql = "
                SELECT 
                    kategori.kategoriad,kategori.dilid,kategori.ustkategoriid,seo.link
                FROM kategori
                    LEFT JOIN seo ON kategori.benzersizid = seo.benzersizid
                WHERE kategori.kategoriid = :currentCatId";

            $category = $this->db->select($sql, ['currentCatId' => $currentCatId]);
            if (!empty($category)) {
                $category = $category[0];

                if ($fromTopToBottom) {
                    array_unshift($categoryHierarchy, $category);
                } else {
                    array_push($categoryHierarchy, $category);
                }

                $currentCatId = $category['ustkategoriid'];
            }else{
                $category['dilid'] = 1;
            }
        } while ($currentCatId != 0);

        // Anasayfayı ekleyelim
        $sql = "
            SELECT 
                kategori.kategoriad,
                seo.link,
                kategori.kategoriid
            FROM 
                kategori
                LEFT JOIN seo ON kategori.benzersizid = seo.benzersizid
            WHERE 
                kategori.anasayfa = 1 AND kategori.dilid = :dilid
        ";

        $homepage = $this->db->select($sql, ['dilid' => $category['dilid']]);
        //print_r($homepage);exit();
        if ($homepage) {
            if($categoryId!=$homepage[0]['kategoriid']){
                if ($fromTopToBottom) {
                    array_unshift($categoryHierarchy, $homepage[0]);
                } else {
                    array_push($categoryHierarchy, $homepage[0]);
                }
            }

        }
        //print_r($categoryHierarchy);exit();
        return $categoryHierarchy;
    }

    public function getSubcategories($categoryId)
    {
        $jsonData = $this->json->readJson(["Category/Subcategories", $categoryId]);
        if (!empty($jsonData)) {
            foreach ($jsonData as $subcategory) {
                $jsonSubCategoryData = $this->getSubcategories($subcategory['kategoriid']);
                if (empty($jsonSubCategoryData)) {
                    $this->getSubcategories($subcategory['kategoriid']);
                }
            }
            return $jsonData;
        }

        $sql = "
            SELECT 
                kategoriid
            FROM 
                kategori
            WHERE 
                kategorisil=0 AND kategoriaktif=1 AND kategori.ustkategoriid = :categoryId";
        $subcategories = $this->db->select($sql, ['categoryId' => $categoryId]);
        //print_r($subcategories);exit();
        if (!empty($subcategories)) {
            $this->json->createJson(["Category/Subcategories", $categoryId], $subcategories);
            // Her alt kategori için özyinelemeli olarak getSubcategories metodunu çalıştır:
            foreach ($subcategories as &$subcategory) {
                $subcategory['subcategories'] = $this->getSubcategories($subcategory['kategoriid']);
            }
        }

        return $subcategories;
    }

    public function getCategoryByPageID($pageID)
    {
        $sql = "
            SELECT 
                *
            FROM 
                kategori
                INNER JOIN sayfalistekategori ON kategori.kategoriid = sayfalistekategori.kategoriid
            WHERE 
                sayfalistekategori.sayfaid = :pageID";
        $result = $this->db->select($sql, ['pageID' => $pageID]);
        return $result ? $result[0] : null;
    }
}