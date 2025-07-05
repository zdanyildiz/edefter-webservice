<?php
class Product
{
    private Database$db;
    private JSON $json;
    public string $productSql;

    public function __construct($db, $json)
    {
        $this->db = $db;
        $this->json = $json;

        $this->productSql = "
            SELECT 
                urunozellikleri.sayfaid
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID
        ";
    }

    public function getProductDetails($productID, $languageCode)
    {
        // ürün detayları, ürün sayfasında alınmaktadır. Her ürün bir sayfayla ilişkili olduğu için Product modelinden önce çalıştırılan Page Modelinde sayfa içerikleri, seo bilgileri, sayfa resimleri ve kategori bilgileri zaten alınmıştır. Sadece ürünle alakalı bilgileri alacağız.

        if (empty($productID) || empty($languageCode)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Product/ProductDetails", $productID]);
        if ($jsonData !== null) {
            return $jsonData;
        }

        $sql = "
            SELECT 
                urunozellikleri.*, urunmarka.markaad, 
                urunparabirim.parabirimid, urunparabirim.parabirimkod, urunparabirim.parabirimsimge, 
                COALESCE(umc.urunmiktarbirimadi, u.urunmiktarbirimadi) AS urunmiktarbirimadi,
                uye.uyefaturaad
            FROM urunozellikleri 
                LEFT JOIN urunmarka ON urunmarka.markaid = urunozellikleri.markaid 
                LEFT JOIN uye ON uye.uyeid=urunozellikleri.tedarikciid 
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
                INNER JOIN urunmiktarbirim u ON urunozellikleri.urunmiktarbirimid = u.urunmiktarbirimid
                    LEFT JOIN urunmiktarbirimceviri umc ON u.urunmiktarbirimid = umc.urunmiktarbirimid AND umc.dilkodu  = '" . $languageCode . "'
            WHERE 
                urunozellikleri.sayfaid = :productID
        ";
        $data = $this->db->select($sql, ['productID' => $productID]);

        if (count($data) > 0) {

            $this->json->createJson(["Product/ProductDetails", $productID], $data);
        }
        else {
            Log::write("Ürün detayları bulunamadı - $productID", "info");
        }
        return $data;
    }

    public function getProductByID($productID)
    {
        // Bir sorgudan dönen ürün id'ler için ürünleri kutucuklar halinde gösterebilmek için genel ürün bilgilerini alacağız.

        if (empty($productID)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Product/ProductByID", $productID]);
        if ($jsonData !== null && $jsonData!=false) {
            return $jsonData;
        }

        $sql = "
            SELECT 
                dil.dilid,dilkisa,
                kategori.kategoriid,
                kategoriad,seoKategori.link as kategorilink,
                sayfa.sayfaad, 
                seo.link,
                GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', ') as resim_url,
                urunmarka.markaad, 
                urunparabirim.parabirimad,urunparabirim.parabirimkod,urunparabirim.parabirimsimge,
                urunozellikleri.*
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                    LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                        LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                        LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
                            INNER JOIN seo AS seoKategori ON seoKategori.benzersizid=kategori.benzersizid
                                INNER JOIN dil ON dil.dilid=kategori.dilid
                LEFT JOIN urunmarka ON urunmarka.markaid=urunozellikleri.markaid
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
            WHERE urunozellikleri.sayfaid = :urunId
            GROUP BY urunozellikleri.urunozellikid
        ";

        $data = $this->db->select($sql, ['urunId' => $productID]);

        if ($data !== null) {
            $this->json->createJson(["Product/ProductByID", $productID], $data);
        }
        else {
            Log::write("Ürün bilgisi bulunamadı - $productID", "info");
        }

        return $data;

    }

    public function getProductByUniqID($productUniqID)
    {

        if (empty($productUniqID)) {
            return [];
        }

        $sql = "
            SELECT 
                sayfa.sayfaid
            FROM 
                sayfa
            WHERE 
                sayfa.benzersizid = :productUniqID and sayfasil=0 and sayfaaktif=1
        ";
        $data = $this->db->select($sql, ['productUniqID' => $productUniqID]);

        if ($data !== null) {
            $data = $this->getProductByID($data[0]["sayfaid"]);
        }
        return $data;
    }

    public function getRelatedProducts($productID, $languageCode)
    {

        if (empty($productID) || empty($languageCode)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Product/RelatedProducts", $productID]);
        if ($jsonData !== null) {
            return $jsonData;
        }

        $sqlUrunid = "
            SELECT 
                urunid
            FROM 
                uruniliski
            WHERE 
                (urunid = :pageId and iliskiid != :pageId2 and iliskiid!=0) or (urunid != :pageId3 and iliskiid = :pageId4)
            GROUP BY urunid;
        ";
        $urunIds = $this->db->select($sqlUrunid, ['pageId' => $productID, 'pageId2' => $productID, 'pageId3' => $productID, 'pageId4' => $productID]);

        // Select product details for the urunid values
        $relatedProducts = [];
        foreach ($urunIds as $urunId) {
            if ($urunId["urunid"] == $productID) {
                continue;
            }
            $relatedProduct = $this->getProductByID($urunId["urunid"], $languageCode);
            if ($relatedProduct) {

                $relatedProducts[] = $relatedProduct;
            }
        }

        if ($relatedProducts !== null) {
            $this->json->createJson(["Product/RelatedProducts", $productID], $relatedProducts);
        }
        else {
            //Log::write("İlgili ürünler bulunamadı - $pageId","info");
        }
        return $relatedProducts;
    }

    //ANASAYFA ÜRÜNLERİ
    public function getSpecialOffers($languageID)
    {
        if (empty($languageID)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Homepage/SpecialOffers", $languageID]);
        if ($jsonData !== null) {
            return $jsonData;
        }

        $sql = $this->productSql;
        $sql .= "
                AND urunozellikleri.urungununfirsati = 1
            GROUP BY urunozellikleri.sayfaid
            LIMIT 12";
        $data = $this->db->select($sql, ['languageID' => $languageID]);
        if (!empty($data)) {
            $this->json->createJson(["Homepage/SpecialOffers", $languageID], $data);
        }
        else {
            //Log::write("Anasayfa Günün fırsatı ürünleri boş - dilid:$languageID", "info");
        }
        return $data;
    }

    public function getHomepageProducts($languageID)
    {
        if (empty($languageID)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Homepage/HomepageProducts", $languageID]);

        $sql = $this->productSql;
        $sql .= "
                AND urunozellikleri.urunanasayfa = 1
            GROUP BY urunozellikleri.sayfaid
            LIMIT 12";
        $data = $this->db->select($sql, ['languageID' => $languageID]);
        if (!empty($data)) {
            $this->json->createJson(["Homepage/HomepageProducts", $languageID], $data);
        }
        else {
            //Log::write("Anasayfa ürünleri boş - dilid:$languageID", "info");
        }
        return $data;
    }

    public function getDiscountedProducts($languageID)
    {
        if (empty($languageID)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Homepage/DiscountedProducts", $languageID]);
        if ($jsonData !== null) {
            return $jsonData;
        }

        $sql = $this->productSql;
        $sql .= "
                AND urunozellikleri.urunindirimde = 1
            GROUP BY urunozellikleri.sayfaid
            LIMIT 12";

        $data = $this->db->select($sql, ['languageID' => $languageID]);
        if (!empty($data)) {
            $this->json->createJson(["Homepage/DiscountedProducts", $languageID], $data);
        }
        else {
            // data null ise hata veriyoruz
            //Log::write("Anasayfa indirimde ürünler boş - dilid:$languageID", "info");
        }
        return $data;
    }

    public function getNewProducts($languageID)
    {
        if (empty($languageID)) {
            return [];
        }

        $jsonData = $this->json->readJson(["Homepage/NewProducts", $languageID]);
        if ($jsonData !== null) {
            return $jsonData;
        }

        $sql = $this->productSql;
        $sql .= "
                AND urunozellikleri.urunyeni = 1
            GROUP BY urunozellikleri.sayfaid
            LIMIT 12";

        $data = $this->db->select($sql, ['languageID' => $languageID]);
        if (!empty($data)) {
            $this->json->createJson(["Homepage/NewProducts", $languageID], $data);
        }
        else {
            //Log::write("Anasayfa yeni ürünler boş - dilid:$languageID", "info");
        }
        return $data;
    }

    // urunozellikleri tablosundaki özelliklere göre variantProperties sütununu güncelleyelim
    public function setProductVariants($productID, $languageCode = "")
    {

        // ürün özellikleri sayfaid'ye (ürün id) göre gruplandırılır.
        // bir ürünün birden fazla özelliği olabilir. Örneğin bir ürünün farklı renk, beden, malzeme gibi özellikleri olabilir.
        // urunozellikleri tablosunda şu şekilde tutulur sayfaid,urunparabirim
        // güncelleme yapmadan önce bir ürün grubunun (sayfaid'si aynı olanlar) özelliklerini alıp varyanlarını oluşturalım
        // daha sonra tüm varyantları json formatında birleştirip sayfaid'ye göre urunozellikleri tablosundaki variantProperties sütununa yazalım
        // bu sayede ürün sayfasında ürünün varyantlarını gösterebiliriz.

        if (empty($languageCode)) {
            // sayfanın dil kodunu dil tablosundan sayfaid->sayfalistekategori->kategoriid->dilid->dil.dilkisa olarak alalım
            $sql = "
                SELECT 
                    dil.dilkisa
                FROM 
                    sayfa
                        INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                            INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid
                                INNER JOIN dil ON dil.dilid=kategori.dilid
                WHERE 
                    sayfa.sayfaid = :pageId
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['pageId' => $productID]);
            $languageCode = $stmt->fetch(\PDO::FETCH_COLUMN);
            if (empty($languageCode)) {
                return [];
            }
            $languageCode = strtolower($languageCode);
        }
        $sql = "
            SELECT 
                sayfaid,urunozellikid,urunstok,urunstokkodu,urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,
                COALESCE(umc.urunmiktarbirimadi, u.urunmiktarbirimadi) AS urunmiktarbirimadi,
                urunaltbaslik,urunkatsayi,urunminimummiktar,parabirimkod,parabirimid,parabirimsimge
            FROM 
                urunozellikleri
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
                INNER JOIN urunmiktarbirim u ON urunozellikleri.urunmiktarbirimid = u.urunmiktarbirimid
                    LEFT JOIN urunmiktarbirimceviri umc ON u.urunmiktarbirimid = umc.urunmiktarbirimid AND umc.dilkodu  = '" . $languageCode . "'
            WHERE
                sayfaid = :pageId
            ORDER BY urunozellikid ASC    
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pageId' => $productID]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($data) > 0) {
            //print_r($data);exit();
            $variantProperties = [];
            //örnek varyant:
            //[ { "variantID:1","variantName": "M16 X 100 - PASLANMAZ - 60 TABAN", "variantCurrencyCode": "TRY", "variantCurrencySymbol": "₺", "variantCurrencyID": 3, "variantSellingPrice": "289.33", "variantPriceWithoutDiscount": "361.76", "variantSellerPrice": "0.00", "variantDiscountRate": 20, "variantQuantity": 999, "variantStockCode": "505.601.610", "variantMinQuantity": "1.0000", "variantCoefficient": "1.0000", "variantProperties": [ { "attribute": { "name": "Malzeme", "value": "Güçlendirilmiş Polyamid (PA) + Paslanmaz (SS)" } }, { "attribute": { "name": "Renk", "value": "Siyah+Metal" } }, { "attribute": { "name": "Ölçü", "value": "M16X100X60 TABAN" } } ] },{...

            foreach ($data as $i => $variantProperty) {
                $variantAttributes = array();
                $variantProperties[$i]['variantID'] = $variantProperty['urunozellikid'];
                $variantProperties[$i]['variantName'] = $variantProperty['urunaltbaslik'];
                $variantProperties[$i]['variantCurrencyID'] = $variantProperty['parabirimid'];
                $variantProperties[$i]['variantCurrencyCode'] = $variantProperty['parabirimkod'];
                $variantProperties[$i]['variantCurrencySymbol'] = $variantProperty['parabirimsimge'];
                $variantProperties[$i]['variantSellingPrice'] = $variantProperty['urunsatisfiyat'];
                $variantProperties[$i]['variantPriceWithoutDiscount'] = $variantProperty['urunindirimsizfiyat'];
                $variantProperties[$i]['variantSellerPrice'] = $variantProperty['urunbayifiyat'];
                $variantProperties[$i]['variantDiscountRate'] = ($variantProperty['urunsatisfiyat'] != "0.00" && $variantProperty['urunindirimsizfiyat'] != "0.00") ? round(100 - (($variantProperty['urunsatisfiyat'] / $variantProperty['urunindirimsizfiyat']) * 100)) : 0;
                $variantProperties[$i]['variantQuantity'] = $variantProperty['urunstok'];
                $variantProperties[$i]['variantStockCode'] = $variantProperty['urunstokkodu'];
                $variantProperties[$i]['variantMinQuantity'] = $variantProperty['urunminimummiktar'];
                $variantProperties[$i]['variantCoefficient'] = $variantProperty['urunkatsayi'];


                $variantProperties[$i]['variantProperties'] = $variantAttributes;
            }
            $variantProperties = json_encode($variantProperties);
            //print_r($variantProperties);exit();
            $this->db->beginTransaction();
            $sql = "
                UPDATE 
                    urunozellikleri 
                SET 
                    variantProperties = :variantProperties
                WHERE 
                    sayfaid = :pageId
            ";
            $stmt = $this->db->prepare($sql);
            // hata kontrolü yapalım, hata yoksa commit edelim
            if ($stmt->execute(['variantProperties' => $variantProperties, 'pageId' => $productID])) {
                $this->json->deleteJson(["Product/ProductDetails", $productID]);
                $this->db->commit();
            } else {
                $this->db->rollBack();
                Log::write("Ürün varyantları güncellenemedi - $productID", "error");
            }

        } else {
            Log::write("Ürün özellikleri bulunamadı", "info");
        }
    }

    public function setAllProductVariants()
    {

        // ürün özellikleri sayfaid'ye (ürün id) göre gruplandırılır.
        // bir ürünün birden fazla özelliği olabilir. Örneğin bir ürünün farklı renk, beden, malzeme gibi özellikleri olabilir.
        // urunozellikleri tablosunda şu şekilde tutulur sayfaid,urunparabirim
        // güncelleme yapmadan önce bir ürün grubunun (sayfaid'si aynı olanlar) özelliklerini alıp varyanlarını oluşturalım
        // daha sonra tüm varyantları json formatında birleştirip sayfaid'ye göre urunozellikleri tablosundaki variantProperties sütununa yazalım
        // bu sayede ürün sayfasında ürünün varyantlarını gösterebiliriz.

        // sayfanın dil kodunu dil tablosundan sayfaid->sayfalistekategori->kategoriid->dilid->dil.dilkisa olarak alalım
        $sql = "
            SELECT 
                dil.dilkisa, sayfa.sayfaid
            FROM 
                sayfa
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid
                            INNER JOIN dil ON dil.dilid=kategori.dilid
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($data) > 0) {
            foreach ($data as $i => $product) {
                $languageCode = strtolower($product['dilkisa']);
                $this->setProductVariants($product['sayfaid'], $languageCode);

            }
        } else {
            Log::write("Ürün özellikleri bulunamadı", "info");
        }
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

    public function getProductUnitNameByProductID($productID){
        $sql = "
            SELECT 
                COALESCE(umc.urunmiktarbirimadi, u.urunmiktarbirimadi) AS urunmiktarbirimadi
            FROM
                urunozellikleri
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=urunozellikleri.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid
                            INNER JOIN dil ON dil.dilid=kategori.dilid
                INNER JOIN urunmiktarbirim u ON urunozellikleri.urunmiktarbirimid = u.urunmiktarbirimid
                    LEFT JOIN urunmiktarbirimceviri umc ON u.urunmiktarbirimid = umc.urunmiktarbirimid AND umc.dilkodu  = dil.dilkisa
            WHERE
                urunozellikleri.sayfaid = :productID
        ";

        $data = $this->db->select($sql, ['productID' => $productID]);
        if (!empty($data)) {
            return $data[0]['urunmiktarbirimadi'];
        } else {
            return "";
        }
    }

    public function getCurrencies($currencyID)
    {
        $sql = "
            SELECT 
                parabirimkod, parabirimsimge
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

    public function getProductImages($productID)
    {
        // resimklasorad ve resimi concat yaparak resim_url oluşturulur
        $sql = "
            SELECT 
                GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', ') as resim_url
            FROM 
                sayfalisteresim
                    LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                    LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
            WHERE
                sayfalisteresim.sayfaid = :pageId
        ";
        $data = $this->db->select($sql, ['pageId' => $productID]);
        if (!empty($data)) {
            return $data[0];
        } else {
            return [];
        }
    }

    public function getProductList($params)
    {
        $resultsPerPage = $params["limit"];
        $currentPage = $params["page"];

        $startFrom = ($currentPage - 1) * $resultsPerPage;

        $totalProducts = $this->getTotalProducts($params["languageID"]);

        $totalPages = ceil($totalProducts / $resultsPerPage);

        $sql = "
            SELECT 
                urunozellikleri.sayfaid
            FROM urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE (sayfasil=0 and sayfaaktif=1 and kategori.dilid = :languageID) 
            GROUP BY urunozellikleri.sayfaid
            LIMIT $startFrom,$resultsPerPage
        ";

        $result = $this->db->select($sql, ['languageID' => $params["languageID"]]);

        if (!empty($result)) {

            foreach ($result as $key => $value) {
                $result[$key] = $this->getProductByID($value["sayfaid"]);
            }

            return [
                "status" => "success",
                "message" => "Products found.",
                "totalProducts" => $totalProducts,
                "totalPages" => $totalPages,
                "products" => $result
            ];
        } else {
            return [
                "status" => "error",
                "message" => "No products found.",
                "totalProducts" => 0,
                "totalPages" => 0,
                "products" => []
            ];
        }
    }

    public function getTotalProducts($languageID)
    {
        $sql = "
            SELECT 
                COUNT(sayfa.sayfaid) as totalProducts
            FROM sayfa
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid 
            WHERE (sayfasil=0 and sayfaaktif=1 and sayfa.sayfatip=7 and kategori.dilid = :languageID) 
        ";

        $result = $this->db->select($sql, ['languageID' => $languageID]);

        if (!empty($result)) {
            return $result[0]["totalProducts"];
        } else {
            return 0;
        }
    }

    public function getAllProductsForMerchantCenter($languageID)
    {
        $sql = "
            SELECT
                sayfa.sayfaad as productName,sayfa.sayfaid as productID,
                urunozellikleri.urunsatisfiyat as productPrice,urunozellikleri.urunstokkodu as productStockCode,
                seo.link as productLink, seo.aciklama as productDescription,urunozellikleri.variantProperties as productVariants,
                urunmarka.markaad as productBrand,
                dil.dilkisa as languageCode, 
                GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ',') as productImages,
                kategori.kategoriad as productCategory,merchant_category_name,
                urunparabirim.parabirimkod as currencyCode, urunparabirim.parabirimsimge as currencySymbol
            FROM
                urunozellikleri
                INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid
                    INNER JOIN seo ON sayfa.benzersizid=seo.benzersizid
                INNER JOIN urunmarka ON urunmarka.markaid=urunozellikleri.markaid
                    INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
                        INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid
                        INNER JOIN dil ON dil.dilid=kategori.dilid
                    INNER JOIN sayfalisteresim ON sayfalisteresim.sayfaid = sayfa.sayfaid
                        INNER JOIN resim ON sayfalisteresim.resimid = resim.resimid
                        INNER JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                INNER JOIN urunparabirim ON urunparabirim.parabirimid=urunozellikleri.urunparabirim
                LEFT JOIN google_merchant_categories ON google_merchant_categories.local_category_id=kategori.kategoriid
            WHERE 
                sayfaaktif=1 and sayfasil=0 and sayfa.sayfatip=7 and kategori.dilid=:languageID
            GROUP BY 
                urunozellikleri.urunstokkodu
            Order By 
                sayfalisteresim.sayfalisteresimid
        ";

        $result = $this->db->select($sql, ['languageID' => $languageID]);

        if (!empty($result)) {
            return $result;
        } else {
            return [];
        }
    }

    public function getProductVariants($variantStockCode)
    {
        $sql = '
            SELECT 
                *
            FROM 
                variant_properties
            WHERE
                variant_stock_code = :variantStockCode
        ';

        $params = ['variantStockCode' => $variantStockCode];

        return $this->db->select($sql, $params);
    }

    public function getProductNameByProductID($productID)
    {
        $sql = "
            SELECT
                sayfaad
            FROM
                sayfa
            WHERE
                sayfaid = :productID
        ";
        return $this->db->select($sql, ['productID' => $productID]);
    }
}
