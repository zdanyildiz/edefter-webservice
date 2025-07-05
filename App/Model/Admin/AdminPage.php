<?php
class AdminPage
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /*
    public function getAllPages(): false|array
    {
        $sql = "SELECT 
                sayfa.*,
                seo.link, seo.baslik, seo.aciklama,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as resim_url,
                GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', ') as kategoriler 
                FROM sayfa
                LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                LEFT JOIN seo ON sayfa.benzersizid = seo.benzersizid
                GROUP BY sayfa.sayfaid;";

        return $this->db->select($sql);
    }
    */

    public function getPage($pageID)
    {
        $pageID = intval($pageID);
        if($pageID==0){
            return [];
        }

        $sql = "
            SELECT 
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfatariholustur as pageCreateDate,
                sayfatarihguncel as pageUpdateDate,
                sayfatip as pageType,
                sayfaad as pageName,
                sayfaicerik as pageContent,
                sayfalink as pageLink,
                sayfasira as pageOrder,
                sayfaaktif as pageActive,
                sayfasil as pageDeleted,
                sayfahit as pageHit,
                GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', ') as pageCategories,
                kategori.kategoriid as pageCategoryID,
                yetki as pageTypePermission,
                dilid as pageLanguageID
            FROM sayfa
                INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                    LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                        LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                    LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            WHERE 
                sayfa.sayfaid = :pageID
        ";


        $data = $this->db->select($sql,['pageID' => $pageID]);

        if(!$data || count($data)<=0){
            return [];
        }
        else{
            $data = $data[0];
            $data['pageImages'] = $this->getPageImages($pageID);
            $data['pageFiles'] = $this->getPageFiles($pageID);
            $data['pageGallery'] = $this->getPageGallery($pageID);
            $data['pageVideos'] = $this->getPageVideos($pageID);
            return $data;
        }
    }

    public function getPageByUniqID($pageUniqID){
        if(empty($pageUniqID)){
            return [];
        }

        $pageID = $this->getPageIDByUniqID($pageUniqID);

        return $this->getPage($pageID);
    }

    public function getPageUniqIDByID($id)
    {
        $sql = "SELECT benzersizid FROM sayfa WHERE sayfaid = :id";
        $result = $this->db->select($sql, ['id' => $id]);

        if($result && count($result)>0){
            return $result[0]['benzersizid'];
        }
        else{
            return "";
        }

    }

    public function getPageIDByUniqID($uniqID): int
    {
        $sql = "SELECT sayfaid FROM sayfa WHERE benzersizid = :uniqID";
        $result = $this->db->select($sql, ['uniqID' => $uniqID]);

        if($result && count($result)>0){
            return $result[0]['sayfaid'];
        }
        else{
            return 0;
        }
    }

    public function updatePage($updateData): array
    {
        $updatePageData = [
            'sayfatarihguncel' => $updateData['pageUpdateDate'],
            'sayfaid' => $updateData['pageID'],
            'sayfaad' => $updateData['pageName'],
            'sayfaicerik' => $updateData['pageContent'],
            'sayfaaktif' => $updateData['pageActive'] ?? 1,
            'sayfalink' => $updateData['pageLink']
        ];

        $sql ="
            UPDATE sayfa
            SET
                sayfatarihguncel = :sayfatarihguncel,
                sayfaad = :sayfaad,
                sayfaicerik = :sayfaicerik,
                sayfaaktif = :sayfaaktif,
                sayfalink = :sayfalink
            WHERE
                sayfaid = :sayfaid
        ";

        $updateResult = $this->db->update($sql, $updatePageData);

        if($updateResult){
            return [
                'status' => 'success',
                'message' => 'Sayfa güncellendi'
            ];
        }else{
            return [
                'status' => 'error',
                'message' => 'Sayfa güncellenemedi'
            ];
        }
    }

    public function insertPage($insertPageData): array
    {

        $sql ="
            INSERT INTO 
                sayfa 
                (benzersizid,sayfatariholustur,sayfatarihguncel,sayfatip,sayfaad,sayfaicerik,sayfasira,sayfalink,sayfaaktif,sayfasil,sayfahit)
                VALUES 
                (:pageUniqID,:pageCreateDate,:pageUpdateDate,:pageType,:pageName,:pageContent,:pageOrder,:pageLink,:pageActive,:pageDeleted,:pageHit)
        ";

        $insertResult = $this->db->insert($sql, $insertPageData);

        if($insertResult){
            return [
                'status' => 'success',
                'message' => 'Sayfa eklendi',
                'pageID' => $insertResult
            ];
        }
        else{
            return [
                'status' => 'error',
                'message' => 'Sayfa eklenemedi'
            ];
        }
    }

    public function deletePage($pageID){
        //sayfasil=1 yapalım
        $sql = "
            UPDATE sayfa
            SET
                sayfasil = 1
            WHERE
                sayfaid = :pageID
        ";

        return $this->db->update($sql, ['pageID' => $pageID]);
    }

    public function deletePageCategoryList($pageID){
        //sayfa kategorilerini sil
        $sql = "
            DELETE 
            FROM 
                sayfalistekategori
            WHERE
                sayfaid = :pageID
        ";

        return $this->db->delete($sql, ['pageID' => $pageID]);
    }

    public function updatePageOrder($updatePageOrderData)
    {
        $sql = "
            UPDATE sayfa
            SET
                sayfasira = :pageOrder
            WHERE
                sayfaid = :pageID
        ";

        $updateResult = $this->db->update($sql, $updatePageOrderData);

        if($updateResult>0){
            return [
                'status' => 'success',
                'message' => 'Sayfa sırası güncellendi'
            ];
        }
        elseif($updateResult == 0){
            return [
                'status' => 'success',
                'message' => 'Sayfa sırası güncell'
            ];
        }
        else{
            return [
                'status' => 'error',
                'message' => 'Sayfa sırası güncellenemedi'
            ];
        }
    }

    public function getPagesByCategoryID($categoryID,$params = [])
    {
        $orderBy = $params['orderBy'] ?? 'sayfa.sayfasira';
        $limit = $params['limit'] ?? 20;
        $currentPage = $params['currentPage'] ?? 1;
        $start = ($currentPage-1)*$limit;

        $sql = "
            SELECT 
                kategori.kategoriid as pageCategoryID,
                kategoriad as pageCategoryName,
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfaicerik as pageContent,
                sayfa.sayfalink as pageLink,
                sayfa.sayfaaktif as pageActive,
                sayfa.sayfasil as pageDeleted,
                sayfa.sayfahit as pageHit,
                sayfa.sayfasira as pageOrder,
                sayfa.sayfatip as pageType,
                sayfatip.yetki as pageTypePermission,
                GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', ') as pageCategories,
                GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', ') as pageImages,
                seo.link as pageSeoLink
            FROM
                sayfa
                    INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                    LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                        LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            
                    LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                        LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                            LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                    INNER JOIN seo ON sayfa.benzersizid = seo.benzersizid
            WHERE
                sayfa.sayfaaktif = 1 AND sayfa.sayfasil = 0 AND kategori.kategoriid = :categoryID
            GROUP BY
                sayfa.sayfaid
            ORDER BY 
                $orderBy
            LIMIT
                $start,$limit
        ";

        return $this->db->select($sql, ['categoryID' => $categoryID]);
    }

    public function updatePageCategory($updateData): array
    {

        $updatePageData = [
            'sayfaid' => $updateData['pageID'],
            'kategoriid' => $updateData['categoryID']
        ];

        $sql ="
            UPDATE sayfalistekategori
            SET
                kategoriid = :kategoriid
            WHERE
                sayfaid = :sayfaid
        ";

        $updateResult = $this->db->update($sql, $updatePageData);

        if($updateResult>0){
            return [
                'status' => 'success',
                'message' => 'Sayfa kategorisi güncellendi'
            ];
        }elseif($updateResult==0){
            return [
                'status' => 'success',
                'message' => 'Sayfa kategorisi güncell'
            ];
        }else{
            return [
                'status' => 'error',
                'message' => 'Sayfa kategorisi güncellenemedi'
            ];
        }
    }

    public function deletePageCategory($updateData): array
    {

        $sql ="
            DELETE 
            FROM 
                sayfalistekategori
            WHERE
                sayfaid = :pageID
        ";

        $deleteResult = $this->db->delete($sql, $updateData);

        if($deleteResult){
            return [
                'status' => 'success',
                'message' => 'Sayfa kategorisi silindi'
            ];
        }else{
            return [
                'status' => 'error',
                'message' => 'Sayfa kategorisi silinemedi'
            ];
        }
    }

    public function insertPageCategory($insertData): array
    {

        $sql ="
            INSERT INTO sayfalistekategori (sayfaid,kategoriid) VALUES (:pageID,:categoryID)
        ";

        $insertResult = $this->db->insert($sql, $insertData);

        if($insertResult){
            return [
                'status' => 'success',
                'message' => 'Sayfa kategori listesine eklendi'
            ];
        }else{
            return [
                'status' => 'error',
                'message' => 'Sayfa kategori listesine eklenemedi'
            ];
        }

    }

    public function deletePageImages($pageImageParams)
    {

        //önce sayfaid ye ait resimleri sil
        $sql ="
            DELETE 
            FROM 
                sayfalisteresim
            WHERE
                sayfaid = :pageID
        ";

        return $this->db->delete($sql, $pageImageParams);
    }

    public function insertPageImages($insertData): array
    {
        Log::adminWrite("PageController: insertPageImages -> insertData: " . json_encode($insertData), "info");

        $error = false;
        foreach($insertData['imageIDs'] as $imageID){
            Log::adminWrite("PageController: insertPageImages -> imageID: " . $imageID, "info");
            //tek tek ekleme yapalım
            $addSql = "INSERT INTO sayfalisteresim (sayfaid,resimid) VALUES (:sayfaid,:resimid)";
            $addResult = $this->db->insert($addSql, ['sayfaid' => $insertData['pageID'], 'resimid' => $imageID]);

            if(!$addResult){
                $error = true;
                break;
            }
        }

        if($error){
            Log::adminWrite("PageController: insertPageImages -> error", "info");
            return [
                'status' => 'error',
                'message' => 'Sayfa resimleri eklenemedi'
            ];
        }else{
            Log::adminWrite("PageController: insertPageImages -> success", "info");
            return [
                'status' => 'success',
                'message' => 'Sayfa resimleri eklendi'
            ];
        }
    }

    public function checkPage($productName,$productCategoryID){
        $sql = "
            SELECT 
                sayfa.sayfaid 
            FROM 
                sayfa 
                    INNER JOIN sayfalistekategori 
                        ON sayfa.sayfaid = sayfalistekategori.sayfaid
            WHERE 
                sayfaad = :sayfaad AND sayfalistekategori.kategoriid = :kategoriid and sayfa.sayfasil = 0
        ";

        $result = $this->db->select($sql, ['sayfaad' => $productName, 'kategoriid' => $productCategoryID]);

        if($result && count($result)>0){
            return $result[0]['sayfaid'];
        }
        else{
            return 0;
        }
    }

    public function checkPageWithPageID($pageID, $productName,$productCategoryID){
        $sql = "
            SELECT 
                sayfa.sayfaid 
            FROM 
                sayfa 
                    INNER JOIN sayfalistekategori 
                        ON sayfa.sayfaid = sayfalistekategori.sayfaid
            WHERE 
                sayfaad = :sayfaad AND sayfalistekategori.kategoriid = :kategoriid AND sayfa.sayfaid != :pageID AND sayfa.sayfasil = 0
        ";

        $result = $this->db->select($sql, ['sayfaad' => $productName, 'kategoriid' => $productCategoryID, 'pageID' => $pageID]);

        if($result && count($result)>0){
            return $result[0]['sayfaid'];
        }
        else{
            return 0;
        }
    }

    public function deletePageFiles($pageFileData): mixed
    {
        //önce sayfaid ye ait dosyaları sil
        $sql ="
            DELETE 
            FROM 
                sayfalistedosya
            WHERE
                sayfaid = :pageID
        ";

        return $this->db->delete($sql, $pageFileData);
    }

    public function insertPageFiles($insertData): array
    {
        $error = false;
        foreach($insertData['fileIDs'] as $fileID){
            //tek tek ekleme yapalım
            $addSql = "INSERT INTO sayfalistedosya (sayfaid,dosyaid) VALUES (:sayfaid,:dosyaid)";
            $addResult = $this->db->insert($addSql, ['sayfaid' => $insertData['pageID'], 'dosyaid' => $fileID]);

            if(!$addResult){
                $error = true;
                break;
            }
        }

        if($error){
            return [
                'status' => 'error',
                'message' => 'Sayfa dosyaları eklenemedi'
            ];
        }else{
            return [
                'status' => 'success',
                'message' => 'Sayfa dosyaları eklendi'
            ];
        }
    }

    public function deletePageVideos($pageData): mixed
    {
        //önce sayfaid ye ait dosyaları sil
        $sql ="
            DELETE 
            FROM 
                sayfalistevideo
            WHERE
                sayfaid = :pageID
        ";

        return $this->db->delete($sql, $pageData);
    }

    public function insertPageVideos($insertData): mixed
    {
        $sql ="
            INSERT INTO 
                sayfalistevideo
                (
                    sayfaid,
                    videoid
                )
                VALUES 
                (
                    :pageID,
                    :videoID
                )
        ";

        return $this->db->insert($sql, $insertData);
    }

    public function deletePageGallery($pageData): mixed
    {
        //önce sayfaid ye ait dosyaları sil
        $sql ="
            DELETE 
            FROM 
                sayfalistegaleri
            WHERE
                sayfaid = :pageID
        ";

        return $this->db->delete($sql, $pageData);
    }

    public function beginTransaction($funcName = "") {
        return $this->db->beginTransaction($funcName);
    }

    public function commit($funcName = "") {
        return $this->db->commit($funcName);
    }

    public function rollback($funcName = "") {
        return $this->db->rollback($funcName);
    }

    public function getPageTypes()
    {
        $sql = "
            SELECT 
                sayfatipad as pageTypeName, 
                sayfatipid as pageTypeID,
                yetki as pageTypePermission
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

    public function getPageImages($pageID) {
        // resimklasorad ve resimi concat yaparak resim_url oluşturulur
        $sql = "
        SELECT
            GROUP_CONCAT(
            CONCAT(
                'imageName:', REPLACE(resim.resimad, '|', '&#124;'), 
                '|imageID:', resim.resimid, 
                '|imageUrl:', resimklasor.resimklasorad, '/', REPLACE(resim.resim, '|', '&#124;')
            ) 
            SEPARATOR '||'
        ) as imageDetails
        FROM
            sayfalisteresim
                LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
        WHERE
            sayfalisteresim.sayfaid = :pageId
        ";
        $data = $this->db->select($sql, ['pageId' => $pageID]);
        if(!empty($data)){

            return $data[0]['imageDetails'];

        }
        else {
            return [];
        }
    }

    public function getPageFiles($pageID){
        $sql = "
            SELECT 
                GROUP_CONCAT(CONCAT('fileName:', dosya.dosyaad, ', fileID:', dosya.dosyaid, ', file:', dosya.dosya, ', fileExtension:', dosya.dosyauzanti) SEPARATOR '; ') as fileDetails
            FROM 
                sayfalistedosya
                    LEFT JOIN dosya ON sayfalistedosya.dosyaid = dosya.dosyaid
            WHERE
                sayfalistedosya.sayfaid = :pageId
        ";
        $data = $this->db->select($sql, ['pageId' => $pageID]);
        if(!empty($data)){

            return $data[0]['fileDetails'];

        }
        else {
            return [];
        }
    }

    public function getPageBySearch($languageID,$searchText)
    {
        $sql = "
            SELECT 
                kategori.kategoriid as pageCategoryID,
                kategoriad as pageCategoryName,
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfaicerik as pageContent,
                sayfa.sayfalink as pageLink,
                sayfa.sayfaaktif as pageActive,
                sayfa.sayfasil as pageDeleted,
                sayfa.sayfahit as pageHit,
                sayfa.sayfasira as pageOrder,
                sayfa.sayfatip as pageType,
                sayfatip.yetki as pageTypePermission,
                IFNULL(GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', '), '') as pageCategories,
                IFNULL(GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', '), '') as pageImages,
                seo.link as pageSeoLink
            FROM
                sayfa
                    INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                    LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                        LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            
                    LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                        LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                            LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                    INNER JOIN seo ON sayfa.benzersizid = seo.benzersizid
            WHERE 
                sayfasil=0 AND sayfaaktif=1 and kategori.dilid = :languageID AND sayfa.sayfaad LIKE :searchText
            GROUP BY
                pageID
            HAVING 
                pageID IS NOT NULL
        ";

        $data = $this->db->select($sql,['searchText' => '%'.$searchText.'%', 'languageID' => $languageID]);

        if(!$data || count($data)<=0){
            //Log::adminWrite("Anadilde arama başlatılıyor");
            $mainLanguageID = $this->getMainLanguageID();
            $data = $this->db->select($sql,['searchText' => '%'.$searchText.'%', 'languageID' => $mainLanguageID]);

            if(!$data || count($data)<=0){
                //Log::adminWrite("Anadilde arama sonucu yok");
                return [];
            }

            //Log::adminWrite(json_encode($data));

            $newData =[];
            foreach ($data as $originalPage){
                $originalPageId = $originalPage['pageID'];
                Log::adminWrite("Anadilde arama sonucu: ". $originalPage['pageID']);
                $newData[] = $this->getTranslatedPage($languageID,$originalPageId);

            }
            return $newData;
        }
        else{
            return $data;
        }
    }

    public function getTranslatedPage($languageID,$originalPageId){
        $sql = "
            Select 
                kategori.kategoriid as pageCategoryID,
                kategoriad as pageCategoryName,
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfaicerik as pageContent,
                sayfa.sayfalink as pageLink,
                sayfa.sayfaaktif as pageActive,
                sayfa.sayfasil as pageDeleted,
                sayfa.sayfahit as pageHit,
                sayfa.sayfasira as pageOrder,
                sayfa.sayfatip as pageType,
                sayfatip.yetki as pageTypePermission,
                IFNULL(GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', '), '') as pageCategories,
                IFNULL(GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', '), '') as pageImages,
                seo.link as pageSeoLink
            From 
                language_page_mapping 
                    INNER JOIN sayfa on language_page_mapping.translated_page_id = sayfa.sayfaid
                    INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                    LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                        LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            
                    LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                        LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                            LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                    INNER JOIN seo ON sayfa.benzersizid = seo.benzersizid
            Where 
                language_page_mapping.original_page_id = :originalPageID and language_page_mapping.dilid=:languageId
        ";
        $params = [
            'originalPageID' => $originalPageId,
            'languageId' => $languageID
        ];
        $resutl = $this->db->select($sql, $params);
        if(!$resutl|| count($resutl)<=0){
            return [];
        }
        else {
            return $resutl[0];
        }
    }

    public function getCategoryPages($mainCategoryID,$mainLanguageID){
        $sql = "
            SELECT 
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfaicerik as pageContent,
                sayfa.sayfalink as pageLink,
                sayfa.sayfaaktif as pageActive,
                sayfa.sayfasil as pageDeleted,
                sayfa.sayfahit as pageHit,
                sayfa.sayfasira as pageOrder,
                sayfa.sayfatip as pageType,
                sayfatip.yetki as pageTypePermission
            FROM
                sayfa
                    INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                    LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                        LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            WHERE 
                sayfasil=0 AND sayfaaktif=1 and kategori.dilid = :languageID AND kategori.kategoriid = :categoryID
            GROUP BY
                pageID
        ";

        return $this->db->select($sql,['categoryID' => $mainCategoryID, 'languageID' => $mainLanguageID]);
    }

    public function getPagesByLanguageID($languageID){

        $sql = "
            SELECT 
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfaicerik as pageContent,
                sayfa.sayfalink as pageLink,
                sayfa.sayfaaktif as pageActive,
                sayfa.sayfasil as pageDeleted,
                sayfa.sayfahit as pageHit,
                sayfa.sayfasira as pageOrder,
                sayfa.sayfatip as pageType
            FROM
                sayfa
                    INNER JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                        LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            WHERE 
                sayfasil=0 AND sayfaaktif=1 and kategori.dilid = :languageID
            GROUP BY
                pageID
        ";

        $result = $this->db->select($sql,['languageID' => $languageID]);

        if($result && count($result)>0){
            return $result;
        }
        else{
            return [];
        }
    }

    public function getPageCategory($pageID){
        $sql = "
            SELECT 
                kategori.kategoriid as categoryID,
                kategori.kategoriad as categoryName
            FROM 
                sayfalistekategori
                    INNER JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            WHERE 
                sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function getPageCategoryID($pageID){
        $sql = "
            SELECT 
                kategoriid as categoryID
            FROM 
                sayfalistekategori
            WHERE 
                sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function getPageGallery($pageID){
        $sql = "
            SELECT 
                resimgaleriid as galleryID
            FROM 
                sayfalistegaleri 
            WHERE sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function addPageGallery($pageID,$galleryID){
        $sql = "
            INSERT INTO 
                sayfalistegaleri 
            (
                sayfaid,
                resimgaleriid
            )
            VALUES 
            (
                :pageID,
                :galleryID
            )
        ";

        return $this->db->insert($sql, ['pageID' => $pageID, 'galleryID' => $galleryID]);
    }

    public function getPageVideos($pageID){
        $sql = "
            SELECT 
                videoid as videoID
            FROM 
                sayfalistevideo 
            WHERE sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function getAllPages($languageID)
    {
        $sql = "
            SELECT 
                sayfa.sayfaid as pageID,sayfaad as pageTitle
            FROM 
                sayfa
                    inner join sayfalistekategori on sayfa.sayfaid = sayfalistekategori.sayfaid
                    inner join kategori on sayfalistekategori.kategoriid = kategori.kategoriid
            WHERE 
                sayfasil = 0 and sayfaaktif=1 and kategori.dilid = :languageID
        ";
        $params = ['languageID' => $languageID];
        return $this->db->select($sql, $params);
    }

    public function getPageById(int $pageId)
    {
        $sql = "
            SELECT
                sayfaid as pageID,
                benzersizid as pageUniqID,
                sayfaad as pageName,
                sayfaicerik as pageContent,
                sayfatip as pageType,
                sayfasira as pageOrder,
                sayfalink as pageLink,
                sayfaaktif as pageActive
            FROM
                sayfa
            WHERE
                sayfaid = :pageId
        ";
        $result = $this->db->select($sql, ['pageId' => $pageId]);
        return $result ? $result[0] : null;
    }

    public function updatePageField(int $pageId, string $field, string $value)
    {
        // Güvenlik için alan adını beyaz listeye alın
        $allowedFields = ['sayfaad', 'sayfalink', 'sayfaicerik'];
        if (!in_array($field, $allowedFields)) {
            return false;
        }

        $sql = "UPDATE sayfa SET `{$field}` = :value WHERE sayfaid = :id";
        $params = [
            'value' => $value,
            'id' => $pageId
        ];
        return $this->db->update($sql, $params);
    }    
    
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
     * Çeviri durumu ile birlikte tüm sayfaları getirir
     * @param int $languageID Dil ID'si
     * @return array|false
     */
    public function getAllPagesWithTranslationStatus($languageID)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        $sql = "
            SELECT 
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfasira as pageOrder,
                sayfa.sayfaaktif as pageActive,
                sayfa.sayfasil as pageDeleted,
                kategori.kategoriad as pageCategoryName,
                kategori.kategoriid as pageCategoryID,
                sayfatip.yetki as pageTypePermission,
                seo.link as pageSeoLink,
                :currentLangID as currentLanguageID,
                :mainLangID as mainLanguageID,
                CASE 
                    WHEN :filterLangID = :compareLangID THEN 1 
                    ELSE 0 
                END as isMainLanguage
            FROM 
                sayfa
                INNER JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                INNER JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                LEFT JOIN seo ON sayfa.benzersizid = seo.benzersizid
            WHERE 
                sayfa.sayfasil = 0 
                AND sayfa.sayfaaktif = 1 
                AND kategori.dilid = :whereLangID
            ORDER BY 
                sayfa.sayfasira ASC, sayfa.sayfaid DESC 
        ";
        
        $params = [
            'currentLangID' => $languageID,
            'mainLangID' => $mainLanguageID,
            'filterLangID' => $languageID,
            'compareLangID' => $mainLanguageID,
            'whereLangID' => $languageID
        ];
        
        return $this->db->select($sql, $params);
    }

    /**
     * Belirli bir sayfa için çeviri durumlarını getirir
     * @param int $originalPageID Ana dildeki sayfa ID'si
     * @return array
     */
    public function getPageTranslationStatus($originalPageID)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        $sql = "
            SELECT 
                dil.dilid as languageID,
                dil.dilad as languageName,
                dil.dilkisa as languageCode,
                lpm.translation_status as translationStatus,
                lpm.last_attempt_date as translationDate,
                lpm.error_message as errorMessage,
                lpm.translated_page_id as translatedPageID
            FROM 
                dil
                LEFT JOIN language_page_mapping lpm ON (
                    dil.dilid = lpm.dilid 
                    AND lpm.original_page_id = :originalPageID
                )
            WHERE 
                dil.dilaktif = 1 
                AND dil.dilsil = 0
                AND dil.dilid != :mainLanguageID  -- Ana dil hariç
            ORDER BY 
                dil.dilsira ASC, dil.dilid ASC
        ";
        
        return $this->db->select($sql, [
            'originalPageID' => $originalPageID,
            'mainLanguageID' => $mainLanguageID
        ]);
    }

    /**
     * Çeviri durumu filtresi ile sayfaları getirir
     * @param int $languageID Dil ID'si
     * @param string $translationFilter Filtre türü: 'all', 'untranslated', 'pending', 'completed', 'failed'
     * @param int|null $targetLanguageID Hedef dil ID'si (isteğe bağlı)
     * @return array|false
     */
    public function getPagesByTranslationStatus($languageID, $translationFilter = 'all', $targetLanguageID = null)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        $baseSQL = "
            SELECT 
                sayfa.sayfaid as pageID,
                sayfa.benzersizid as pageUniqID,
                sayfa.sayfaad as pageName,
                sayfa.sayfasira as pageOrder,
                kategori.kategoriad as pageCategoryName,
                kategori.kategoriid as pageCategoryID,
                sayfatip.yetki as pageTypePermission,
                seo.link as pageSeoLink
            FROM 
                sayfa
                INNER JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                INNER JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                INNER JOIN sayfatip ON sayfa.sayfatip = sayfatip.sayfatipid
                LEFT JOIN seo ON sayfa.benzersizid = seo.benzersizid
        ";
        
        $whereConditions = [
            "sayfa.sayfasil = 0",
            "sayfa.sayfaaktif = 1", 
            "kategori.dilid = :languageID"
        ];
        
        $params = ['languageID' => $languageID];
        
        // Çeviri durumu filtresine göre WHERE koşulu ekle
        switch ($translationFilter) {
            case 'untranslated':
                if ($targetLanguageID) {
                    $whereConditions[] = "NOT EXISTS (
                        SELECT 1 FROM language_page_mapping lpm 
                        WHERE lpm.original_page_id = sayfa.sayfaid 
                        AND lpm.dilid = :targetLanguageID
                    )";
                    $params['targetLanguageID'] = $targetLanguageID;
                } else {
                    $whereConditions[] = "NOT EXISTS (
                        SELECT 1 FROM language_page_mapping lpm 
                        WHERE lpm.original_page_id = sayfa.sayfaid 
                        AND lpm.dilid != :mainLanguageID
                    )";
                    $params['mainLanguageID'] = $mainLanguageID;
                }
                break;
                
            case 'pending':
                if ($targetLanguageID) {
                    $whereConditions[] = "EXISTS (
                        SELECT 1 FROM language_page_mapping lpm 
                        WHERE lpm.original_page_id = sayfa.sayfaid 
                        AND lpm.dilid = :targetLanguageID 
                        AND lpm.translation_status = 'pending'
                    )";
                    $params['targetLanguageID'] = $targetLanguageID;
                }
                break;
                
            case 'completed':
                if ($targetLanguageID) {
                    $whereConditions[] = "EXISTS (
                        SELECT 1 FROM language_page_mapping lpm 
                        WHERE lpm.original_page_id = sayfa.sayfaid 
                        AND lpm.dilid = :targetLanguageID 
                        AND lpm.translation_status = 'completed'
                    )";
                    $params['targetLanguageID'] = $targetLanguageID;
                }
                break;
                
            case 'failed':
                if ($targetLanguageID) {
                    $whereConditions[] = "EXISTS (
                        SELECT 1 FROM language_page_mapping lpm 
                        WHERE lpm.original_page_id = sayfa.sayfaid 
                        AND lpm.dilid = :targetLanguageID 
                        AND lpm.translation_status = 'failed'
                    )";
                    $params['targetLanguageID'] = $targetLanguageID;
                }
                break;
        }
          $sql = $baseSQL . " WHERE " . implode(' AND ', $whereConditions) . " ORDER BY sayfa.sayfasira ASC, sayfa.sayfaid DESC";
        
        return $this->db->select($sql, $params);
    }

    /**
     * Ana dilde olmayan bir sayfanın ana dil karşılığını kontrol eder
     * @param int $currentPageID Mevcut sayfa ID'si  
     * @param int $currentLanguageID Mevcut dil ID'si
     * @return array|null Ana dil sayfası bilgisi veya null
     */
    public function getMainLanguageEquivalent($currentPageID, $currentLanguageID)
    {
        $mainLanguageID = $this->getMainLanguageID();
        
        // Eğer zaten ana dildeyse null döndür
        if ($currentLanguageID == $mainLanguageID) {
            return null;
        }
        
        $sql = "
            SELECT 
                main_page.sayfaid as mainPageID,
                main_page.sayfaad as mainPageName,
                main_dil.dilad as mainLanguageName
            FROM 
                language_page_mapping lpm
                INNER JOIN sayfa main_page ON lpm.original_page_id = main_page.sayfaid
                INNER JOIN dil main_dil ON main_dil.dilid = :mainLanguageID
            WHERE 
                lpm.translated_page_id = :currentPageID 
                AND lpm.dilid = :currentLanguageID
            LIMIT 1
        ";
        
        $result = $this->db->select($sql, [
            'currentPageID' => $currentPageID,
            'currentLanguageID' => $currentLanguageID,
            'mainLanguageID' => $mainLanguageID
        ]);
        
        return $result ? $result[0] : null;
    }

    /**
     * Bir sayfanın ana dilde olup olmadığını kontrol eder
     * @param int $languageID Dil ID'si
     * @return bool Ana dilde ise true
     */
    public function isMainLanguage($languageID): bool
    {
        $mainLanguageID = $this->getMainLanguageID();
        return $languageID == $mainLanguageID;
    }

    /**
     * Belirli bir orijinal sayfanın belirli bir hedef dile çeviri durumunu getirir.
     * @param int $originalPageID Orijinal sayfa ID'si
     * @param int $targetLanguageID Hedef dil ID'si
     * @return array|null Çeviri durumu bilgisi veya null
     */
    public function getSpecificTranslationStatus(int $originalPageID, int $targetLanguageID): ?array
    {
        $sql = "
            SELECT 
                lpm.translation_status as translationStatus,
                lpm.last_attempt_date as translationDate,
                lpm.error_message as errorMessage,
                lpm.translated_page_id as translatedPageID
            FROM 
                language_page_mapping lpm
            WHERE 
                lpm.original_page_id = :originalPageID
                AND lpm.dilid = :targetLanguageID
            LIMIT 1
        ";
        
        $result = $this->db->select($sql, [
            'originalPageID' => $originalPageID,
            'targetLanguageID' => $targetLanguageID
        ]);
        
        return $result ? $result[0] : null;
    }
}