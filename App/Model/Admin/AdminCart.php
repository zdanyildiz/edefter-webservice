<?php

class AdminCart
{
    private AdminDatabase $db;
    private Config $config;
    private Json $json;
    private Helper $helper;
    public function __construct($db,$config)
    {
        $this->db = $db;
        $this->config = $config;
        $this->json = $config->Json;
        $this->helper = $config->Helper;
    }

    public function getCartByOrderUniqID($orderUniqID,$stockCode):array
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
                siparisbenzersiz = :orderUniqID and urunstokkodu = :stockCode
            ORDER BY urunid 
        ";
        $data = $this->db->select($sql, ['orderUniqID' => $orderUniqID, 'stockCode' => $stockCode]);
        if($data){
            include_once MODEL."Admin/AdminProduct.php";
            $product = new AdminProduct($this->db, $this->config);

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

                $productCurrency = $product->getCurrency($productCurrencyID);
                $productCurrencySymbol = $productCurrency["parabirimsimge"];
                $productCurrencyCode = $productCurrency["parabirimkod"];

                $productPrice = $value["urunfiyat"];
                $productTax = $value["urunkdv"];
                $productShippingCost = $value["urunkargoucreti"];
                $productReturnQuantity = $value["uruniadeadet"];

                $productDiscountAmount = $value["indirimmiktari"];
                $productDiscountDescription = $value["indirimaciklamasi"];

                $productDetail = $product->getProductByID($productID);
                if(!$productDetail){
                    continue;
                }
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

    public function addCart($cartData):mixed
    {

        $sql = "
            INSERT INTO 
                uyesepet 
            SET 
                uyebenzersiz = :memberUniqID, 
                sepetbenzersiz = :cartUniqID, 
                sepetolusturtarih = :cartCreatedDate, 
                sepetguncelletarih = :cartUpdatedDate, 
                urunstokkodu = :productStockCode, 
                urunid = :productID, 
                urunvaryant = :productVariant, 
                urundesi = :productDesi, 
                urunadet = :productQuantity, 
                urunparabirim = :productCurrencyID, 
                urunfiyat = :productPrice, 
                urunkdv = :productTax, 
                urunkargoucreti = :productShippingCost, 
                uruniadeadet = :productReturnQuantity, 
                indirimmiktari = :productDiscountAmount, 
                indirimaciklamasi = :productDiscountDescription, 
                sepetdurum = :cartStatus, 
                odemedurum = :paymentStatus, 
                siparisbenzersiz = :orderUniqID, 
                sepetsil = :cartDeleted
        ";

        $params = [
            'memberUniqID' => $cartData['memberUniqID'],
            'cartUniqID' => $cartData['cartUniqID'],
            'cartCreatedDate' => $cartData['cartCreatedDate'],
            'cartUpdatedDate' => $cartData['cartUpdatedDate'],
            'productStockCode' => $cartData['productStockCode'],
            'productID' => $cartData['productID'],
            'productVariant' => $cartData['productVariant'],
            'productDesi' => $cartData['productDesi'],
            'productQuantity' => $cartData['productQuantity'],
            'productCurrencyID' => $cartData['productCurrencyID'],
            'productPrice' => $cartData['productPrice'],
            'productTax' => $cartData['productTax'],
            'productShippingCost' => $cartData['productShippingCost'],
            'productReturnQuantity' => $cartData['productReturnQuantity'],
            'productDiscountAmount' => $cartData['productDiscountAmount'],
            'productDiscountDescription' => $cartData['productDiscountDescription'],
            'cartStatus' => $cartData['cartStatus'],
            'paymentStatus' => $cartData['paymentStatus'],
            'orderUniqID' => $cartData['orderUniqID'],
            'cartDeleted' => $cartData['cartDeleted']
        ];

        return $this->db->insert($sql, $params);
    }

    public function updateCart($cartUniqID,$updateCartData):int
    {

        $updateFields = "";

        foreach ($updateCartData as $key => $value) {
            $updateFields .= $key . " = :" . $key . ",";
        }

        $updateFields = rtrim($updateFields, ",");

        $sql = "
            UPDATE 
                uyesepet 
            SET 
                $updateFields 
            WHERE 
                sepetbenzersiz = :sepetbenzersiz
        ";

        $params = $updateCartData;
        $params['sepetbenzersiz'] = $cartUniqID;

        return $this->db->update($sql, $params);
    }

    //transaction
    public function beginTransaction($funcName = "")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName="")
    {
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }

    public function inTransaction()
    {
        return $this->db->inTransaction();
    }
}