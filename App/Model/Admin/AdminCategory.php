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
class AdminCategory
{
    private $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getCategories($languageID)
    {
        $sql = "
            SELECT 
                kategoriid as categoryID, 
                kategoriad as categoryName, 
                kategori.benzersizid as categoryUniqID, 
                seo.link as categorySeoLink
            FROM 
                kategori
                INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
            WHERE 
                dilid = :languageID and kategorigrup!=7 and kategorisil=0 and ustkategoriid=0
        ";
        $data = $this->db->select($sql, ['languageID' => $languageID]);
        if (!empty($data)) {
            return $data;
        }
        else {
            return [];
        }
    }

    public function getCategory($categoryID)
    {

        $sql = "
            SELECT 
                kategoriid as categoryID,
                ustkategoriid as topCategoryID,
                kategoriad as categoryName, 
                kategori.benzersizid as categoryUniqID, 
                kategoriicerik as categoryContent,
                kategori.resimid as categoryImageID,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as categoryImage,
                kategorisira as categoryOrder,
                kategorisiralama as categorySorting,
                kategorigrup as categoryType,
                anasayfa as homePage,
                kategoriaktif as categoryActive,
                kategorisil as categoryDeleted,
                kategori.kategorilink as categoryLink,
                seo.baslik as categorySeoTitle,
                seo.aciklama as categorySeoDescription,
                seo.kelime as categorySeoKeywords,
                dilid as languageID
                
            FROM 
                kategori
                    INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
                    LEFT JOIN resim ON kategori.resimid=resim.resimid
                        LEFT JOIN resimklasor ON resimklasor.resimklasorid=resim.resimklasorid
            WHERE 
                kategoriid = :categoryID
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            return $data[0];
        }
        else {
            return [];
        }

    }
    public function getAllProductCategories($languageID)
    {
        $sql = "
            SELECT 
                * 
            FROM 
                kategori
            WHERE
                kategorisil=0 and kategorigrup=7 and ustkategoriid=0 and 
                dilid = :languageID
        ";

        $result = $this->db->select($sql, ['languageID' => $languageID]);

        if(empty($result)){
            return [];
        }

        //sütun isimlerini ingilizceye çevirelim
        $result = array_map(function($item){
            return array(
                'categoryID' => $item['kategoriid'],
                'categoryLanguageID' => $item['dilid'],
                'categoryCreationDate' => $item['kategoritariholustur'],
                'categoryUpdateDate' => $item['kategoritarihguncel'],
                'categoryParentID' => $item['ustkategoriid'],
                'categoryLayer' => $item['kategorikatman'],
                'categoryName' => $item['kategoriad'],
                'categoryImageID' => $item['resimid'],
                'categoryContent' => $item['kategoriicerik'],
                'categoryLink' => $item['kategorilink'],
                'categoryOrder' => $item['kategorisira'],
                'categorySorting' => $item['kategorisiralama'],
                'categoryGroup' => $item['kategorigrup'],
                'categoryHomePage' => $item['anasayfa'],
                'categoryActive' => $item['kategoriaktif'],
                'categoryDelete' => $item['kategorisil'],
                'categoryUniqueID' => $item['benzersizid']
            );
        }, $result);

        return $result;
    }

    public function getAllCategories($languageID)
    {
        $sql = "
        SELECT 
            * 
        FROM 
            kategori
        WHERE
            kategorisil=0 AND kategoriaktif=1 AND dilid = :languageID
        ORDER BY
            CASE 
                WHEN ustkategoriid = 0 THEN kategoriid
                ELSE ustkategoriid
            END,
            ustkategoriid,
            kategoriid
    ";

        return $this->db->select($sql, ['languageID' => $languageID]);
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
            $uniqueID = $result[0]['benzersizid'];

        }

        $sql = "
            SELECT 
                kategori.*,seo.link,seo.baslik,seo.aciklama,dil.dilid,dilkisa,
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

        if(empty($data)){
            return [];
        }
        
        //sütun isimlerini ingilizceye çevirelim
        $data = array_map(function($item){
            return array(
                'categoryID' => $item['kategoriid'],
                'categoryLanguageID' => $item['dilid'],
                'categoryCreationDate' => $item['kategoritariholustur'],
                'categoryUpdateDate' => $item['kategoritarihguncel'],
                'categoryParentID' => $item['ustkategoriid'],
                'categoryLayer' => $item['kategorikatman'],
                'categoryName' => $item['kategoriad'],
                'categoryImageID' => $item['resimid'],
                'categoryContent' => $item['kategoriicerik'],
                'categoryLink' => $item['kategorilink'],
                'categoryOrder' => $item['kategorisira'],
                'categorySorting' => $item['kategorisiralama'],
                'categoryGroup' => $item['kategorigrup'],
                'categoryHomePage' => $item['anasayfa'],
                'categoryActive' => $item['kategoriaktif'],
                'categoryDelete' => $item['kategorisil'],
                'categoryUniqueID' => $item['benzersizid'],
                'categorySeoLink' => $item['link'],
                'categorySeoTitle' => $item['baslik'],
                'categorySeoDescription' => $item['aciklama'],
                'categoryLanguageShort' => $item['dilkisa'],
                'categoryImageURL' => $item['resim_url']
            );
        }, $data);

        $data = $data[0]; // Tek bir kategori döndüğü için ilk elemanı alalım

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

    public function getCategoryUniqIDByID($categoryID)
    {
        $sql = "
            SELECT 
                benzersizid 
            FROM 
                kategori 
            WHERE 
                kategorisil=0 and kategoriaktif=1 and kategoriid = :categoryID
        ";

        $result = $this->db->select($sql,["categoryID" => $categoryID]);

        if(empty($result)){
            return[];
        }

        return $result[0]['benzersizid'];

    }

    public function getPagesOfCategory($categoryId,$filter = [])
    {
        if ($categoryId == 0) {
            return [];
        }
        $where = "";
        $param = ['categoryId' => $categoryId];
        $innerJoin = "";

        $jsonData = $this->json->readJson(["Category/Pages", $categoryId]);
        if (!empty($jsonData)) {
            return $jsonData;
        }



        $sql = "
            SELECT 
                sayfa.sayfaid
            FROM sayfa
                JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                $innerJoin
            WHERE 
                sayfalistekategori.kategoriid = :categoryId AND sayfa.sayfaaktif = 1 AND sayfa.sayfasil = 0 $where
            GROUP BY sayfa.sayfaid";

        $data = $this->db->select($sql,$param);
        if (!empty($data)&&empty($filterNames)) {
            $this->json->createJson(["Category/Pages", $categoryId], $data);
        }
        else{
            // kategorinin sayfaları yoksa üst kategori olabilir,
            // bu kategorinin altındaki kategorilerin sayfalarını çekelim

            $sql = "
                SELECT 
                    kategori.kategoriid
                FROM kategori
                WHERE 
                    ustkategoriid = :categoryId";
            $categoryData = $this->db->select($sql,$param);
            foreach ($categoryData as $category){
                // $categrory['kategoriid'] alt kategorinin id'sini alıp getPagesOfCategory metodunu tekrar çalıştıralım
                $jsonData = $this->getPagesOfCategory($category['kategoriid'],$filter);
                //dönen sonuçları toplayıp data değişkenine atayalım
                if (!empty($jsonData)) {
                    $data = array_merge($data,$jsonData);
                }
            }
        }
        return $data;
    }

    public function getCategoryHierarchy($categoryID)
    {
        $sql = "
            SELECT 
                kategoriid as categoryID,
                kategoriad as categoryName,
                kategorilink as categoryLink,
                ustkategoriid as parentCategoryID
            FROM 
                kategori
            WHERE 
                kategoriid = :categoryID
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            $category = $data[0];
            $categoryHierarchy = [];
            $categoryHierarchy[] = $category;
            while ($category['parentCategoryID'] != 0) {
                $sql = "
                    SELECT 
                        kategoriid as categoryID,
                        kategoriad as categoryName,
                        kategorilink as categoryLink,
                        ustkategoriid as parentCategoryID
                    FROM 
                        kategori
                    WHERE 
                        kategoriid = :categoryID
                ";
                $data = $this->db->select($sql, ['categoryID' => $category['parentCategoryID']]);
                if (!empty($data)) {
                    $category = $data[0];
                    $categoryHierarchy[] = $category;
                } else {
                    break;
                }
            }
            return array_reverse($categoryHierarchy);
        }
        else {
            return [];
        }
    }

    public function getSubcategories($categoryId)
    {

        $sql = "
            SELECT 
                kategoriid AS categoryID,
                dilid AS categoryLanguageID,
                kategoritariholustur AS categoryCreationDate,
                kategoritarihguncel AS categoryUpdateDate,
                ustkategoriid AS categoryParentID,
                kategorikatman AS categoryLayer,
                kategoriad AS categoryName,
                resimid AS categoryImageID,
                kategoriicerik AS categoryContent,
                kategorilink AS categoryLink,
                kategorisira AS categoryOrder,
                kategorisiralama AS categorySorting,
                kategorigrup AS categoryGroup,
                anasayfa AS categoryHomePage,
                kategoriaktif AS categoryActive,
                kategorisil AS categoryDelete,
                kategori.benzersizid AS categoryUniqueID,
                seo.baslik AS categorySeoTitle,
                seo.aciklama AS categorySeoDescription,
                seo.kelime AS categorySeoKeywords,
                seo.link AS categorySeoLink
            FROM 
                kategori
                INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
            WHERE 
                kategorisil=0 AND kategoriaktif=1 AND kategori.ustkategoriid = :categoryId";
        $subcategories = $this->db->select($sql, ['categoryId' => $categoryId]);

        if (!empty($subcategories)) {
            // Her alt kategori için özyinelemeli olarak getSubcategories metodunu çalıştır:
            foreach ($subcategories as &$subcategory) {
                $subcategory['subcategories'] = $this->getSubcategories($subcategory['categoryID']);
            }
        }

        return $subcategories;
    }

    public function getSubcategory($categoryId, $languageId = null)
    {
        $sql = "
            SELECT 
                *
            FROM 
                kategori
            WHERE 
                kategorisil=0 AND kategoriaktif=1 AND kategori.ustkategoriid = :categoryId";
        
        $params = ['categoryId' => $categoryId];
        
        // Eğer dil ID'si belirtilmişse, dil filtrelemesi ekle
        if ($languageId !== null) {
            $sql .= " AND dilid = :languageId";
            $params['languageId'] = $languageId;
        }
        
        $subcategories = $this->db->select($sql, $params);

        if (!empty($subcategories)) {
            //sütun isimlerini ingilizceye çevirelim
            $subcategories = array_map(function($item){
                return array(
                    'categoryID' => $item['kategoriid'],
                    'categoryLanguageID' => $item['dilid'],
                    'categoryCreationDate' => $item['kategoritariholustur'],
                    'categoryUpdateDate' => $item['kategoritarihguncel'],
                    'categoryParentID' => $item['ustkategoriid'],
                    'categoryLayer' => $item['kategorikatman'],
                    'categoryName' => $item['kategoriad'],
                    'categoryImageID' => $item['resimid'],
                    'categoryContent' => $item['kategoriicerik'],
                    'categoryLink' => $item['kategorilink'],
                    'categoryOrder' => $item['kategorisira'],
                    'categorySorting' => $item['kategorisiralama'],
                    'categoryGroup' => $item['kategorigrup'],
                    'categoryType' => $item['kategorigrup'],
                    'categoryHomePage' => $item['anasayfa'],
                    'categoryActive' => $item['kategoriaktif'],
                    'categoryDelete' => $item['kategorisil'],
                    'categoryDeleted' => $item['kategorisil'],
                    'categoryUniqueID' => $item['benzersizid']
                );
            }, $subcategories);

        }
        else{
            $subcategories = [];
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

    public function getCategoryTypes()
    {
        $sql = "
            SELECT 
                sayfatipad as categoryTypeName, 
                sayfatipid as categoryTypeID,
                yetki as categoryTypePermission
            FROM 
                sayfatip
            WHERE 
                sayfatipsil = 0
            ORDER BY 
                sayfatipad
        ";
        $result = $this->db->select($sql);
        return $result;
    }

    public function begintransaction($title="")
    {
        $this->db->beginTransaction($title);
    }

    public function commit($title="")
    {
        $this->db->commit($title);
    }

    public function rollback($title="")
    {
        $this->db->rollback($title);
    }

    public function insertCategory($insertData)
    {
        $sql = "
            INSERT INTO 
                kategori 
            SET 
                dilid = :languageID,
                kategoritariholustur = :createDate,
                kategoritarihguncel = :updateDate,
                ustkategoriid = :topCategoryID,
                kategorikatman = :categoryLayer,
                kategoriad = :categoryName,
                resimid = :categoryImageID,
                kategoriicerik = :categoryContent,
                kategorilink = :categoryLink,
                kategorisira = :categoryOrder,
                kategorisiralama = :categorySorting,
                kategorigrup = :categoryType,
                anasayfa = :categoryHomePage,
                kategoriaktif = :categoryActive,
                kategorisil = :categoryDeleted,
                benzersizid = :categoryUniqID
        ";

        $result = $this->db->insert($sql, $insertData);

        if ($result) {
            return [
                'status' => "success",
                'message' => 'Kategori eklendi',
                'categoryID' => $this->db->pdo->lastInsertId()
            ];
        }
        else {
            return [
                'status' => "error",
                'message' => 'Kategori eklenemedi'
            ];
        }
    }

    public function updateCategory($updateData)
    {
        $sql = "
            UPDATE 
                kategori 
            SET 
                kategoritarihguncel = :updateDate,
                ustkategoriid = :topCategoryID,
                kategorikatman = :categoryLayer,
                kategoriad = :categoryName,
                resimid = :categoryImageID,
                kategoriicerik = :categoryContent,
                kategorilink = :categoryLink,
                kategorisira = :categoryOrder,
                kategorisiralama = :categorySorting,
                kategorigrup = :categoryType,
                anasayfa = :categoryHomePage,
                kategoriaktif = :categoryActive,
                kategorisil = :categoryDeleted
            WHERE
                kategoriid = :categoryID
        ";

        $result = $this->db->update($sql, $updateData);

        if ($result>=0) {
            if ($result > 0) {
                return [
                    'status' => 'success',
                    'message' => 'Kategori güncellendi'
                ];
            } else {
                return [
                    'status' => 'success',
                    'message' => 'Kategori güncellenemedi, veriler zaten güncel'
                ];
            }
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Kategori güncellenemedi'
            ];
        }
    }

    public function getCategoryUniqID($categoryID)
    {
        $sql = "
            SELECT 
                benzersizid as categoryUniqID
            FROM 
                kategori
            WHERE 
                kategoriid = :categoryID
        ";

        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            return $data[0]['categoryUniqID'];
        }
        else {
            return '';
        }
    }

    public function getCategoryImages($categoryID)
    {
        $sql = "
            SELECT 
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as imagePath
            FROM 
                kategori
                    INNER JOIN resim ON kategori.resimid=resim.resimid
                        INNER JOIN resimklasor ON resimklasor.resimklasorid=resim.resimklasorid
            WHERE 
                kategoriid = :categoryID
        ";

        $data = $this->db->select($sql, ['categoryID' => $categoryID]);

        if (!empty($data)) {
            return $data[0];
        }
        else {
            return [];
        }

    }

    public function getSubCategoryCount($categoryID)
    {
        $sql = "
            SELECT 
                COUNT(kategoriid) as subCategoryCount
            FROM 
                kategori
            WHERE 
                ustkategoriid = :categoryID and kategorisil=0 and kategoriaktif=1
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            return $data[0]['subCategoryCount'];
        } else {
            return 0;
        }

    }

    public function getCategoryPageCount($categoryID)
    {
        $sql = "
            SELECT 
                COUNT(sayfaid) as pageCount
            FROM 
                sayfalistekategori
            WHERE 
                kategoriid = :categoryID
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            return $data[0]['pageCount'];
        } else {
            return 0;
        }
    }

    public function getCategoryBySearch($searchText,$languageID)
    {
        $sql = "
            SELECT 
                kategoriid as categoryID, kategoriad as categoryName, kategori.benzersizid as categoryUniqID, seo.link as categorySeoLink
            FROM 
                kategori
                INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
            WHERE 
                dilid = :languageID and kategoriad LIKE :searchText and kategorigrup!=7 and kategorisil=0 
        ";

        $data = $this->db->select($sql, ['languageID' => $languageID, 'searchText' => "%".$searchText."%"]);

        if (!empty($data)) {
            return $data;
        }
        else {
            return [];
        }

    }

    public function getCategoryLanguageID($categoryID)
    {
        $sql = "
            SELECT 
                dilid as languageID
            FROM 
                kategori
            WHERE 
                kategoriid = :categoryID
        ";

        $data = $this->db->select($sql, ['categoryID' => $categoryID]);

        if (!empty($data)) {
            return $data[0]['languageID'];
        }
        else {
            return 0;
        }
    }

    public function copyMainCategories()
    {
        //anadil'e (1) bağlı anasayfa kategorisini seo bilgileriyle çekip yeni dilin altına ekleyelim
        $sql = "
            SELECT 
                kategoriid as categoryID,
                dilid as languageID,
                kategoritariholustur as categoryCreationDate,
                kategoritarihguncel as categoryUpdateDate,
                ustkategoriid as topCategoryID,
                kategorikatman as categoryLayer,
                kategoriad as categoryName,
                resimid as categoryImageID,
                kategoriicerik as categoryContent,
                kategorilink as categoryLink,
                kategorisira as categoryOrder,
                kategorisiralama as categorySorting,
                kategorigrup as categoryType,
                anasayfa as homePage,
                kategoriaktif as categoryActive,
                kategorisil as categoryDeleted,
                benzersizid as categoryUniqueID
            FROM 
                kategori
            WHERE 
                dilid = 1 and kategorisil=0 and kategoriaktif=1 and anasayfa=1
        ";

        $mainCategory = $this->db->select($sql);

        if(empty($mainCategory)){
            return [
                'status' => 'error',
                'message' => 'Main category not found'
            ];
        }

        $mainCategory = $mainCategory[0];
        return [
            'status' => 'success',
            'message' => 'Main category copied',
            'mainCategory' => $mainCategory
        ];
    }

    public function deleteCategory($categoryID)
    {
        $sql = "
            UPDATE 
                kategori 
            SET 
                kategorisil = 1
            WHERE 
                kategoriid = :categoryID
        ";

        return $this->db->update($sql, ['categoryID' => $categoryID]);
    }

    public function updateCategoryField(int $categoryId, string $field, string $value)
    {
        // Güvenlik için alan adını beyaz listeye alın
        $allowedFields = ['kategoriad', 'kategorilink', 'kategoriicerik'];
        if (!in_array($field, $allowedFields)) {
            return false;
        }

        $sql = "UPDATE kategori SET `{$field}` = :value WHERE kategoriid = :id";
        $params = [
            'value' => $value,
            'id' => $categoryId
        ];
        return $this->db->update($sql, $params);
    }

    public function updateCategoryUniqID(int $categoryId, string $newUniqID)
    {
        $sql = "UPDATE kategori SET benzersizid = :newUniqID WHERE kategoriid = :categoryId";
        $params = [
            'newUniqID' => $newUniqID,
            'categoryId' => $categoryId
        ];
        return $this->db->update($sql, $params);
    }

    /**
     * Kategorileri çeviri durumu ile birlikte al
     * @param int $languageID Seçilen dil ID'si
     * @param string $translationFilter Filtre türü: 'all', 'untranslated', 'pending', 'completed', 'failed'
     * @param string $searchText Arama metni
     * @param int $parentCategoryID Üst kategori ID'si (0 = ana kategoriler)
     * @return array
     */



    /**
     * Ana dil ID'sini dinamik olarak getirir
     * @return int
     */
    private function getMainLanguageID(): int {
        $sql = "SELECT dilid FROM dil WHERE anadil = 1 AND dilsil = 0 AND dilaktif=1 LIMIT 1";
        $result = $this->db->select($sql);
        return $result ? (int)$result[0]['dilid'] : 1; // Varsayılan olarak 1 döndür
    }

    /**
     * Kategorileri çeviri durumu ile birlikte al
     * @param int $languageID Seçilen dil ID'si
     * @param string $translationFilter Filtre türü: 'all', 'untranslated', 'pending', 'completed', 'failed'
     * @param string $searchText Arama metni
     * @param int $parentCategoryID Üst kategori ID'si (0 = ana kategoriler)
     * @return array
     */
    public function getCategoriesWithTranslationStatus(int $languageID, string $translationFilter = 'all', string $searchText = '', int $parentCategoryID = 0)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        $isMainLanguage = ($languageID == $mainLanguageID);
        
        $baseSQL = "
            SELECT 
                k.kategoriid as categoryID,
                k.kategoriad as categoryName, 
                k.benzersizid as categoryUniqID,
                k.kategorisira as categoryOrder,
                k.ustkategoriid as parentCategoryID,
                s.link as categorySeoLink,
                (SELECT COUNT(*) FROM kategori WHERE ustkategoriid = k.kategoriid AND kategorisil = 0 AND kategoriaktif = 1) as subCategoryCount
            FROM 
                kategori k
                INNER JOIN seo s ON k.benzersizid = s.benzersizid
        ";
        
        $whereConditions = [
            "k.kategorisil = 0",
            "k.kategoriaktif = 1", 
            "k.dilid = :languageID",
            "k.ustkategoriid = :parentCategoryID"
        ];
        
        $params = [
            'languageID' => $languageID,
            'parentCategoryID' => $parentCategoryID
        ];
        
        if (!empty($searchText)) {
            $whereConditions[] = "k.kategoriad LIKE :searchText";
            $params['searchText'] = '%' . $searchText . '%';
        }
        
        // Çeviri durumu filtresine göre WHERE koşulu ekle
        switch ($translationFilter) {
            case 'untranslated':
                $whereConditions[] = "NOT EXISTS (
                    SELECT 1 FROM language_category_mapping lcm 
                    WHERE lcm.original_category_id = k.kategoriid 
                    AND lcm.dilid != :mainLanguageID
                )";
                $params['mainLanguageID'] = $mainLanguageID;
                break;
                
            case 'pending':
                $whereConditions[] = "EXISTS (
                    SELECT 1 FROM language_category_mapping lcm 
                    WHERE lcm.original_category_id = k.kategoriid 
                    AND lcm.dilid != :mainLanguageID 
                    AND lcm.translation_status = 'pending'
                )";
                $params['mainLanguageID'] = $mainLanguageID;
                break;
                
            case 'completed':
                $whereConditions[] = "EXISTS (
                    SELECT 1 FROM language_category_mapping lcm 
                    WHERE lcm.original_category_id = k.kategoriid 
                    AND lcm.dilid != :mainLanguageID 
                    AND lcm.translation_status = 'completed'
                )";
                $params['mainLanguageID'] = $mainLanguageID;
                break;
                
            case 'failed':
                $whereConditions[] = "EXISTS (
                    SELECT 1 FROM language_category_mapping lcm 
                    WHERE lcm.original_category_id = k.kategoriid 
                    AND lcm.dilid != :mainLanguageID 
                    AND lcm.translation_status = 'failed'
                )";
                $params['mainLanguageID'] = $mainLanguageID;
                break;
        }
        
        $sql = $baseSQL . " WHERE " . implode(' AND ', $whereConditions) . " ORDER BY k.kategorisira ASC, k.kategoriid ASC";
        
        $categories = $this->db->select($sql, $params);
        
        foreach ($categories as &$category) {
            $category['isMainLanguage'] = $isMainLanguage;
            if ($isMainLanguage) {
                $category['translationDetails'] = $this->getCategoryTranslationStatus($category['categoryID']);
                $category['mainLanguageEquivalent'] = null;
            } else {
                $mainLangEquivalent = $this->getMainLanguageEquivalent($category['categoryID'], $languageID);
                $category['mainLanguageEquivalent'] = $mainLangEquivalent;

                if ($mainLangEquivalent) {
                    $translationStatus = $this->getSpecificTranslationStatus($mainLangEquivalent['mainCategoryID'], $languageID);
                    $category['translationDetails'] = $translationStatus ? [$translationStatus] : [];
                } else {
                    $category['translationDetails'] = $this->getCategoryTranslationStatus($category['categoryID']);
                }
            }
        }
        
        return $categories ?: [];
    }

    /**
     * Belirli bir kategori için çeviri durumunu al
     * @param int $categoryID Kategori ID
     * @return array
     */
    public function getCategoryTranslationStatus(int $categoryID)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        $sql = "
            SELECT 
                d.dilid as languageID,
                d.dilad as languageName,
                d.dilkisa as languageCode,
                lcm.translation_status as translationStatus,
                lcm.last_attempt_date as translationDate,
                lcm.error_message as errorMessage,
                lcm.translated_category_id as translatedCategoryID
            FROM 
                dil d
                LEFT JOIN language_category_mapping lcm ON (
                    d.dilid = lcm.dilid 
                    AND lcm.original_category_id = :originalCategoryID
                )
            WHERE 
                d.dilaktif = 1 
                AND d.dilsil = 0
                AND d.dilid != :mainLanguageID
            ORDER BY 
                d.dilsira ASC, d.dilid ASC
        ";
        
        return $this->db->select($sql, [
            'originalCategoryID' => $categoryID,
            'mainLanguageID' => $mainLanguageID
        ]) ?: [];
    }

    /**
     * Ana dilde olmayan bir kategorinin ana dil karşılığını kontrol eder
     * @param int $currentCategoryID Mevcut kategori ID'si  
     * @param int $currentLanguageID Mevcut dil ID'si
     * @return array|null Ana dil kategori bilgisi veya null
     */
    public function getMainLanguageEquivalent($currentCategoryID, $currentLanguageID)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        if ($currentLanguageID == $mainLanguageID) {
            return null;
        }
        
        $sql = "
            SELECT 
                main_cat.kategoriid as mainCategoryID,
                main_cat.kategoriad as mainCategoryName,
                main_dil.dilad as mainLanguageName
            FROM 
                language_category_mapping lcm
                INNER JOIN kategori main_cat ON lcm.original_category_id = main_cat.kategoriid
                INNER JOIN dil main_dil ON main_dil.dilid = :mainLanguageID
            WHERE 
                lcm.translated_category_id = :currentCategoryID 
                AND lcm.dilid = :currentLanguageID
            LIMIT 1
        ";
        
        $result = $this->db->select($sql, [
            'currentCategoryID' => $currentCategoryID,
            'currentLanguageID' => $currentLanguageID,
            'mainLanguageID' => $mainLanguageID
        ]);
        
        return $result ? $result[0] : null;
    }

    /**
     * Belirli bir orijinal kategorinin belirli bir hedef dile çeviri durumunu getirir.
     * @param int $originalCategoryID Orijinal kategori ID'si
     * @param int $targetLanguageID Hedef dil ID'si
     * @return array|null Çeviri durumu bilgisi veya null
     */
    public function getSpecificTranslationStatus(int $originalCategoryID, int $targetLanguageID): ?array
    {
        $sql = "
            SELECT 
                lcm.translation_status as translationStatus,
                lcm.last_attempt_date as translationDate,
                lcm.error_message as errorMessage,
                lcm.translated_category_id as translatedCategoryID
            FROM 
                language_category_mapping lcm
            WHERE 
                lcm.original_category_id = :originalCategoryID
                AND lcm.dilid = :targetLanguageID
            LIMIT 1
        ";
        
        $result = $this->db->select($sql, [
            'originalCategoryID' => $originalCategoryID,
            'targetLanguageID' => $targetLanguageID
        ]);
        
        return $result ? $result[0] : null;
    }
}