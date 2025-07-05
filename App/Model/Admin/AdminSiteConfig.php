<?php

class AdminSiteConfig
{
    private AdminDatabase $db;
    public int $languageId=1;
    public array $siteConfig;

    public function __construct($db,$languageId)
    {
        $this->db = $db;
        $this->languageId = $languageId;

        $this->siteConfig = [
            'generalSettings' => $this->getGeneralSettings(),
            'logoSettings' => $this->getLogoSettings(),
            'socialMediaSettings' => $this->getSocialMediaSettings(),
            'bankSettings' => $this->getBankSettings(),
            'companySettings' => $this->getCompanySettings(),
            'priceSettings' => $this->getPriceSettings(),
            'eftInfo' => $this->getEftInfo(),
            'bannerInfo' => $this->getBannerInfo(),
            'specificPageLinks' => $this->getSpecificPageLinks(),
            'currencyRates' => $this->updateCurrencyRates(),
            'analysisCodes' => $this->getAnalysisCodes(),
            'adConversionCode' => $this->getAdConversionCode(),
            'salesConversionCodeSettings' => $this->getSalesConversionCodeSettings(),
            'cartConversionCode' => $this->getCartConversionCode(),
            'tagManager' => $this->getTagManager()
        ];
    }

    public function getSiteConfig() {
        return $this->siteConfig;
    }

    public function getGeneralSettings()
    {
        $sql = "SELECT * FROM ayargenel WHERE dilid = :languageId";
        $data = $this->db->select($sql, ['languageId' => $this->languageId]);
        if (empty($data)) {
            return [];
        }
        return $data[0];
    }

    public function getLogoSettings()
    {
        $sql = "
            SELECT 
                ayarlogo.*, CONCAT(resimklasor.resimklasorad, '/', resim.resim) as resim_url
            FROM 
                ayarlogo
                    LEFT JOIN resim ON ayarlogo.resimid = resim.resimid
                        LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
            WHERE 
                ayarlogo.dilid = :languageId";

        $logo = $this->db->select($sql, ['languageId' => $this->languageId]);

        if (empty($logo)) {
            $logo = ['resim_url' => '/Theme/logo.png', 'logoyazi' => 'Pozitif Eticaret'];
        }
        else{
            $logo = $logo[0];
        }


        $sql = "
            SELECT 
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as resim_url
            FROM
                resim
                INNER JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
            WHERE
                benzersizid = '12345678901234567890'
        ";

        $favIcon = $this->db->select($sql);

        if (empty($favIcon)) {
            $favIcon = '/Theme/logo.png';
        }
        else{
            $favIcon = $favIcon[0]['resim_url'];
        }
        $logo['favIcon'] = $favIcon;

        return $logo;
    }

    public function getSocialMediaSettings()
    {
        $sql = "SELECT * FROM ayarsosyalmedya WHERE dilid = :languageId";
        $data = $this->db->select($sql, ['languageId' => $this->languageId]);
        if (empty($data)) {
            return [];
        }
        return $data[0];
    }

    public function getBankSettings()
    {
        $sql = "SELECT * FROM ayarbanka WHERE ayarbankaaktif=1 AND dilid = :languageId";
        return $this->db->select($sql, ['languageId' => $this->languageId]);
    }

    public function getCompanySettings()
    {
        $location = new Location($this->db);

        $sql = "SELECT * FROM ayarfirma WHERE dilid = :languageId";

        $result = $this->db->select($sql, ['languageId' => $this->languageId]);

        if (empty($result)) {
            return [];
        }
        $result = $result[0];

        $result['ayarfirmaulke'] = $location->getCountryNameById($result['ayarfirmaulke']);

        $result['ayarfirmasehir'] = $location->getCityNameById($result['ayarfirmasehir']);

        $result['ayarfirmailce'] = $location->getCountyNameById($result['ayarfirmailce']);

        $result['ayarfirmasemt'] = $location->getAreaNameById($result['ayarfirmasemt']);

        $result['ayarfirmamahalle'] = $location->getNeighborhoodNameById($result['ayarfirmamahalle']);

        return $result;
    }

    public function getPriceSettings()
    {
        $sql = "SELECT * FROM ayarfiyat WHERE dilid = :languageId";
        return $this->db->select($sql, ['languageId' => $this->languageId]);
    }

    public function getEftInfo()
    {
        $sql = "SELECT * FROM bankaeft WHERE bankaeftsil=0 AND dilid = :languageId";
        return $this->db->select($sql, ['languageId' => $this->languageId]);
    }

    public function getBannerInfo()
    {
        return [];
    }

    public function getSpecificPageLinks(){
        $sql = "
        SELECT 	
            link,sayfatip
        FROM 
            seo 
            INNER JOIN sayfa ON 
                sayfa.benzersizid=seo.benzersizid
                INNER JOIN sayfalistekategori ON 
                    sayfalistekategori.sayfaid=sayfa.sayfaid
                INNER JOIN kategori ON 
                    kategori.kategoriid=sayfalistekategori.kategoriid
        WHERE
            (sayfasil=0 and sayfaaktif='1') and 
            (sayfatip='1' OR sayfatip='8' OR sayfatip='9' OR sayfatip='10' OR sayfatip='11' OR sayfatip='12' OR 
             sayfatip='13' OR sayfatip='14' OR sayfatip='15' OR sayfatip='17' OR sayfatip='18' OR sayfatip='19' OR sayfatip='21' OR sayfatip='22') AND dilid= :languageId
        ";
        return $this->db->select($sql, ['languageId' => $this->languageId]);

    }
    public function updateCurrencyRates() {
        // Get the last update date from the database
        $lastUpdateDate = $this->db->select("SELECT MAX(parabirimkurtarih) as lastUpdate FROM urunparabirim WHERE parabirimid IN ('2', '3')")[0]['lastUpdate'];

        // Check if the last update was today
        if (date('Y-m-d') != date('Y-m-d', strtotime($lastUpdateDate))) {


            try {
                $connect_web = @simplexml_load_file('http://www.tcmb.gov.tr/kurlar/today.xml');
                $now = date('Y-m-d H:i:s'); // Current date and time

                if ($connect_web) {
                    if(isset($connect_web->Currency[0]->BanknoteSelling)){

                        $this->db->beginTransaction();

                        $usd_selling = str_replace(",", ".", $connect_web->Currency[0]->BanknoteSelling);
                        $usd_selling = number_format($usd_selling, 2, '.', '');

                        $result = $this->db->update("UPDATE urunparabirim SET parabirimkur = ?, parabirimkurtarih = ? WHERE parabirimid = '2'", [$usd_selling, $now]);

                        if (!$result) {
                            $this->db->rollback();
                            Log::adminWrite('Error while updating currency rates','error');
                        }
                        else {
                            $this->db->commit();
                        }
                    }

                    if(isset($connect_web->Currency[3]->BanknoteSelling)){

                        $this->db->beginTransaction();

                        $euro_selling = str_replace(",", ".", $connect_web->Currency[3]->BanknoteSelling);
                        $euro_selling = number_format($euro_selling, 2, '.', '');

                        $result = $this->db->update("UPDATE urunparabirim SET parabirimkur = ?, parabirimkurtarih = ? WHERE parabirimid = '3'", [$euro_selling, $now]);

                        if (!$result) {
                            $this->db->rollback();
                            Log::adminWrite('Error while updating currency rates','error');
                        }
                        else {
                            $this->db->commit();
                        }
                    }

                } else {
                    $this->db->beginTransaction();

                    $result = $this->db->update("UPDATE urunparabirim SET parabirimkurtarih = ? WHERE parabirimid IN ('2', '3')", [$now]);

                    if (!$result) {
                        $this->db->rollback();
                        Log::adminWrite('Error while updating currency rates','error');
                    }
                    else {
                        $this->db->commit();
                    }
                }

            } catch (Exception $e) {
                Log::adminWrite('Error while updating currency rates: ' . $e->getMessage(),'error');
            }
        }

        // Get the latest currency rates from the database
        $usd_rate = $this->db->select("SELECT parabirimkur FROM urunparabirim WHERE parabirimid = '2'")[0]['parabirimkur'];
        $euro_rate = $this->db->select("SELECT parabirimkur FROM urunparabirim WHERE parabirimid = '3'")[0]['parabirimkur'];

        // Return the latest currency rates
        return ['usd' => $usd_rate, 'euro' => $euro_rate];
    }

    public function getAnalysisCodes()
    {
        $sql = "SELECT * FROM ayaranaliz WHERE ayaranalizsil=0 AND dilid = :languageId";
        return $this->db->select($sql, ['languageId' => $this->languageId]);
    }

    public function getSalesConversionCodeSettings()
    {
        $sql = "SELECT * FROM ayarsatisdonusumkodu WHERE dilid = :languageId";
        return $this->db->select($sql, ['languageId' => $this->languageId]);
    }

    public function getAdConversionCode(){
        $sql = "SELECT * FROM ad_conversion_code WHERE ad_conversion_code_deleted = 0 AND language_id = :languageID";
        return $this->db->select($sql, [':languageID' => $this->languageId]);
    }

    public function getCartConversionCode(){
        $sql = "SELECT * FROM cart_conversion_code WHERE cart_conversion_code_deleted = 0 AND language_id = :languageID";
        return $this->db->select($sql, [':languageID' => $this->languageId]);
    }

    public function getTagManager(){
        $sql = "SELECT * FROM tag_manager WHERE tag_manager_deleted = 0 AND language_id = :languageID";
        return $this->db->select($sql, [':languageID' => $this->languageId]);
    }
}