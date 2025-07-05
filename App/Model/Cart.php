<?php

/**
 * @var Log $log
 */

class Cart
{
    private Database $db;
    private Helper $helper;
    private Session $session;
    private Config $config;
    private array $visitor;
    private Casper $casper;
    private Json $json;
    private array $routerResult;

    public function __construct(Database $db,Helper $helper,Session $session,Config $config)
    {
        $this->db = $db;
        $this->helper = $helper;
        $this->session = $session;
        $this->config = $config;

        $this->casper = $this->session->getCasper();
        $this->visitor = $this->casper->getVisitor();
        $this->json = $this->casper->getConfig()->Json;
        $this->routerResult = $this->session->getSession("routerResult");
    }

    public function addCart($product_id, $stock_code, $price, $quantity):array
    {
        // ürün kontrolü
        $productCheck = $this->checkProduct($product_id,$stock_code);

        if ($productCheck['status'] == 'error') {
            $resultStatus = "error";
            return [
                "message" => "Ürün bulunamadı. ($product_id)",
                "status" => $resultStatus
            ];
        }

        //ürün var
        $product = $productCheck["data"];

        // bu ürün daha önceden sepete atılmış mı
        $visitorUniqID = $this->visitor['visitorUniqID'];
        $isProductInCart = $this->isProductInCart($visitorUniqID,$stock_code);

        // false dönmemişse sepet ve adet güncellenecek, cevap dönecek
        if(!empty($isProductInCart)){
            $cartUniqID = $isProductInCart["cartUniqID"];
            $quantity += $isProductInCart["quantity"];
            return $this->updateCart($cartUniqID, $quantity);
        }

        //parabirimi düzenleme
        $fixPrice = $this->fixPrice($product);
        $productSalesPrice = $fixPrice['price'];
        $productCurrencyID = $fixPrice['currencyID'];

        $resultMessage = "";

        //die("$productSalesPrice != $price");
        // ürün fiyatı değişmişse sepete eklenen ürünün fiyatını güncelleyelim ve kullanıcıya mesaj dönelim
        if ($productSalesPrice != $price) {
            // return "Ürün fiyatı değişmiş.";
            // ürün fiyatı değişmişse sepete eklenen ürünün fiyatını güncelleyelim ve kullanıcıya mesaj dönelim
            $price = $productSalesPrice;
            $resultMessage .= "Ürün fiyatı değişmiş. Sepete eklenen ürünün fiyatı güncellendi.<br>";
        }

        // stok kontrolü yap gerekirse quantiti adetini değiştir
        if ($product['urunstok'] < $quantity) {
            // return "Yeterli stok yok.";
            // yeterli stok yoksa stok adeti kadar sepete ekleyelim ve kullanıcıya mesaj dönelim
            $quantity = $product['urunstok'];
            $resultMessage .= "Yeterli stok yok. Sepete eklenen ürünün adedi güncellendi.";
        }

        if($product['urunstok'] == 0){
            $resultStatus = "error";
            $resultMessage .= "Stokta ürün bulunmamaktadır.";
            return [
                "status" => $resultStatus,
                "message" => $resultMessage
            ];
        }

        $productVariants = json_decode($product['variantProperties'], true);

        if (!empty($productVariants)) {
            $productVariant = "";
            foreach ($productVariants as $variant) {
                if ($variant['variantStockCode'] == $stock_code) {
                    //$variantCurrencyID = $variant['variantCurrencyID'];
                    $productVariant = $variant['variantProperties'];
                    break;
                }
            }
            $productVariants = json_encode($productVariant);
        }
        else {
            $productVariants = "";
        }

        $productTax = $product['urunkdv'];

        // sepete eklenen ürün herhangi bir kampanyaya dahil mi kontrol edelim
        $campaignControl = $this->checkCampaign(
            $product_id,$product['kategoriid'],$product['markaid'],$product['tedarikciid']);

        $discountedAmount = 0;
        $discountedExplanation = "";
        if(!empty($campaignControl)){
            $discount = $this->calculateDiscount($campaignControl, $quantity, $price);
            $discountedAmount = $discount['discountedAmount'];
            $discountedExplanation = $discount['discountedExplanation'];
            $resultMessage .= $discountedExplanation ."<br>";
        }

        $insertData = [
            'uyebenzersiz' => $visitorUniqID,
            'sepetbenzersiz' => $this->helper->createPassword(20, 2),
            "sepetolusturtarih" => date("Y-m-d H:i:s"),
            "sepetguncelletarih" => date("Y-m-d H:i:s"),
            "urunstokkodu" => $stock_code,
            "urunid" => $product_id,
            "urunvaryant" => $productVariants,
            "urundesi" => "1",
            "urunadet" => $quantity,
            "urunparabirim" => $productCurrencyID,
            "urunfiyat" => $price,
            "urunkdv" => $productTax,
            "urunkargoucreti" => 0,
            "uruniadeadet" => 0,
            "indirimmiktari" => $discountedAmount,
            "indirimaciklamasi" => $discountedExplanation,
            "sepetdurum" => 0,
            "odemedurum" => 0,
            "siparisbenzersiz" => "",
            "sepetsil" => 0
        ];

        $insertSql = "
            INSERT INTO 
                uyesepet 
                (uyebenzersiz, sepetbenzersiz, sepetolusturtarih, sepetguncelletarih, urunstokkodu, urunid, urunvaryant, urundesi, urunadet, urunparabirim, urunfiyat, urunkdv, urunkargoucreti, uruniadeadet, indirimmiktari, indirimaciklamasi, sepetdurum, odemedurum, siparisbenzersiz, sepetsil) 
            VALUES 
                (:uyebenzersiz, :sepetbenzersiz, :sepetolusturtarih, :sepetguncelletarih, :urunstokkodu, :urunid, :urunvaryant, :urundesi, :urunadet, :urunparabirim, :urunfiyat, :urunkdv, :urunkargoucreti, :uruniadeadet, :indirimmiktari, :indirimaciklamasi, :sepetdurum, :odemedurum, :siparisbenzersiz, :sepetsil)
        ";

        $insertParams = [
            'uyebenzersiz' => $insertData['uyebenzersiz'],
            'sepetbenzersiz' => $insertData['sepetbenzersiz'],
            'sepetolusturtarih' => $insertData['sepetolusturtarih'],
            'sepetguncelletarih' => $insertData['sepetguncelletarih'],
            'urunstokkodu' => $insertData['urunstokkodu'],
            'urunid' => $insertData['urunid'],
            'urunvaryant' => $insertData['urunvaryant'],
            'urundesi' => $insertData['urundesi'],
            'urunadet' => $insertData['urunadet'],
            'urunparabirim' => $insertData['urunparabirim'],
            'urunfiyat' => $insertData['urunfiyat'],
            'urunkdv' => $insertData['urunkdv'],
            'urunkargoucreti' => $insertData['urunkargoucreti'],
            'uruniadeadet' => $insertData['uruniadeadet'],
            'indirimmiktari' => $insertData['indirimmiktari'],
            'indirimaciklamasi' => $insertData['indirimaciklamasi'],
            'sepetdurum' => $insertData['sepetdurum'],
            'odemedurum' => $insertData['odemedurum'],
            'siparisbenzersiz' => $insertData['siparisbenzersiz'],
            'sepetsil' => $insertData['sepetsil']
        ];


        $this->db->beginTransaction();
        if ($this->db->insert($insertSql, $insertParams)) {
            $this->db->commit();
            $resultStatus = "success";
            $resultMessage .= "Ürün sepete eklendi.";
        }
        else {
            $this->db->rollBack();
            $resultStatus = "error";
            $resultMessage .= "Ürün sepete eklenemedi.";
        }

        $visitor = $this->visitor;
        $visitor['visitorCart'] = $this->getCart($visitor['visitorUniqID']);
        $this->casper->setVisitor($visitor);
        $this->session->updateSession("casper",$this->casper);

        return [
            "status" => $resultStatus,
            "message" => $resultMessage,
            'cartData'=>$insertParams
        ];
    }

    public function updateCart($cart_id, $quantity):array
    {
        // Ürünün mevcut fiyatını ve stok durumunu kontrol et
        $sql = "
            SELECT 
                urunid,urunfiyat,indirimmiktari,indirimaciklamasi,urunparabirim,parabirimsimge,urunstokkodu
            FROM 
                uyesepet 
                INNER JOIN urunparabirim ON uyesepet.urunparabirim = urunparabirim.parabirimid
            WHERE sepetbenzersiz = :cart_id
        ";
        $cart = $this->db->select($sql, ['cart_id' => $cart_id]);

        if (!$cart) {
            return [
                "status" => "error",
                "message" => "Sepet bulunamadı.",
                'cartData'=>[]
            ];
        }
        $cart = $cart[0];

        $productID = $cart['urunid'];
        $productStockCode = $cart['urunstokkodu'];
        $price = $cart['urunfiyat'];

        // ürün kontrolü
        $productCheck = $this->checkProduct($productID,$productStockCode);

        if ($productCheck['status'] == 'error') {
            return $productCheck;
        }

        //ürün var
        $product = $productCheck["data"];
        $productStock = $product['urunstok'];

        $resultMessage = "";
        if ($productStock < $quantity) {
            $quantity = $productStock;
            $resultMessage .= "Yeterli stok yok. Sepete eklenen ürünün adedi güncellendi.";
        }

        if($productStock == 0){
            $resultStatus = "error";
            $resultMessage .= "Stokta ürün bulunmamaktadır.";
            return [
                "status" => $resultStatus,
                "message" => $resultMessage,
                'cartData'=>$product
            ];
        }

        // sepete eklenen ürün herhangi bir kampanyaya dahil mi kontrol edelim

        $discountedAmount = 0;
        $discountedExplanation = "";
        $campaignControl = $this->checkCampaign(
            $productID,$product['kategoriid'],$product['markaid'],$product['tedarikciid']);
        if(!empty($campaignControl)){
            $discount = $this->calculateDiscount($campaignControl, $quantity, $price);
            $discountedAmount = $discount['discountedAmount'];
            $discountedExplanation = $discount['discountedExplanation'];
            $resultMessage .= $discountedExplanation ."<br>";
        }

        $updateSql = "
            UPDATE 
                uyesepet 
            SET 
                urunadet = :urunadet, indirimmiktari = :indirimmiktari, indirimaciklamasi = :indirimaciklamasi, sepetguncelletarih = :sepetguncelletarih 
            WHERE 
                sepetbenzersiz = :cartUniqID
        ";

        $updateParams = [
            'urunadet' => $quantity,
            'indirimmiktari' => $discountedAmount,
            'indirimaciklamasi' => $discountedExplanation,
            'cartUniqID' => $cart_id,
            'sepetguncelletarih' => date("Y-m-d H:i:s")
        ];

        $this->db->beginTransaction();

        if ($this->db->update($updateSql, $updateParams)) {
            $this->db->commit();
            $resultStatus = "success";
            $resultMessage .= "Ürün güncellendi.";

            $sql = "
                SELECT 
                    urunid,urunfiyat,indirimmiktari,indirimaciklamasi,urunparabirim,parabirimsimge,urunadet
                FROM 
                    uyesepet 
                    INNER JOIN urunparabirim ON uyesepet.urunparabirim = urunparabirim.parabirimid
                WHERE sepetbenzersiz = :cart_id
            ";
            $cart = $this->db->select($sql, ['cart_id' => $cart_id]);
            $cart = $cart[0];
        }
        else {
            $this->db->rollBack();
            $resultStatus = "error";
            $resultMessage .= "Ürün güncellenemedi.";
        }

        $visitor = $this->visitor;
        $visitor['visitorCart'] = $this->getCart($visitor['visitorUniqID']);
        $this->casper->setVisitor($visitor);
        $this->session->updateSession("casper",$this->casper);

        return [
            "message" => $resultMessage,
            "status" => $resultStatus,
            'cartData'=>$cart
        ];
    }

    public function removeCart($cart_id):array
    {
        $visitorUniqID = $this->visitor['visitorUniqID'];

        $sql = "
            DELETE FROM 
                uyesepet 
            WHERE 
                sepetbenzersiz = :cart_id AND uyebenzersiz = :visitorUniqID
        ";

        $params = [
            'cart_id' => $cart_id,
            'visitorUniqID' => $visitorUniqID
        ];

        $this->db->beginTransaction();
        if ($this->db->delete($sql, $params)) {
            $this->db->commit();

            $return = [
                "message" => "Ürün sepetten kaldırıldı.",
                "status" => "success"
            ];
        }
        else {
            $this->db->rollBack();
            $return = [
                "message" => "Ürün sepetten kaldırılamadı.",
                "status" => "error"
            ];
        }

        $visitor = $this->visitor;
        $visitor['visitorCart'] = $this->getCart($visitor['visitorUniqID']);
        $this->casper->setVisitor($visitor);
        $this->session->updateSession("casper",$this->casper);

        return $return;
    }

    public function getCart($visitorUniqID):array
    {
        // Get the visitor's unique ID
        //print_r($this->visitor);exit();
        //$visitorUniqID = $this->visitor['visitorUniqID'];
        // Use the unique ID to query the database and get the cart items
        // Note: You'll need to replace 'uyesepet' and 'uyebenzersiz' with your actual table and column names
        $sql = "
            SELECT 
                * 
            FROM 
                uyesepet 
            WHERE 
                sepetsil = 0 AND sepetdurum = 0 AND uyebenzersiz = :visitorUniqID
            ORDER BY urunid 
        ";
        $data = $this->db->select($sql, ['visitorUniqID' => $visitorUniqID]);
        if($data){
            include_once MODEL."Product.php";
            $product = new Product($this->db, $this->json);

            $cardInfo = [];
            $cardInfo["totalPrice"] = 0;
            $cardInfo["totalDesi"] = 0;
            $cardInfo["totalQuantity"] = 0;
            $cardInfo["totalCount"] = count($data);
            foreach ($data as $value) {
                $cartUniqID = $value["sepetbenzersiz"];
                $productID = $value["urunid"];
                $productSelectedStockCode = $value["urunstokkodu"];
                $productSelectedVariant = $value["urunvaryant"];
                $productDesi = $value["urundesi"];
                $productQuantity = $value["urunadet"];
                $productCurrencyID = $value["urunparabirim"];

                $productCurrency = $product->getCurrencies($productCurrencyID);
                $productCurrencySymbol = $productCurrency["parabirimsimge"];
                $productCurrencyCode = $productCurrency["parabirimkod"];

                $productPrice = $value["urunfiyat"];
                $productTax = $value["urunkdv"];
                $productShippingCost = $value["urunkargoucreti"];
                $productReturnQuantity = $value["uruniadeadet"];

                $productDiscountAmount = $value["indirimmiktari"];
                $productDiscountDescription = $value["indirimaciklamasi"];

                $productDetail = $product->getProductByID($productID);
                //print_r($productDetail);exit();
                $productDetail = $productDetail[0];
                //print_r($productDetail);exit();
                $productName = $productDetail["sayfaad"];
                $productImage = $productDetail["resim_url"];
                $productLink = $productDetail["link"];
                $productMinQuantity = $productDetail["urunminimummiktar"];
                $productMaxQuantity = $productDetail["urunmaksimummiktar"];
                //artış kat sayısı
                $productCoefficient = $productDetail["urunkatsayi"];
                //mikar birim id ve miktar birim adı
                $routerResult = $this->session->getSession("routerResult");
                $productUnitID = $productDetail["urunmiktarbirimid"];
                $productUnitName = $product->getProductUnitName($productUnitID,$this->helper->toLowerCase($this->routerResult["languageCode"]));

                $cardInfo["totalPrice"] += $productPrice * $productQuantity;
                $cardInfo["totalDesi"] += $productDesi * $productQuantity;
                $cardInfo["totalQuantity"] += $productQuantity;

                $cardInfo["cartProducts"][] = [
                    "cartUniqID" => $cartUniqID,
                    "productID" => $productID,
                    "productSelectedStockCode" => $productSelectedStockCode,
                    "productSelectedVariant" => $productSelectedVariant,
                    "productDesi" => $productDesi,
                    "productQuantity" => $productQuantity,
                    "productCurrencyID" => $productCurrencyID,
                    "productPrice" => $productPrice,
                    "productTax" => $productTax,
                    "productShippingCost" => $productShippingCost,
                    "productReturnQuantity" => $productReturnQuantity,
                    "productDiscountAmount" => $productDiscountAmount,
                    "productDiscountDescription" => $productDiscountDescription,
                    "productName" => $productName,
                    "productImage" => $productImage,
                    "productLink" => $productLink,
                    "productMinQuantity" => $productMinQuantity,
                    "productMaxQuantity" => $productMaxQuantity,
                    "productCoefficient" => $productCoefficient,
                    "productUnitID" => $productUnitID,
                    "productUnitName" => $productUnitName,
                    "productCurrencySymbol" => $productCurrencySymbol,
                    "productCurrencyCode" => $productCurrencyCode
                ];
            }
            return $cardInfo;
        }
        else{
            return [];
        }
    }

    public function getCartByUniqID($cartUniqID){
        $sql = "
            SELECT 
                uyesepet.*,sayfaad 
            FROM 
                uyesepet 
                    INNER JOIN sayfa ON uyesepet.urunid = sayfa.sayfaid
            WHERE 
                sepetbenzersiz = :cartUniqID
        ";
        $data = $this->db->select($sql, ['cartUniqID' => $cartUniqID]);
        if($data) {
            return $data[0];
        }
        else {
            return [];
        }
    }

    public function getCartUniqIDByOrderUniqID($orderUniqID){
        $sql = "
            SELECT 
                uyesepet.*,sayfaad 
            FROM 
                uyesepet 
                    INNER JOIN sayfa ON uyesepet.urunid = sayfa.sayfaid
            WHERE 
                siparisbenzersiz = :cartUniqID
        ";
        $data = $this->db->select($sql, ['cartUniqID' => $orderUniqID]);
        if($data) {
            return $data;
        }
        else {
            return [];
        }
    }
    public function getCartByOrderUniqID($orderUniqID):array
    {
        // Get the visitor's unique ID
        //print_r($this->visitor);exit();
        //$visitorUniqID = $this->visitor['visitorUniqID'];
        // Use the unique ID to query the database and get the cart items
        // Note: You'll need to replace 'uyesepet' and 'uyebenzersiz' with your actual table and column names
        $sql = "
            SELECT 
                * 
            FROM 
                uyesepet 
            WHERE 
                siparisbenzersiz = :orderUniqID
            ORDER BY urunid 
        ";
        $data = $this->db->select($sql, ['orderUniqID' => $orderUniqID]);
        if($data){
            include_once MODEL."Product.php";
            $product = new Product($this->db, $this->json);

            $cardInfo = [];
            $cardInfo["totalPrice"] = 0;
            $cardInfo["totalDesi"] = 0;
            $cardInfo["totalQuantity"] = 0;
            $cardInfo["totalCount"] = count($data);
            foreach ($data as $value) {
                $cartUniqID = $value["sepetbenzersiz"];
                $productID = $value["urunid"];
                $productSelectedStockCode = $value["urunstokkodu"];
                $productSelectedVariant = $value["urunvaryant"];
                $productDesi = $value["urundesi"];
                $productQuantity = $value["urunadet"];
                $productCurrencyID = $value["urunparabirim"];

                $productCurrency = $product->getCurrencies($productCurrencyID);
                $productCurrencySymbol = $productCurrency["parabirimsimge"];
                $productCurrencyCode = $productCurrency["parabirimkod"];

                $productPrice = $value["urunfiyat"];
                $productTax = $value["urunkdv"];
                $productShippingCost = $value["urunkargoucreti"];
                $productReturnQuantity = $value["uruniadeadet"];

                $productDiscountAmount = $value["indirimmiktari"];
                $productDiscountDescription = $value["indirimaciklamasi"];

                $productDetail = $product->getProductByID($productID);
                //print_r($productDetail);exit();
                $productDetail = $productDetail[0];
                //print_r($productDetail);exit();
                $productName = $productDetail["sayfaad"];
                $productImage = $productDetail["resim_url"];
                $productLink = $productDetail["link"];
                $productMinQuantity = $productDetail["urunminimummiktar"];
                $productMaxQuantity = $productDetail["urunmaksimummiktar"];
                //artış kat sayısı
                $productCoefficient = $productDetail["urunkatsayi"];
                //mikar birim id ve miktar birim adı
                $routerResult = $this->session->getSession("routerResult");
                $productUnitID = $productDetail["urunmiktarbirimid"];

                $languageCode = $this->routerResult["languageCode"] ?? "tr";
                $productUnitName = $product->getProductUnitName($productUnitID,$this->helper->toLowerCase($languageCode));

                $cardInfo["totalPrice"] += $productPrice * $productQuantity;
                $cardInfo["totalDesi"] += $productDesi * $productQuantity;
                $cardInfo["totalQuantity"] += $productQuantity;

                $cardInfo["cartProducts"][] = [
                    "cartUniqID" => $cartUniqID,
                    "productID" => $productID,
                    "productSelectedStockCode" => $productSelectedStockCode,
                    "productSelectedVariant" => $productSelectedVariant,
                    "productDesi" => $productDesi,
                    "productQuantity" => $productQuantity,
                    "productCurrencyID" => $productCurrencyID,
                    "productPrice" => $productPrice,
                    "productTax" => $productTax,
                    "productShippingCost" => $productShippingCost,
                    "productReturnQuantity" => $productReturnQuantity,
                    "productDiscountAmount" => $productDiscountAmount,
                    "productDiscountDescription" => $productDiscountDescription,
                    "productName" => $productName,
                    "productImage" => $productImage,
                    "productLink" => $productLink,
                    "productMinQuantity" => $productMinQuantity,
                    "productMaxQuantity" => $productMaxQuantity,
                    "productCoefficient" => $productCoefficient,
                    "productUnitID" => $productUnitID,
                    "productUnitName" => $productUnitName,
                    "productCurrencySymbol" => $productCurrencySymbol,
                    "productCurrencyCode" => $productCurrencyCode
                ];
            }
            return $cardInfo;
        }
        else{
            return [];
        }
    }

    public function updateCartFromVisitorUniqIDtoMemberUniqID($visitorUniqID, $memberUniqID):bool
    {

        $sql = "
            UPDATE 
                uyesepet 
            SET 
                uyebenzersiz = :memberUniqID 
            WHERE 
                uyebenzersiz = :visitorUniqID
        ";

        $params = [
            'memberUniqID' => $memberUniqID,
            'visitorUniqID' => $visitorUniqID
        ];

        $this->db->beginTransaction("updateCartFromVisitorUniqIDtoMemberUniqID");
        //Log::write("Is in transaction after beginTransaction: " . ($this->db->inTransaction() ? "yes" : "no"), "info");

        $result = $this->db->update($sql, $params);

        if ($result>0) {
            $this->db->commit("updateCartFromVisitorUniqIDtoMemberUniqID");
            Log::write("Is in transaction after commit: " . ($this->db->inTransaction() ? "yes" : "no"), "info");
            return true;
        }
        else {
            $this->db->rollBack("updateCartFromVisitorUniqIDtoMemberUniqID");
            Log::write("Is in transaction after rollback: " . ($this->db->inTransaction() ? "yes" : "no"), "info");
            return false;
        }

    }

    public function checkProduct($productID,$stockCode):array
    {

        $where = "urunozellikleri.sayfaid = :product_id AND JSON_EXTRACT(variantProperties, '$[*].variantStockCode') LIKE CONCAT('%', :stock_code, '%')";
        $params = ['product_id' => $productID, 'stock_code' => $stockCode];

        $sql = "
            SELECT 
                urunsatisfiyat,urunparabirim,urunstok,variantProperties,urunkdv,tedarikciid,markaid,kategoriid 
            FROM 
                urunozellikleri 
                inner join sayfalistekategori on urunozellikleri.sayfaid = sayfalistekategori.sayfaid
            WHERE 
                $where
        ";

        Log::write("checkProduct: $sql", "info");


        $product = $this->db->select($sql, $params);

        if (!$product) {
            return [
                "status" => "error",
                "message" => "Ürün bulunamadı.|$productID,$stockCode|checkProduct."
            ];
        }
        return [
            "status" => "success",
            "data" => $product[0]
        ];
    }

    public function checkCampaign($productID, $categoryID, $brandID, $supplierID)
    {
        $this->config->includeClass('Campaign');
        $campaignModel = new Campaign($this->db);
        return $campaignModel->checkCampaign(
            $productID,
            $categoryID,
            $brandID,
            $supplierID
        );
    }

    function calculateDiscount($campaignControl, $quantity, $price):array {
        $discountedAmount = 0;
        $discountedExplanation = "";
        $result = "";

        if($campaignControl[0]['tur']=="miktar_indirim"){
            array_multisort(array_column($campaignControl, 'miktar_sinir'), SORT_DESC, $campaignControl);
            foreach ($campaignControl as $campaign) {
                $amountLimit = $campaign['miktar_sinir'];
                $discountRate = $campaign['indirim_orani'];
                if($quantity >= $amountLimit){
                    $discountedAmount = $price * $quantity * $discountRate;
                    $discountedExplanation = $campaign["ad"];
                    $result .= $discountedExplanation ."<br>";
                    break;
                }
            }
        }

        return [
            'discountedAmount' => $discountedAmount,
            'discountedExplanation' => $discountedExplanation,
            'result' => $result
        ];
    }

    public function fixPrice($product):array
    {
        $productCurrencyID = $product['urunparabirim'];
        $productSalesPrice = $product['urunsatisfiyat'];

        $configPriceSettings = $this->casper->getSiteConfig()["priceSettings"][0];
        $configPriceUnit = $configPriceSettings["parabirim"];

        $currencyRates = $this->casper->getSiteConfig()["currencyRates"];

        $usdToTry = $currencyRates["usd"];
        $eurToTry = $currencyRates["euro"];

        if($productCurrencyID != $configPriceUnit){
            switch ($configPriceUnit) {
                case 1:
                    if ($productCurrencyID == 2) {
                        $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * $usdToTry : "0.00";
                    }
                    elseif ($productCurrencyID == 3) {
                        $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * $eurToTry : "0.00";
                    }
                    $productCurrencyID = 1;
                    break;
                case 2:
                    if ($productCurrencyID == 1) {
                        $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice / $usdToTry : "0.00";
                    }
                    elseif ($productCurrencyID == 3) {
                        $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * ($eurToTry / $usdToTry) : "0.00";
                    }
                    $productCurrencyID = 2;
                    break;
                case 3:
                    if ($productCurrencyID == 1) {
                        $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice / $eurToTry : "0.00";
                    }
                    elseif ($productCurrencyID == 2) {
                        $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * ($usdToTry / $eurToTry) : "0.00";
                    }
                    $productCurrencyID = 3;
                    break;
            }
        }
        //die("$productSalesPrice");
        //$productSalesPrice = $this->helper->formatCurrency($productSalesPrice);
        $productSalesPrice = number_format($productSalesPrice, 2, '.', '');

        return [
            'price' => $productSalesPrice,
            'currencyID' => $productCurrencyID
        ];
    }

    public function isProductInCart($visitorUniqID,$productStockCode):array{
        $checkSql = "
            SELECT 
                sepetbenzersiz,urunadet
            FROM 
                uyesepet 
            WHERE 
                uyebenzersiz = :uyebenzersiz AND urunstokkodu = :urunstokkodu AND 
                sepetsil=0 AND sepetdurum = 0 AND siparisbenzersiz = ''
        ";
        $result = $this->db->select($checkSql, ['uyebenzersiz' => $visitorUniqID, 'urunstokkodu' => $productStockCode]);
        if($result){
            return
            [
                "cartUniqID" =>$result[0]['sepetbenzersiz'],
                "quantity" => $result[0]['urunadet']
            ];
        }
        else{
            return [];
        }
    }

    public function updateCartByOrderUniqID($orderUniqID, array $updateData){

        // Güncellenecek alanları ve değerleri SQL sorgusuna ekleyelim
        $updateFields = "";
        foreach ($updateData as $field => $value) {
            $updateFields .= "$field = :$field, ";
        }
        // Son virgülü kaldıralım
        $updateFields = rtrim($updateFields, ", ");

        // SQL sorgumuzu oluşturalım
        $sql = "UPDATE uyesepet SET $updateFields WHERE siparisbenzersiz = :orderUniqID";

        // Parametrelerimizi oluşturalım
        $params = array_merge($updateData, ["orderUniqID" => $orderUniqID]);

        $this->db->beginTransaction();
        if ($this->db->update($sql, $params)) {
            $this->db->commit();
            return true;
        }
        else {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCartByCartUniqID($cartUniqID, array $updateData){

        // Güncellenecek alanları ve değerleri SQL sorgusuna ekleyelim
        $updateFields = "";
        foreach ($updateData as $field => $value) {
            $updateFields .= "$field = :$field, ";
        }
        // Son virgülü kaldıralım
        $updateFields = rtrim($updateFields, ", ");

        // SQL sorgumuzu oluşturalım
        $sql = "UPDATE uyesepet SET $updateFields WHERE sepetbenzersiz = :cartUniqID";

        // Parametrelerimizi oluşturalım
        $params = array_merge($updateData, ["cartUniqID" => $cartUniqID]);

        $this->db->beginTransaction();
        if ($this->db->update($sql, $params)) {
            $this->db->commit();
            return true;
        }
        else {
            $this->db->rollBack();
            return false;
        }
    }

    public function getCartHtmlForMail($orderProducts){
        //Database $db,Helper $helper,Session $session,Config $config
        $helper = $this->helper;
        $config = $this->config;

        $totalQuantity = 0;
        $totalPrice = 0;
        $totalDesi = 0;
        $totalModel = 0;
        $totalDiscountAmount = 0;

        $orderHtml ='<div class="cart-items">';
        foreach ($orderProducts['cartProducts'] as $product){
            $totalModel++;
            $cartUniqID = $product['cartUniqID'];
            $productID = $product['productID'];
            $productName = $product['productName'];
            $productStockCode = $product['productSelectedStockCode'];
            $productLink = $product['productLink'];
            $productPrice = $product['productPrice'];
            $productQuantity = $product['productQuantity'];
            $productQuantity = str_replace(".0000", "", $productQuantity);
            $productDesi = $product['productDesi'];

            $productMinQuantity = $product['productMinQuantity'];
            $productMinQuantity = str_replace(".0000", "", $productMinQuantity);

            $productMaxQuantity = $product['productMaxQuantity'];
            $productMaxQuantity = str_replace(".0000", "", $productMaxQuantity);

            $productCoefficient = $product['productCoefficient'];
            $productCoefficient = str_replace(".0000", "", $productCoefficient);

            $productUnitName = $product['productUnitName'];

            $productCurrencyID = $product['productCurrencyID'];
            $productCurrencySymbol = $product['productCurrencySymbol'];
            $productCurrencyCode = $product['productCurrencyCode'];

            $productSelectedVariant = $product['productSelectedVariant'];

            $productDiscountAmount = $product['productDiscountAmount'];
            $productDiscountDescription = $product['productDiscountDescription'];

            $totalDiscountAmount += $productDiscountAmount;


            $productTotalDesi = $productDesi * $productQuantity;
            $totalDesi += $productTotalDesi;

            $totalQuantity += $productQuantity;
            $productTotalPrice = $productPrice * $productQuantity;

            $totalPrice += $productTotalPrice;

            $productImage = explode(", ", $product['productImage'])[0];
            $imageRoot = imgRoot."?imagePath=".trim($productImage)."&width=150";

            $orderHtml .='<div class="cart-item">';//cart-item
            $orderHtml .='<div class="cart-image-container">';//cart-image-container
            $orderHtml .='<a href="'.$config->http.$config->hostDomain.$productLink.'" class="cart-item-image-link" ><img src="'.$config->http.$config->hostDomain.$imageRoot.'" class="cart-item-image" alt="'. $productName.'" loading="lazy" width="150" height="150"></a>';
            $orderHtml .='</div>';//cart-image-container
            $orderHtml .='<div class="cart-item-details">';//cart-item-details
            $orderHtml .='<a href="'.$config->http.$config->hostDomain.$productLink.'" class="cart-item-title">'.$productName.'</a>';
            $orderHtml .='<div class="cart-item-variant-text">'._sepet_urun_stok_kod_yazi.' '.$productStockCode.'</div>';

            if(!empty($productSelectedVariant)){
                $productSelectedVariant = json_decode($productSelectedVariant, true);
                foreach ($productSelectedVariant as $variant) {
                    $orderHtml .='<div class="cart-item-variant-text">'.$variant['attribute']['name'].': '.$variant['attribute']['value'].'</div>';
                }
            }

            $orderHtml .='</div>';//cart-item-details

            $orderHtml .='<div class="cart-item-price" id="price-'.$cartUniqID.'">'.$productCurrencySymbol.' '.$helper->formatCurrency($productPrice).' </div>';
            $orderHtml .='<div class="cart-item-quantity">';//cart-item-quantity
                $orderHtml .='<div class="cart-item-quantity-wrapper">';//cart-item-quantity-wrapper
                    $orderHtml .='<div class="quantity" title="Quantity">';//quantity
                        //$orderHtml .='<button class="qtyBtn minus" name="minus" id="minus-'.$cartUniqID.'" data-cartID="'.$cartUniqID.'"><i>-</i></button>';
                        $orderHtml .= $productQuantity;
                        //$orderHtml .='<button class="qtyBtn plus" name="plus" id="plus-'.$cartUniqID.'" data-cartID="'.$cartUniqID.'"><i>+</i></button>';
                    $orderHtml .='</div>';//quantity
                    $orderHtml .='<span class="productUnitName">'.$productUnitName.'</span>';
                $orderHtml .='</div>';//cart-item-quantity-wrapper
            $orderHtml .='</div>';//cart-item-quantity
            $orderHtml .='<div class="cart-totals">';//cart-totals
            $orderHtml .='<span class="cart-item-total-price">'.$productCurrencySymbol.' '.$helper->formatCurrency($productTotalPrice).'</span>';
            if($productDiscountAmount>0){
                $orderHtml .='<span class="cart-item-discount-description">'.$productDiscountDescription.'</span>';
                $orderHtml .='<span class="cart-item-discount-amount">'.$productCurrencySymbol.' '.$helper->formatCurrency($productDiscountAmount).'</span>';
                $orderHtml .='<span class="cart-item-discounted-price"><i>'.$productCurrencySymbol.' '.$helper->formatCurrency($productTotalPrice-$productDiscountAmount).'</i></span>';
            }
            $orderHtml .='</div>';//cart-totals
            $orderHtml .='</div>';//cart-item

        }
        $orderHtml .='</div>';
        $orderHtml .='<div class="cart-summary">';
        $orderHtml .='<ul>';
        $orderHtml .='<li class="total-model">'._sepet_toplam_urun_modeli.': '.$totalModel .'</li>';
        $orderHtml .='<li class="total-quantitiy">'._sepet_toplam_urun_adedi.': '.$totalQuantity.'</li>';
        $orderHtml .='<li class="total-price">'._sepet_sepet_toplam_tutar_yazi.' '.$helper->formatCurrency($totalPrice, 2) .' '.$productCurrencySymbol.'</li>';
        if($totalDiscountAmount>0){
            $orderHtml .='<li class="total-discount">'._sepet_indirim_toplam_tutar_yazi.' <i>'. $helper->formatCurrency($totalDiscountAmount, 2) .' '.$productCurrencySymbol.'</i></li>';
            $orderHtml .='<li class="total-discounted-price">'._sepet_indirimli_toplam_tutar_yazi.' '. $helper->formatCurrency($totalPrice-$totalDiscountAmount, 2) .' '.$productCurrencySymbol.'</li>';
        }
        $orderHtml .='</ul>';
        $orderHtml .='</div>';

        return $orderHtml;
    }
}