<?php
class AdminProductCategory
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getProductCategories($languageID)
    {
        $sql = "
            SELECT 
                kategoriid as productCategoryID, 
                kategoriad as productCategoryName, 
                kategori.benzersizid as productCategoryUniqID, 
                seo.link as productCategorySeoLink
            FROM 
                kategori
                INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
            WHERE 
                dilid = :languageID and kategorigrup=7 and kategorisil=0 and kategoriaktif=1 and ustkategoriid=0
        ";
        $data = $this->db->select($sql, ['languageID' => $languageID]);
        if (!empty($data)) {
            return $data;
        }
        else {
            return [];
        }
    }

    public function getProductCategory($categoryID)
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

    public function getProductCategoryBySearch($searchText,$languageID)
    {
        $sql = "
            SELECT 
                kategoriid as productCategoryID, 
                kategoriad as productCategoryName, 
                kategori.benzersizid as productCategoryUniqID, 
                seo.link as productCategorySeoLink
            FROM 
                kategori
                INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
            WHERE 
                dilid = :languageID and 
                kategoriad LIKE :searchText and 
                kategoriicerik LIKE :searchText1 and
                kategorigrup=7 and 
                kategorisil=0 and 
                kategoriaktif=1
        ";
        $data = $this->db->select($sql, ['languageID' => $languageID, 'searchText' => "%".$searchText."%", 'searchText1' => "%".$searchText."%"]);
        if (!empty($data)) {
            return $data;
        }
        else {
            return [];
        }

    }

    public function getSubCategories($categoryID)
    {
        $sql = "
            SELECT 
                kategoriid as productCategoryID, 
                kategoriad as productCategoryName, 
                kategori.benzersizid as productCategoryUniqID, 
                seo.link as productCategorySeoLink
            FROM 
                kategori
                INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
            WHERE 
                ustkategoriid = :categoryID and kategorisil=0 and kategoriaktif=1
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            return $data;
        } else {
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

    public function getCategoryHierarchyForExcel($categoryID)
    {
        $sql = "
            SELECT 
                kategoriid as categoryID,
                kategoriad as categoryName,
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
            $categoryHierarchy[] = $category['categoryName'];

            while ($category['parentCategoryID'] != 0) {
                $sql = "
                SELECT 
                    kategoriid as categoryID,
                    kategoriad as categoryName,
                    ustkategoriid as parentCategoryID
                FROM 
                    kategori
                WHERE 
                    kategoriid = :categoryID
            ";
                $data = $this->db->select($sql, ['categoryID' => $category['parentCategoryID']]);

                if (!empty($data)) {
                    $category = $data[0];
                    $categoryHierarchy[] = $category['categoryName'];
                } else {
                    break;
                }
            }

            // Kategori hiyerarşisini ters çevirip > işareti ile birleştiriyoruz
            return implode(' > ', array_reverse($categoryHierarchy));
        } else {
            return '';
        }
    }


    public function getCategoryProductTotalByCategoryID($categoryID)
    {
        $sql = "
            SELECT 
                COUNT(sayfalistekategori.sayfaid) as productCount
            FROM 
                sayfalistekategori
            WHERE 
                kategoriid = :categoryID
            GROUP BY 
                sayfaid
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {
            return count($data);
        }
        else {
            return 0;
        }

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

    public function insertProductCategory($insertData)
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

    public function updateProductCategory($updateData)
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

    public function getCategoryIdByLanguageIdAndParentIdAndName($languageID, $currentCategoryID, $categoryName)
    {
        $sql = "
            SELECT 
                kategori.kategoriid as categoryID
            FROM 
                kategori
            WHERE 
                kategori.dilid = :languageID AND kategori.ustkategoriid = :currentCategoryID AND kategori.kategoriad = :categoryName
        ";
        $data = $this->db->select($sql, ['languageID' => $languageID, 'currentCategoryID' => $currentCategoryID, 'categoryName' => $categoryName]);

        if (!empty($data)) {
            return $data[0]['categoryID'];
        }
        else {
            return 0;
        }
    }

    public function getGoogleCategoryName($categoryID){
        $sql = "
            SELECT 
                merchant_category_name
            FROM
                google_merchant_categories
            WHERE
                local_category_id = :categoryID
        ";
        $params = ["categoryID"=>$categoryID];
        return $this->db->select($sql,$params);
    }
    public function addGoogleCategory($categoryID, $googleCategory){
        $sql = "
            INSERT INTO google_merchant_categories SET
            local_category_id = :categoryID,
            merchant_category_name = :googleCategory
        ";
        $params = ["categoryID" => $categoryID, "googleCategory" => $googleCategory];
        return $this->db->insert($sql,$params);
    }

    public function deleteGoogleCategory($categoryID){
        $sql = "
            DELETE FROM google_merchant_categories WHERE local_category_id = :categoryID
        ";
        $params = ["categoryID" => $categoryID];
        return $this->db->delete($sql,$params);
    }
}