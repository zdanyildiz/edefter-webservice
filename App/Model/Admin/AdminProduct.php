<?php
class AdminProduct
{
    private AdminDatabase $db;
    public string $productSql;
    public Json $json;

    public int $resultsPerPage = 20;
    public int $currentPage = 1;

    public function __construct(AdminDatabase $db,Config $config) {
        $this->db = $db;
        $this->json = $config->Json;

        $this->productSql = "
            SELECT 
                urunozellikleri.*,
                sayfa.*,
                kategori.kategoriid,kategori.kategoriad, kategori.dilid,
                seo.baslik,seo.aciklama,seo.kelime,seo.link,
                urunparabirim.parabirimad,urunparabirim.parabirimkod,urunparabirim.parabirimsimge
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
                    INNER JOIN seo ON seo.benzersizid=sayfa.benzersizid
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
            WHERE 
                sayfasil=0
        ";

        $this->checkTable();
    }

    private function checkTable()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS variant_properties (
                id INT AUTO_INCREMENT PRIMARY KEY,
                variant_id VARCHAR(255) NOT NULL,
                variant_stock_code VARCHAR(255) NOT NULL,
                variant_quantity INT NOT NULL,
                variant_selling_price DECIMAL(10, 2) NOT NULL,
                variant_image_ids TEXT NULL,
                attribute_name VARCHAR(255) NOT NULL,
                attribute_value VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        if ($this->db->createTable($query)) {
            //Log::adminWrite("Table 'variant_properties' checked/created successfully.", "info");
        } else {
            Log::adminWrite("Failed to check/create 'variant_properties' table.", "error");
        }
    }

    // Varyant ekleme fonksiyonu
    public function addVariantProperty($variantData)
    {
        $query = "
            INSERT INTO variant_properties 
            (variant_id, variant_stock_code, variant_quantity, variant_selling_price, variant_image_ids, attribute_name, attribute_value)
            VALUES 
            (:variant_id, :variant_stock_code, :variant_quantity, :variant_selling_price, :variant_image_ids, :attribute_name, :attribute_value)
        ";

        return $this->db->insert($query, [
            ':variant_id' => $variantData['variant_id'],
            ':variant_stock_code' => $variantData['variant_stock_code'],
            ':variant_quantity' => $variantData['variant_quantity'],
            ':variant_selling_price' => $variantData['variant_selling_price'],
            ':variant_image_ids' => $variantData['variant_image_ids'],
            ':attribute_name' => $variantData['attribute_name'],
            ':attribute_value' => $variantData['attribute_value']
        ]);
    }

    // Varyant güncelleme fonksiyonu
    public function updateVariantProperty($id, $variantData)
    {
        $query = "
            UPDATE variant_properties SET
                variant_id = :variant_id,
                variant_stock_code = :variant_stock_code,
                variant_quantity = :variant_quantity,
                variant_selling_price = :variant_selling_price,
                variant_image_ids = :variant_image_ids,
                attribute_name = :attribute_name,
                attribute_value = :attribute_value,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ";

        return $this->db->update($query, [
            ':id' => $id,
            ':variant_id' => $variantData['variant_id'],
            ':variant_stock_code' => $variantData['variant_stock_code'],
            ':variant_quantity' => $variantData['variant_quantity'],
            ':variant_selling_price' => $variantData['variant_selling_price'],
            ':variant_image_ids' => $variantData['variant_image_ids'],
            ':attribute_name' => $variantData['attribute_name'],
            ':attribute_value' => $variantData['attribute_value']
        ]);
    }

    // Varyant silme fonksiyonu
    public function deleteVariantProperty($productID)
    {
        $query = "DELETE FROM variant_properties WHERE variant_id = :id";
        return $this->db->delete($query, [':id' => $productID]);
    }

    // Varyant özelliklerini getir fonksiyonu
    public function getVariantProperties($variantId)
    {
        $query = "SELECT * FROM variant_properties WHERE variant_id = :variant_id";
        return $this->db->select($query, [':variant_id' => $variantId]);
    }

    public function getProduct($productID): array{
        $productSql = $this->productSql . " AND urunozellikleri.sayfaid = :productID";
        $params = array(
            'productID' => $productID
        );
        $product = $this->db->select($productSql, $params);

        if($product){
            $product = $product[0];
            //print_r($product);exit();

            $productLanguageID = $product['dilid'];
            $productCategoryID = $product['kategoriid'];
            $productCategoryName = $product['kategoriad'];

            $productID = $product['sayfaid'];
            $productUniqueID = $product['benzersizid'];
            $productName = $product['sayfaad'];
            $productContent = $product['sayfaicerik'];
            $productLink = $product['sayfalink'];
            $productOrder = $product['sayfasira'];
            $productActive = $product['sayfaaktif'];
            $productUpdateDate = $product['sayfatarihguncel'];

            $productImages = $this->getProductImages($productID);
            $productFiles = $this->getProductFiles($productID);
            $productVideos = $this->getProductVideos($productID);
            $productGallery = $this->getProductGallery($productID);

            $productSeoTitle = $product['baslik'];
            $productSeoDescription = $product['aciklama'];
            $productSeoKeywords = $product['kelime'];
            $productSeoLink = $product['link'];

            $productGroupID = $product['urungrupid'];
            $productBrandID = $product['markaid'];
            $productSupplierID = $product['tedarikciid'];
            $productModel = $product['urunmodel'];

            $productSubTitle = $product['urunaltbaslik'];
            $productShortDesc = $product['urunhediye'];
            $productDescription = $product['urunaciklama'];

            $productCargoTime = $product['urunkargosuresi'];
            $productFixedCargoPrice = $product['urunsabitkargoucreti'];
            $productSalePrice = $product['urunsatisfiyat'];
            $productNonDiscountedPrice = $product['urunindirimsizfiyat'];
            $productDealerPrice = $product['urunbayifiyat'];
            $productPurchasePrice = $product['urunalisfiyat'];
            $productMarketplacePrice = $product['urunpazaryerifiyat'];

            $productShowOldPrice = $product['uruneskifiyatgoster'];
            $productInstallment = $product['uruntaksit'];
            $productTax = $product['urunkdv'];
            $productStock = $product['urunstok'];
            $productStockCode = $product['urunstokkodu'];

            $productDiscountRate = $product['urunindirimorani'];

            $productPriceLastDate = $product['urunfiyatsontarih'];
            $productHomePage = $product['urunanasayfa'];
            $productDiscounted = $product['urunindirimde'];
            $productNew = $product['urunyeni'];
            $productBulkDiscount = $product['uruntopluindirim'];
            $productSameDayShipping = $product['urunanindakargo'];
            $productFreeShipping = $product['urunucretsizkargo'];
            $productPreOrder = $product['urunonsiparis'];
            $productPriceAsk = $product['urunfiyatsor'];
            $productCargo = $product['urunkargo'];
            $productCurrency = $product['urunparabirim'];
            $productCurrencySymbol = $product['parabirimsimge'];

            $productDayOpportunity = $product['urungununfirsati'];
            $productCreditCard = $product['urunkredikarti'];
            $productCashOnDelivery = $product['urunkapidaodeme'];
            $productBankTransfer = $product['urunhavaleodeme'];
            $productSalesQuantity = $product['urunsatisadet'];
            $productDiscountRateShow = $product['urunindirimoranigoster'];


            $productGTIN = $product['urungtin'];
            $productMPN = $product['urunmpn'];
            $productBarcode = $product['urunbarkod'];
            $productOEM = $product['urunoem'];
            $productDesi = $product['urundesi'];
            $productMinimumQuantity = $product['urunminimummiktar'];
            $productMaximumQuantity = $product['urunmaksimummiktar'];
            $productCoefficient = $product['urunkatsayi'];
            $productQuantityUnitID = $product['urunmiktarbirimid'];
            $productVariantProperties = !empty($product['variantProperties']) ? json_decode($product['variantProperties'], true) : [];
            $productProperties = !empty($product['product_properties']) ? json_decode($product['product_properties'], true) : [];

            return [
                "productLanguageID" => $productLanguageID,
                "productID" => $productID,
                "productUniqueID" => $productUniqueID,
                "productName" => $productName,
                "productContent" => $productContent,
                "productLink" => $productLink,
                "productOrder" => $productOrder,
                "productActive" => $productActive,
                "productUpdateDate" => $productUpdateDate,
                "productImages" => $productImages,
                "productFiles" => $productFiles,
                "productVideos" => $productVideos,
                "productGallery" => $productGallery,
                "productCategoryID" => $productCategoryID,
                "productCategoryName" => $productCategoryName,
                "productSeoTitle" => $productSeoTitle,
                "productSeoDescription" => $productSeoDescription,
                "productSeoKeywords" => $productSeoKeywords,
                "productSeoLink" => $productSeoLink,
                "productGroupID" => $productGroupID,
                "productBrandID" => $productBrandID,
                "productSupplierID" => $productSupplierID,
                "productSubTitle" => $productSubTitle,
                "productShortDesc" => $productShortDesc,
                "productDescription" => $productDescription,
                "productCargoTime" => $productCargoTime,
                "productFixedCargoPrice" => $productFixedCargoPrice,
                "productSalePrice" => $productSalePrice,
                "productNonDiscountedPrice" => $productNonDiscountedPrice,
                "productDealerPrice" => $productDealerPrice,
                "productPurchasePrice" => $productPurchasePrice,
                "productMarketplacePrice" => $productMarketplacePrice,
                "productShowOldPrice" => $productShowOldPrice,
                "productInstallment" => $productInstallment,
                "productTax" => $productTax,
                "productStock" => $productStock,
                "productStockCode" => $productStockCode,
                "productModel" => $productModel,
                "productDiscountRate" => $productDiscountRate,
                "productPriceLastDate" => $productPriceLastDate,
                "productHomePage" => $productHomePage,
                "productDiscounted" => $productDiscounted,
                "productNew" => $productNew,
                "productBulkDiscount" => $productBulkDiscount,
                "productDiscountedShipping" => $productSameDayShipping,
                "productFreeShipping" => $productFreeShipping,
                "productPreOrder" => $productPreOrder,
                "productPriceAsk" => $productPriceAsk,
                "productCargo" => $productCargo,
                "productCurrency" => $productCurrency,
                "productCurrencySymbol" => $productCurrencySymbol,
                "productDayOpportunity" => $productDayOpportunity,
                "productCreditCard" => $productCreditCard,
                "productCashOnDelivery" => $productCashOnDelivery,
                "productBankTransfer" => $productBankTransfer,
                "productSalesQuantity" => $productSalesQuantity,
                "productDiscountRateShow" => $productDiscountRateShow,
                "productGTIN" => $productGTIN,
                "productMPN" => $productMPN,
                "productBarcode" => $productBarcode,
                "productOEM" => $productOEM,
                "productDesi" => $productDesi,
                "productMinimumQuantity" => $productMinimumQuantity,
                "productMaximumQuantity" => $productMaximumQuantity,
                "productCoefficient" => $productCoefficient,
                "productQuantityUnitID" => $productQuantityUnitID,
                "productVariantProperties" => $productVariantProperties,
                "productProperties" => $productProperties
            ];
        }
        else {
            return [];
        }
    }

    /**
     * @param $languageID
     * @return array|false
     */

    public function getAllProductsForExcel($languageID){
        $sql = "
            SELECT
                sayfa.sayfaid as productID,
                sayfa.sayfaad as productName,
                sayfa.sayfaaktif as productActive,
                sayfa.sayfaicerik as productDescription,
                urunozellikleri.urunaltbaslik as productShortDescription,
                kategori.kategoriid as productCategoryID,
                urunmarka.markaad as productBrand,
                parabirimkod as productCurrency,
                urunozellikleri.urunkdv as productTax,
                urunozellikleri.variantProperties as productVariants,
                urunozellikleri.product_properties as productProperties,
                urunozellikleri.urunkargosuresi as productCargo,
                urunozellikleri.urunmodel as productModel
                
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
                    INNER JOIN seo ON seo.benzersizid=sayfa.benzersizid
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
                INNER JOIN urunmarka ON urunmarka.markaid=urunozellikleri.markaid
            WHERE 
                sayfasil=0 AND kategori.dilid = :languageID
        ";
        $params = array('languageID' => $languageID);
        return $this->db->select($sql, $params);
    }

    public function getProductsByCategoryID($categoryID)
    {
        $sql = "
            SELECT 
                urunozellikleri.sayfaid as productID
            FROM 
                urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE 
                sayfasil=0 and sayfaaktif=1 and kategori.kategoriid = :categoryID
            GROUP BY 
                urunozellikleri.sayfaid
        ";
        $data = $this->db->select($sql, ['categoryID' => $categoryID]);
        if (!empty($data)) {

            return $data;
        }
        else {
            return [];
        }

    }

    public function getProductImages($productID) {
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
        $data = $this->db->select($sql, ['pageId' => $productID]);
        if(!empty($data)){

            return $data[0]['imageDetails'];

        }
        else {
            return [];
        }
    }

    public function getProductImagesForExcel($productID, $domain)
    {
        // Resim bilgilerini al ve URL'lerini oluştur
        $sql = "
            SELECT
                GROUP_CONCAT(
                    CONCAT(
                        'https://', :domain, '/Public/Image/',
                        resimklasor.resimklasorad, '/', REPLACE(resim.resim, '|', '&#124;')
                    ) 
                    SEPARATOR ','
                ) as imageUrls
            FROM
                sayfalisteresim
                    LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                    LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
            WHERE
                sayfalisteresim.sayfaid = :pageId
        ";
        $data = $this->db->select($sql, ['pageId' => $productID, 'domain' => $domain]);

        if (!empty($data)) {
            return $data[0]['imageUrls'];
        } else {
            return '';
        }
    }

    public function getProductFiles($productID){
        $sql = "
            SELECT 
                GROUP_CONCAT(CONCAT('fileName:', dosya.dosyaad, ', fileID:', dosya.dosyaid, ', file:', dosya.dosya, ', fileExtension:', dosya.dosyauzanti) SEPARATOR '; ') as fileDetails
            FROM 
                sayfalistedosya
                    LEFT JOIN dosya ON sayfalistedosya.dosyaid = dosya.dosyaid
            WHERE
                sayfalistedosya.sayfaid = :pageId
        ";
        $data = $this->db->select($sql, ['pageId' => $productID]);
        if(!empty($data)){

            return $data[0]['fileDetails'];

        }
        else {
            return [];
        }
    }

    public function getProductGroups(): array{
        $sql = "
            SELECT 
                urungrupid as productGroupID,
                urungrupad as productGroupName,
                urungrupaciklama as productGroupDescription,
                urungrupkdv as productGroupTax,
                urungrupindirim as productGroupDiscount,
                urungrupfiyateski as productGroupOldPrice,
                urungrupfiyatsontarih as productGroupPriceLastDate,
                urungruptaksit as productGroupInstallment,
                urungruphediye as productGroupGift,
                urungrupaciklamakisa as productGroupShortDescription,
                urungrupkargosuresi as productGroupCargoTime,
                urungrupsabitkargoucreti as productGroupFixedCargoPrice,
                benzersizid as productGroupUniqueID
            FROM 
                urungrup
            WHERE 
                urungrupsil=0
        ";
        $data = $this->db->select($sql);

        if(!empty($data)){
            return $data;
        }
        else {
            return [];
        }
    }

    /*
    public function getProductGroupByGroupID($groupID){
        $sql = "
            SELECT
                urungrup.*
            FROM
                urungrup
            WHERE
                urungrupsil=0 AND urungrupid = :groupID
        ";
        $data = $this->db->select($sql, ['groupID' => $groupID]);
        if(!empty($data)){
            $data = $data[0];
            $data['productGroupID'] = $data['urungrupid'];
            $data['productGroupName'] = $data['urungrupad'];
            $data['productGroupDescription'] = $data['urungrupaciklama'];
            $data['productGroupTax'] = $data['urungrupkdv'];
            $data['productGroupDiscount'] = $data['urungrupindirim'];
            $data['productGroupOldPrice'] = $data['urungrupfiyateski'];
            $data['productGroupPriceLastDate'] = $data['urungrupfiyatsontarih'];
            $data['productGroupInstallment'] = $data['urungruptaksit'];
            $data['productGroupGift'] = $data['urungruphediye'];
            $data['productGroupShortDescription'] = $data['urungrupaciklamakisa'];
            $data['productGroupCargoTime'] = $data['urungrupkargosuresi'];
            $data['productGroupFixedCargoPrice'] = $data['urungrupsabitkargoucreti'];
            $data['productGroupUniqueID'] = $data['benzersizid'];
            return $data;
        }
        else {
            return [];
        }
    }
    */
    public function productSearch($params): array
    {

        $resultsPerPage = $this->resultsPerPage;
        $currentPage = $this->currentPage;


        if(empty($params['q'])) {
            return [];
        }
        else{
            $term = $params['q'];

            $where = "(
                sayfa.sayfaad LIKE :term OR 
                sayfa.sayfaicerik LIKE :term1 OR 
                seo.baslik LIKE :term2 OR 
                seo.aciklama LIKE :term3 OR 
                seo.kelime LIKE :term4 OR 
                urunozellikleri.urunstokkodu LIKE :term5
                )";

            $executeParams = [
                ":term" => "%".$term."%",
                ":term1" => "%".$term."%",
                ":term2" => "%".$term."%",
                ":term3" => "%".$term."%",
                ":term4" => "%".$term."%",
                ":term5" => "%".$term."%"
            ];

            unset($params['q']);
        }

        if(empty($params['languageID'])) {
            return [];
        }

        $languageID = $params['languageID'];
        $executeParams[":languageID"] = $languageID;
        unset($params['languageID']);

        if(isset($params["categoryID"])){
            if($params["categoryID"]>0){
                $where .= " AND kategori.kategoriid = :categoryID";
                $executeParams[":categoryID"] = $params["categoryID"];

            }
            unset($params["categoryID"]);
        }

        if(isset($params["page"])){
            $currentPage = $params["page"];
            unset($params["page"]);
        }

        if(isset($params["limit"])){
            $resultsPerPage = $params["limit"];
            unset($params["limit"]);
        }

        $i=0;
        foreach ($params as $key => $value) {
            $i++;
            $where .= " AND JSON_SEARCH(variantProperties, 'one', :key$i, NULL, '$[*].variantProperties[*].attribute.name') IS NOT NULL AND JSON_SEARCH(variantProperties, 'one', :value$i, NULL, '$[*].variantProperties[*].attribute.value') IS NOT NULL";
            $executeParams[":key$i"] = $key;
            $executeParams[":value$i"] = $value;
        }

        $startFrom = ($currentPage-1) * $resultsPerPage;

        $sql = "
            SELECT 
                urunozellikleri.sayfaid
            FROM 
                urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE 
                (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND ".$where."
            GROUP BY 
                urunozellikleri.sayfaid
            LIMIT 
                $startFrom,$resultsPerPage
        ";

        $data =  $this->db->select($sql,$executeParams);

        if(count($data)>0){

            $searchCountSql="
                SELECT 
                    COUNT(DISTINCT urunozellikleri.sayfaid) as total
                FROM urunozellikleri
                    INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                        INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                        INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                            INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
                WHERE (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND ".$where;

            $stmt = $this->db->select($searchCountSql,$executeParams);
            $total = $stmt[0]['total'];
            //
            $totalPages = ceil($total / $resultsPerPage);
            $searchResults['searchResultTotalPages'] = $totalPages;
            $searchResults['searchResultCurrentPage'] = $currentPage;
            $searchResults['searchTotalResults'] = $total;

            $searchResultIds = array_column($data, "sayfaid");
            $searchResults['searchResultProductIDs'] =$searchResultIds;

            //$this->json->createJson(["Search",$uniqSql],$searchResults);

            /*foreach ($searchResultIds as $searchResultId) {
                $searchResults['searchResultProducts'][] = $this->productModel->getProductByID($searchResultId);
            }*/

        }
        else{
            $searchResults['searchResultProductIDs'] = [];
            $searchResults['searchResultProducts'] = [];
            $searchResults['searchResultTotalPages'] = 0;
            $searchResults['searchResultCurrentPage'] = 0;
            $searchResults['searchTotalResults'] = 0;
        }
        return $searchResults;
    }

    public function searchProduct($params): array
    {

        $resultsPerPage = $this->resultsPerPage;
        $currentPage = $this->currentPage;


        if(empty($params['q'])) {
            return [];
        }
        else{
            $term = $params['q'];
            $where = "(
                sayfa.sayfaad LIKE :term OR 
                seo.baslik LIKE :term2 OR 
                seo.aciklama LIKE :term3 OR 
                seo.kelime LIKE :term4 OR 
                urunozellikleri.urunstokkodu LIKE :term5
                )";
            $executeParams = [
                ":term" => "%".$term."%",
                ":term2" => "%".$term."%",
                ":term3" => "%".$term."%",
                ":term4" => "%".$term."%",
                ":term5" => "%".$term."%"
            ];

            unset($params['q']);
        }

        if(empty($params['languageID'])) {
            return [];
        }

        $languageID = $params['languageID'];
        $executeParams[":languageID"] = $languageID;
        unset($params['languageID']);

        if(isset($params["categoryID"])){
            $where .= " AND kategori.kategoriid = :categoryID";
            $executeParams[":categoryID"] = $params["categoryID"];
            unset($params["categoryID"]);
        }

        if(isset($params["page"])){
            $currentPage = $params["page"];
            unset($params["page"]);
        }

        if(isset($params["limit"])){
            $resultsPerPage = $params["limit"];
            unset($params["limit"]);
        }

        $i=0;
        foreach ($params as $key => $value) {
            $i++;
            $where .= " AND JSON_SEARCH(variantProperties, 'one', :key$i, NULL, '$[*].variantProperties[*].attribute.name') IS NOT NULL AND JSON_SEARCH(variantProperties, 'one', :value$i, NULL, '$[*].variantProperties[*].attribute.value') IS NOT NULL";
            $executeParams[":key$i"] = $key;
            $executeParams[":value$i"] = $value;
        }


        $startFrom = ($currentPage-1) * $resultsPerPage;

        $sql = "
            SELECT 
                urunozellikleri.sayfaid
            FROM 
                urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE 
                (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) AND ".$where."
            GROUP BY 
                urunozellikleri.sayfaid
            LIMIT 
                $startFrom,$resultsPerPage
        ";

        $data =  $this->db->select($sql,$executeParams);

        if(count($data)>0){
            foreach ($data as $key => $value) {
                $data[$key] = $this->getProduct($value['sayfaid']);
            }
            return $data;
        }
        return [];
    }

    public function getProductByID($productID)
    {
        // Bir sorgudan dönen ürün id'ler için ürünleri kutucuklar halinde gösterebilmek için genel ürün bilgilerini alacağız.

        if (empty($productID)) {
            return [];
        }

        $sql = "
            SELECT 
                urunozellikleri.*, 
                sayfa.sayfaad, 
                seo.link,
                GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', ') as resim_url,
                urunmarka.markaad, 
                urunparabirim.parabirimad,
                urunparabirim.parabirimkod,
                urunparabirim.parabirimsimge,
                kategoriad,
                seoKategori.link as kategorilink,
                kategori.kategoriid,
                urunmiktarbirimadi
            FROM 
                urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                    LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                        LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                        LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
                            INNER JOIN seo AS seoKategori ON seoKategori.benzersizid=kategori.benzersizid
                INNER JOIN urunmarka ON urunmarka.markaid=urunozellikleri.markaid
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
                INNER JOIN urunmiktarbirim ON urunmiktarbirim.urunmiktarbirimid=urunozellikleri.urunmiktarbirimid
            WHERE 
                urunozellikleri.sayfaid = :urunId
            GROUP BY 
                urunozellikleri.urunozellikid
        ";

        return $this->db->select($sql, ['urunId' => $productID]);

    }

    public function getProductUnitName($unitID, $languageCode)
    {
        $sql = "
            SELECT 
                COALESCE(umc.urunmiktarbirimadi, u.urunmiktarbirimadi) AS urunmiktarbirimadi
            FROM 
                urunmiktarbirim u
                    LEFT JOIN urunmiktarbirimceviri umc ON u.urunmiktarbirimid = umc.urunmiktarbirimid AND umc.dilkodu  = '" . $languageCode . "'
            WHERE 
                u.urunmiktarbirimid = :unitID
        ";
        $data = $this->db->select($sql, ['unitID' => $unitID]);
        if (!empty($data)) {
            return $data[0]['urunmiktarbirimadi'];
        } else {
            return "";
        }
    }

    public function getCurrency($currencyID)
    {
        $sql = "
            SELECT 
                parabirimkod, parabirimsimge, parabirimad
            FROM 
                urunparabirim
            WHERE 
                parabirimid = :currencyID
        ";
        $data = $this->db->select($sql, ['currencyID' => $currencyID]);
        if (!empty($data)) {
            return $data[0];
        } else {
            return [];
        }
    }

    public function getCurrencies(): array
    {
        $sql = "
            SELECT 
                parabirimid, parabirimkod, parabirimsimge, parabirimad, parabirimkur
            FROM 
                urunparabirim
        ";
        $data = $this->db->select($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return [];
        }
    }

    public function getProductModels($searchText): array
    {
        $sql = "
            SELECT 
                urunmodel
            FROM 
                urunozellikleri
            WHERE 
                urunmodel LIKE :searchText
            GROUP BY 
                urunmodel
            LIMIT 10
        ";

        $data = $this->db->select($sql, ['searchText' => "%".$searchText."%"]);

        if (!empty($data)) {
            return $data;
        } else {
            return [];
        }
    }

    public function updateProduct($updateData):array
    {
        $productID = $updateData['productID'];

        $this->deleteProduct($productID);

        //ürün özelliklerini sildik, update yerine insert yapacağız
        $sql = "
            INSERT INTO 
                urunozellikleri
            SET 
                sayfaid = :productID,
                tedarikciid = :productSupplierID,
                markaid = :productBrandID,
                urunmodel = :productModel,
                urungrupid = :productGroupID,
                urunaciklama = :productDescription,
                urunaltbaslik = :productShortDesc,
                urunhediye = :productShortDesc1,
                urunparabirim = :productCurrency,
                uruneskifiyatgoster = :productShowOldPrice,
                uruntaksit = :productInstallment,
                urunkdv = :productTax,
                urunindirimorani = :productDiscountRate,
                urunsatisadet = :productSalesQuantity,
                urunmiktarbirimid = :productQuantityUnitID,
                urunminimummiktar = :productMinimumQuantity,
                urunmaksimummiktar = :productMaximumQuantity,
                urunkatsayi = :productCoefficient,
                uruntopluindirim = :productBulkDiscount,
                urunfiyatsor = :productPriceAsk,
                variantProperties = :variantProperties,
                product_properties = :productProperties,
                urunstokkodu = :productStockCode,
                urungtin = :productGTIN,
                urunmpn = :productMPN,
                urunbarkod = :productBarcode,
                urunoem = :productOEM,
                urunstok = :productStock,
                urunsatisfiyat = :productSalePrice,
                urunindirimsizfiyat = :productDiscountPrice,
                urunbayifiyat = :productDealerPrice,
                urunalisfiyat = :productPurchasePrice,
                urunkredikarti = :productCreditCard,
                urunhavaleodeme = :productBankTransfer,
                urundesi = :productDesi,
                urunkargosuresi = :productCargoTime,
                urunsabitkargoucreti = :productFixedCargoPrice,
                urunfiyatsontarih = :productPriceLastDate,
                urunanasayfa = :productHomePage,
                urungununfirsati = :productDayOpportunity,
                urunindirimde = :productDiscounted,
                urunyeni = :productNew,
                urunanindakargo = :productSameDayShipping,
                urunucretsizkargo = :productFreeShipping,
                urunonsiparis = :productPreOrder,
                urunkapidaodeme = :productCashOnDelivery
        ";

        $params = [
            'productSupplierID' => $updateData['productSupplierID'],
            'productBrandID' => $updateData['productBrandID'],
            'productModel' => $updateData['productModel'],
            'productGroupID' => $updateData['productGroupID'],
            'productDescription' => $updateData['productDescription'],
            'productShortDesc' => $updateData['productShortDesc'],
            'productShortDesc1' => $updateData['productShortDesc'],
            'productCurrency' => $updateData['productCurrency'],
            'productShowOldPrice' => $updateData['productShowOldPrice'],
            'productInstallment' => $updateData['productInstallment'],
            'productTax' => $updateData['productTax'],
            'productDiscountRate' => $updateData['productDiscountRate'],
            'productSalesQuantity' => $updateData['productSalesQuantity'],
            'productQuantityUnitID' => $updateData['productQuantityUnitID'],
            'productMinimumQuantity' => $updateData['productMinimumQuantity'],
            'productMaximumQuantity' => $updateData['productMaximumQuantity'],
            'productCoefficient' => $updateData['productCoefficient'],
            'productBulkDiscount' => $updateData['productBulkDiscount'],
            'productPriceAsk' => $updateData['productPriceAsk'],
            'variantProperties' => $updateData['variantProperties'],
            'productProperties' => $updateData['productProperties'],
            'productStockCode' => $updateData['productStockCode'],
            'productGTIN' => $updateData['productGTIN'],
            'productMPN' => $updateData['productMPN'],
            'productBarcode' => $updateData['productBarcode'],
            'productOEM' => $updateData['productOEM'],
            'productStock' => $updateData['productStock'],
            'productSalePrice' => $updateData['productSalePrice'],
            'productDiscountPrice' => $updateData['productDiscountPrice'],
            'productDealerPrice' => $updateData['productDealerPrice'],
            'productPurchasePrice' => $updateData['productPurchasePrice'],
            'productCreditCard' => $updateData['productCreditCard'],
            'productBankTransfer' => $updateData['productBankTransfer'],
            'productDesi' => $updateData['productDesi'],
            'productCargoTime' => $updateData['productCargoTime'],
            'productFixedCargoPrice' => $updateData['productFixedCargoPrice'],
            'productPriceLastDate' => $updateData['productPriceLastDate'],
            'productID' => $updateData['productID'],
            'productHomePage' => $updateData['productHomePage'],
            'productDayOpportunity' => $updateData['productDayOpportunity'],
            'productDiscounted' => $updateData['productDiscounted'],
            'productNew' => $updateData['productNew'],
            'productSameDayShipping' => $updateData['productSameDayShipping'],
            'productFreeShipping' => $updateData['productFreeShipping'],
            'productPreOrder' => $updateData['productPreOrder'],
            'productCashOnDelivery' => $updateData['productCashOnDelivery']
        ];

        $result = $this->db->insert($sql, $params);
        if ($result) {
            if($result>0){
                return [
                    'status' => 'success',
                    'message' => 'Ürün başarıyla güncellendi'
                ];
            }
            else{
                return [
                    'status' => 'success',
                    'message' => 'Ürün verileri zaten güncel'
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Ürün güncellenirken bir hata oluştu'
            ];
        }
    }

    public function updateProductByData($updateData)
    {
        $sql = "
            UPDATE 
                urunozellikleri 
            SET 
                markaid = :productBrandID,
                urunmodel = :productModel,
                urunaltbaslik = :productShortDesc,
                urunparabirim = :productCurrency,
                urunkdv = :productTax,
                variantProperties = :variantProperties,
                product_properties = :productProperties,
                urunstokkodu = :productStockCode,
                urungtin = :productGTIN,
                urunmpn = :productMPN,
                urunbarkod = :productBarcode,
                urunoem = :productOEM,
                urunstok = :productStock,
                urunsatisfiyat = :productSalePrice,
                urunindirimsizfiyat = :productDiscountPrice,
                urunkargosuresi = :productCargoTime
            WHERE 
                sayfaid = :productID
        ";
        return $this->db->update($sql, $updateData);
    }

    public function insertProduct($insertData):array{

        $sql = "
            INSERT INTO 
                urunozellikleri
            SET 
                sayfaid = :productID,
                tedarikciid = :productSupplierID,
                markaid = :productBrandID,
                urunmodel = :productModel,
                urungrupid = :productGroupID,
                urunaciklama = :productDescription,
                urunaltbaslik = :productShortDesc,
                urunhediye = :productShortDesc1,
                urunparabirim = :productCurrency,
                uruneskifiyatgoster = :productShowOldPrice,
                uruntaksit = :productInstallment,
                urunkdv = :productTax,
                urunindirimorani = :productDiscountRate,
                urunsatisadet = :productSalesQuantity,
                urunmiktarbirimid = :productQuantityUnitID,
                urunminimummiktar = :productMinimumQuantity,
                urunmaksimummiktar = :productMaximumQuantity,
                urunkatsayi = :productCoefficient,
                uruntopluindirim = :productBulkDiscount,
                urunfiyatsor = :productPriceAsk,
                variantProperties = :variantProperties,
                product_properties = :productProperties,
                urunstokkodu = :productStockCode,
                urungtin = :productGTIN,
                urunmpn = :productMPN,
                urunbarkod = :productBarcode,
                urunoem = :productOEM,
                urunstok = :productStock,
                urunsatisfiyat = :productSalePrice,
                urunindirimsizfiyat = :productDiscountPrice,
                urunbayifiyat = :productDealerPrice,
                urunalisfiyat = :productPurchasePrice,
                urunkredikarti = :productCreditCard,
                urunhavaleodeme = :productBankTransfer,
                urundesi = :productDesi,
                urunkargosuresi = :productCargoTime,
                urunsabitkargoucreti = :productFixedCargoPrice,
                urunfiyatsontarih = :productPriceLastDate,
                urunanasayfa = :productHomePage,
                urungununfirsati = :productDayOpportunity,
                urunindirimde = :productDiscounted,
                urunyeni = :productNew,
                urunanindakargo = :productSameDayShipping,
                urunucretsizkargo = :productFreeShipping,
                urunonsiparis = :productPreOrder,
                urunkapidaodeme = :productCashOnDelivery
        ";

        $params = [
            'productID' => $insertData['productID'],
            'productSupplierID' => $insertData['productSupplierID'],
            'productBrandID' => $insertData['productBrandID'],
            'productModel' => $insertData['productModel'],
            'productGroupID' => $insertData['productGroupID'],
            'productDescription' => $insertData['productDescription'],
            'productShortDesc' => $insertData['productShortDesc'],
            'productShortDesc1' => $insertData['productShortDesc'],
            'productCurrency' => $insertData['productCurrency'],
            'productShowOldPrice' => $insertData['productShowOldPrice'],
            'productInstallment' => $insertData['productInstallment'],
            'productTax' => $insertData['productTax'],
            'productDiscountRate' => $insertData['productDiscountRate'],
            'productSalesQuantity' => $insertData['productSalesQuantity'],
            'productQuantityUnitID' => $insertData['productQuantityUnitID'],
            'productMinimumQuantity' => $insertData['productMinimumQuantity'],
            'productMaximumQuantity' => $insertData['productMaximumQuantity'],
            'productCoefficient' => $insertData['productCoefficient'],
            'productBulkDiscount' => $insertData['productBulkDiscount'],
            'productPriceAsk' => $insertData['productPriceAsk'],
            'variantProperties' => $insertData['variantProperties'],
            'productProperties' => $insertData['productProperties'],
            'productStockCode' => $insertData['productStockCode'],
            'productGTIN' => $insertData['productGTIN'],
            'productMPN' => $insertData['productMPN'],
            'productBarcode' => $insertData['productBarcode'],
            'productOEM' => $insertData['productOEM'],
            'productStock' => $insertData['productStock'],
            'productSalePrice' => $insertData['productSalePrice'],
            'productDiscountPrice' => $insertData['productDiscountPrice'],
            'productDealerPrice' => $insertData['productDealerPrice'],
            'productPurchasePrice' => $insertData['productPurchasePrice'],
            'productCreditCard' => $insertData['productCreditCard'],
            'productBankTransfer' => $insertData['productBankTransfer'],
            'productDesi' => $insertData['productDesi'],
            'productCargoTime' => $insertData['productCargoTime'],
            'productFixedCargoPrice' => $insertData['productFixedCargoPrice'],
            'productPriceLastDate' => $insertData['productPriceLastDate'],
            'productHomePage' => $insertData['productHomePage'],
            'productDayOpportunity' => $insertData['productDayOpportunity'],
            'productDiscounted' => $insertData['productDiscounted'],
            'productNew' => $insertData['productNew'],
            'productSameDayShipping' => $insertData['productSameDayShipping'],
            'productFreeShipping' => $insertData['productFreeShipping'],
            'productPreOrder' => $insertData['productPreOrder'],
            'productCashOnDelivery' => $insertData['productCashOnDelivery']
        ];

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Ürün başarıyla eklendi'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Ürün eklenirken bir hata oluştu'
            ];
        }
    }
        //silme yapalım
    public function deleteProduct($productID)
    {
        $sql = "
            DELETE FROM 
                urunozellikleri
            WHERE 
                sayfaid = :productID
        ";
        return $this->db->delete($sql, ['productID' => $productID]);
    }

    public function formatProductProperties($productPropertiesJson)
    {
        // JSON verisini diziye çevir
        $propertiesArray = json_decode($productPropertiesJson, true);

        // JSON verisi geçerli değilse ya da boşsa boş string döndür
        if (json_last_error() !== JSON_ERROR_NONE || empty($propertiesArray)) {
            return '';
        }

        $formattedProperties = [];

        // Her bir özelliği [Özellik Adı: Özellik Değeri] formatında ekle
        foreach ($propertiesArray as $property) {
            if (isset($property['attribute']['name']) && isset($property['attribute']['value'])) {
                $name = trim($property['attribute']['name']);
                $value = trim($property['attribute']['value']);
                $formattedProperties[] = "[$name:$value]";
            }
        }

        // Özellikleri birleştir ve döndür
        return implode(',', $formattedProperties);
    }

    public function formatVariantProperties($propertiesArray)
    {

        // JSON verisi geçerli değilse ya da boşsa boş string döndür
        if (empty($propertiesArray)) {
            return '';
        }

        $formattedProperties = [];

        // Her bir özelliği [Özellik Adı: Özellik Değeri] formatında ekle
        foreach ($propertiesArray as $property) {
            if (isset($property['attribute']['name']) && isset($property['attribute']['value'])) {
                $name = trim($property['attribute']['name']);
                $value = trim($property['attribute']['value']);
                $formattedProperties[] = "[$name:$value]";
            }
        }

        // Özellikleri birleştir ve döndür
        return implode(',', $formattedProperties);
    }

    public function getProductIdByStockCode($productStockCode){
        $sql = "
            SELECT 
                sayfaid
            FROM 
                urunozellikleri
            WHERE 
                urunozellikleri.urunstokkodu = :productStockCode or JSON_UNQUOTE(JSON_EXTRACT(variantProperties, '$.variantStockCode')) = :productStockCode1

        ";
        $params = array(
            'productStockCode' => $productStockCode,
            'productStockCode1' => $productStockCode
        );
        $result = $this->db->select($sql, $params);

        if (!empty($result)) {
            return $result[0]['sayfaid'];
        }
        return 0;
    }

    public function getProductGallery($pageID){
        $sql = "
            SELECT 
                resimgaleriid as galleryID
            FROM 
                sayfalistegaleri 
            WHERE sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function getProductVideos($pageID){
        $sql = "
            SELECT 
                videoid as videoID
            FROM 
                sayfalistevideo 
            WHERE sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    //transaction işlemleri
    public function beginTransaction($funcName = "")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName = "")
    {
        $this->db->commit($funcName);
    }

    public function rollback($funcName = "")
    {
        $this->db->rollBack($funcName);
    }

}


