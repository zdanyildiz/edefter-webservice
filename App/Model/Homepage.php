<?php
class Homepage {
    private Product $productModel;
    private Database $db;

    public $languageID = 1;
    public $languageCode = "tr";

    public $homePageCategoryID = 0;
    public $homePageCategoryContent = "";
    public $homePageCategoryImage = "";

    public $seoTitle = "404";
    public $seoDescription = "Sayfa bulunamadı";
    public $seoLink = "/404";
    public $seoKeywords = "404";
    public $seoImage = "";

    public function __construct($db,$json,$languageID) {
        $this->db = $db;
        $this->getHomepageLanguage($languageID);
        $this->getHomePage($this->languageID);
        $this->productModel = new Product($db,$json);
    }

    public function getHomepageLanguage($languageID) {
        $sql = "
            SELECT
                *
            FROM
                dil
            WHERE
                dilsil=0 AND dilaktif=1 AND dilid = :languageID
        ";
        $params = [
            'languageID' => $languageID
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            $result = $result[0];
            $this->languageID = $result['dilid'];
            $this->languageCode = $result['dilkisa'];
        }
    }

    public  function getHomePage($languageID)
    {
        $sql = "
            SELECT
                baslik as seoTitle,
                aciklama as seoDescription,
                kelime as seoKeywords,
                link as seoLink,
                seo.resim as seoImage,
                kategoriid,
                kategoriicerik,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as resim_url 
            FROM
                kategori
                    INNER JOIN seo ON kategori.benzersizid=seo.benzersizid
                    LEFT JOIN resim ON resim.resimid = kategori.resimid
                    LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
            WHERE
                kategorisil = 0 AND kategoriaktif=1 AND anasayfa=1 AND dilid = :languageID
        ";

        $homepage = $this->db->select($sql, ['languageID' => $languageID]);

        if ($homepage) {
            $this->homePageCategoryID = $homepage[0]['kategoriid'];
            $this->homePageCategoryContent = $homepage[0]['kategoriicerik'];
            $this->homePageCategoryImage = $homepage[0]['resim_url'];
            $this->seoTitle = $homepage[0]['seoTitle'];
            $this->seoDescription = $homepage[0]['seoDescription'];
            $this->seoKeywords = $homepage[0]['seoKeywords'];
            $this->seoLink = $homepage[0]['seoLink'];
            $this->seoImage = $homepage[0]['seoImage'];
        }
    }

    public function getSpecialOfferProducts($languageID): array{
        $SpecialOfferProductIds = $this->productModel->getSpecialOffers($languageID);
        //print_r($SpecialOfferProductIds);exit();
        $SpecialOfferProducts = [];
        foreach ($SpecialOfferProductIds as $i => $product) {
            array_push($SpecialOfferProducts, $this->productModel->getProductByID($product['sayfaid']));
        }
        //print_r($SpecialOfferProducts);exit();
        return $SpecialOfferProducts;
    }

    public function getHomepageProducts($languageID) {
        $homepageProductIds = $this->productModel->getHomepageProducts($languageID);
        $homepageProducts = [];
        foreach ($homepageProductIds as $i => $product) {
            array_push($homepageProducts, $this->productModel->getProductByID($product['sayfaid']));
        }
        return $homepageProducts;
    }

    public function getDiscountedProducts($languageID) {
        $discountedProductIds = $this->productModel->getDiscountedProducts($languageID);
        $discountedProducts = [];
        foreach ($discountedProductIds as $i => $product) {
            array_push($discountedProducts, $this->productModel->getProductByID($product['sayfaid']));
        }
        return $discountedProducts;
    }

    public function getNewProducts($languageID) {
       $newProductIds = $this->productModel->getNewProducts($languageID);
        $newProducts = [];
        foreach ($newProductIds as $i => $product) {
            array_push($newProducts, $this->productModel->getProductByID($product['sayfaid']));
        }
        return $newProducts;
    }
}