<?php

class DetectContent{

    private $db;
    private $router;
    private $url;
    private $baseUrl;

    private $urlExtension;

    private $query;

    public function __construct($db,$router){

        $this->db = $db;
        $this->router = $router;
        $this->url = $router->url;
        $this->query = $router->query;


        $baseUrl = parse_url($this->url);
        $this->baseUrl = $baseUrl['path'] ?? "";

        if($this->getContentByURL($this->url)){
            return;
        }

        //@todo arama linki /arama ya da /search olmayacak
        if($this->getContentByURL($this->baseUrl)){
            return;
        }

        $this->urlExtension = pathinfo($this->baseUrl, PATHINFO_EXTENSION);

        if( empty( $this->urlExtension ) && empty($this->query) ){

            $this->router->seoTitle = "404";
            $this->router->seoDescription = "Sayfa bulunamadı";
            $this->router->seoLink = "/404";
            $this->router->seoKeywords = "404";

            $this->router->contentName = "Page";
            $this->router->languageID = 1;
            $this->router->languageCode = "tr";

            $this->router->contentUniqID="404";

            return;
        }

        if(!$this->checkContentByExtension() && empty($this->query)){

            $this->router->seoTitle = "404";
            $this->router->seoDescription = "Sayfa bulunamadı";
            $this->router->seoLink = "/404";
            $this->router->seoKeywords = "404";

            $this->router->contentName = "Page";
            $this->router->languageID = 1;
            $this->router->languageCode = "tr";

            $this->router->contentUniqID="404";
        }

    }

    public function getContentByURL($url){

        $sql = "
            SELECT
                *
            FROM
                seo
            WHERE
                link = :link or orjinallink = :orjinalLink 
        ";

        $seo = $this->db->select($sql, ['link' => $url, 'orjinalLink' => $url]);

        if($seo){
            $seo = $seo[0];
            $this->router->seoTitle = $seo['baslik'];
            $this->router->seoDescription = $seo['aciklama'];
            $this->router->seoKeywords = $seo['kelime'];
            $this->router->seoLink = $seo['link'];
            $this->router->seoImage = $seo['resim'] ?? "";

            $this->router->contentUniqID = $seo['benzersizid'];

            if($this->checkCategoryByUniqID()){
                return true;
            }
            else if($this->checkPageByUniqID()){
                return true;
            }

            return false;
        }
        else{
            $this->router->seoTitle = "404";
            $this->router->seoDescription = "Sayfa bulunamadı";
            $this->router->seoLink = "/404";
            $this->router->seoKeywords = "404";
        }
    }

    public function checkCategoryByUniqID(){
        $sql = "
            SELECT
                kategori.*,dil.dilid,dil.dilkisa
            FROM
                kategori
                    INNER JOIN dil ON kategori.dilid = dil.dilid
            WHERE
                kategorisil=0 AND kategoriaktif=1 AND benzersizid = :uniqID
        ";

        $category = $this->db->select($sql, ['uniqID' => $this->router->contentUniqID]);

        if($category){
            $category = $category[0];

            if($category['anasayfa'] == 1){
                $this->router->contentName = "HomePage";
                $this->router->languageID = $category['dilid'];
                $this->router->languageCode = mb_strtolower($category['dilkisa']);
                return true;
            }

            $this->router->contentName = "Category";
            $this->router->languageID = $category['dilid'];
            $this->router->languageCode = mb_strtolower($category['dilkisa']);
            return true;
        }
        else{
            return false;
        }
    }

    public function checkPageByUniqID()
    {
        $sql = "
            SELECT
                *
            FROM
                sayfa
                    INNER JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                    INNER JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                    INNER JOIN dil ON kategori.dilid = dil.dilid
            WHERE
                sayfa.benzersizid = :uniqID
        ";

        $page = $this->db->select($sql, ['uniqID' => $this->router->contentUniqID]);

        if ($page) {
            $page = $page[0];
            $this->router->contentName = "Page";
            $this->router->languageID = $page['dilid'];
            $this->router->languageCode = mb_strtolower($page['dilkisa']);

            return true;
        }
        else{
            return false;
        }

    }

    public function checkContentByExtension()
    {
        $baseUrl = $this->baseUrl;
        $baseUrl = str_replace($this->urlExtension, '', $baseUrl);
        $baseUrl = str_replace('.', '', $baseUrl);

        $type = substr($baseUrl,  - 1);
        $id = intval(substr($baseUrl, 0, -1));

        if($type == "s"){
            return $this->checkPageByID($id);
        }
        elseif ($type == "m"){
            return $this->checkCategoryByID($id);
        }
        elseif($type== 'l') {
            return $this->checkHomePageByID($id);
        }

        return false;
    }

    public function checkPageByID($id)
    {
        $sql = "
            SELECT
                seo.benzersizid,seo.baslik,seo.aciklama,seo.kelime,seo.link,seo.resim,
                dil.dilid,dil.dilkisa
            FROM
                sayfa
                    INNER JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                    INNER JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                    INNER JOIN dil ON kategori.dilid = dil.dilid
                    INNER JOIN seo ON sayfa.benzersizid = seo.benzersizid
            WHERE
                sayfa.sayfaid = :id
        ";

        $page = $this->db->select($sql, ['id' => $id]);

        if ($page) {
            $page = $page[0];
            $this->router->contentName = "Page";
            $this->router->contentUniqID = $page['benzersizid'];
            $this->router->languageID = $page['dilid'];
            $this->router->languageCode = mb_strtolower($page['dilkisa']);
            $this->router->seoTitle = $page['baslik'];
            $this->router->seoDescription = $page['aciklama'];
            $this->router->seoKeywords = $page['kelime'];
            $this->router->seoLink = $page['link'];
            $this->router->seoImage = $page['resim'];

            return true;
        }
        else{
            return false;
        }
    }

    public function checkCategoryByID($id)
    {
        $sql = "
            SELECT
               seo.benzersizid,seo.baslik,seo.aciklama,seo.kelime,seo.link,seo.resim,
               dil.dilid,dil.dilkisa
            FROM
                kategori
                    INNER JOIN dil ON kategori.dilid = dil.dilid
                    INNER JOIN seo ON kategori.benzersizid = seo.benzersizid
            WHERE
                kategori.kategoriid = :id
        ";

        $category = $this->db->select($sql, ['id' => $id]);

        if($category){
            $category = $category[0];
            $this->router->contentName = "Category";
            $this->router->contentUniqID = $category['benzersizid'];
            $this->router->languageID = $category['dilid'];
            $this->router->languageCode = mb_strtolower($category['dilkisa']);
            $this->router->seoTitle = $category['baslik'];
            $this->router->seoDescription = $category['aciklama'];
            $this->router->seoKeywords = $category['kelime'];
            $this->router->seoLink = $category['link'];
            $this->router->seoImage = $category['resim'];
            return true;
        }
        else{
            return false;
        }
    }

    public function checkHomePageByID($id)
    {
        $sql = "
            SELECT 
                dil.dilid AS languageID,
                dil.dilkisa AS languageCode,
                kategori.benzersizid AS category_uniqueid,
                seo.baslik AS seoTitle,
                seo.aciklama AS seoDescription,
                seo.link AS seoLink,
                seo.kelime AS seoKeywords,
                seo.resim AS seoImage
            FROM dil
                INNER JOIN kategori ON dil.dilid = kategori.dilid
                    INNER JOIN seo ON kategori.benzersizid = seo.benzersizid
            WHERE dil.dilid = :id AND kategori.anasayfa = 1
        ";

        $homepage = $this->db->select($sql, ['id' => $id]);

        if($homepage){
            $homepage = $homepage[0];
            $this->router->contentName = "HomePage";
            $this->router->contentUniqID = $homepage['category_uniqueid'];
            $this->router->languageID = $homepage['languageID'];
            $this->router->languageCode = mb_strtolower($homepage['languageCode']);
            $this->router->seoTitle = $homepage['seoTitle'];
            $this->router->seoDescription = $homepage['seoDescription'];
            $this->router->seoLink = $homepage['seoLink'];
            $this->router->seoKeywords = $homepage['seoKeywords'];
            $this->router->seoImage = $homepage['seoImage'];
            return true;
        }
        else{
            return false;
        }
    }

    public function getRouter()
    {
        return $this->router;
    }
}