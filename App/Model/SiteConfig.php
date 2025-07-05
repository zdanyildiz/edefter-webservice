<?php
include_once MODEL .'Language.php';
include_once MODEL . 'BannerModel.php';
class SiteConfig
{
    private Database $db;
    public int $languageId=1;
    public string $languageCode="tr";
    public array $siteConfig = [];
    public int $siteConfigVersion = 0;

    public function __construct($db,$languageId)
    {
        $this->db = $db;
        $this->languageId = $languageId;
        $languageModel = new Language($this->db, "", $languageId);
        $this->languageCode = $languageModel->getLanguageCode($languageId);
        $this->siteConfigVersion = $this->getSiteConfigVersion();

    }

    public function createSiteConfig()
    {
        if($this->siteConfigVersion == 0){
            $this->addSiteConfigVersion();
        }
        else{
            $this->updateSiteConfigVersion();
        }

        $this->siteConfigVersion = $this->siteConfigVersion + 1;

        $this->siteConfig = [
            'siteConfigVersion' => $this->siteConfigVersion,
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
            'tagManager' => $this->getTagManager(),
            'siteSettings' => $this->getSiteSettings()
        ];
    }

    public function getSiteConfigVersion()
    {
        $languageId = $this->languageId;
        $sql = "Select * From site_config_versions where language_id = :languageId";
        $data = $this->db->select($sql, ['languageId' => $languageId]);
        if (empty($data)) {
            return 0;
        }
        return $data[0]['version'];
    }

    public function addSiteConfigVersion(){
        $languageId = $this->languageId;
        $version = 1;
        $sql = "Insert into site_config_versions (language_id, version) values (:languageId, :version)";
        return $this->db->insert($sql, ['languageId' => $languageId, 'version' => $version]);
    }

    public function updateSiteConfigVersion(){
        $languageId = $this->languageId;
        $sql = "UPDATE site_config_versions SET version = version + 1 WHERE language_id = :languageId";
        return $this->db->update($sql, ['languageId' => $languageId]);
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
        $sql = "
            SELECT 
                * 
            FROM 
                payment_providers 
                    INNER JOIN payment_provider_settings
                        ON payment_provider_settings.payment_provider_id = payment_providers.id
            WHERE 
                status=1 AND languageCode = :languageCode";
        return $this->db->select($sql, ['languageCode' => $this->languageCode]);
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
        $bannerTypeModel = new BannerTypeModel($this->db);
        $bannerLayoutModel = new BannerLayoutModel($this->db);
        $bannerGroupModel = new BannerGroupModel($this->db);
        $bannerStyleModel = new BannerStyleModel($this->db);
        $bannerDisplayRulesModel = new BannerDisplayRulesModel($this->db);
        $bannerModel = new BannerModel($this->db);

        $result = [];
        $bannerDisplayRules = $bannerDisplayRulesModel->getDisplayRulesByLanguageCode($this->languageCode);
        if(empty($bannerDisplayRules)){return [];}
        foreach ($bannerDisplayRules as $displayRule) {
            /*
                id
                group_id
                type_id
                page_id
                category_id
                language_code
                created_at
                updated_at
             */
            $grupId = $displayRule['group_id'];
            $bannerGroup = $bannerGroupModel->getGroupById($grupId);
            //Log::write("Banner Group: ". json_encode($bannerGroup));
            if (empty($bannerGroup)) {
                return [];
            }
            $bannerGroup = $bannerGroup[0];

            /*
                id
                group_name
                layout_id
                columns
                content_alignment
                style_class
                custom_css
                order_num
                visibility_start
                visibility_end
                banner_duration
                created_at
                updated_at
             */

            $layoutId = $bannerGroup['layout_id'];
            $layoutInfo = $bannerLayoutModel->getLayoutById($layoutId);
            if (empty($layoutInfo)) {
                return [];
            }
            $layoutInfo = $layoutInfo[0];

            $visibilityEnd = isset($bannerGroup['visibility_end']) ? $bannerGroup['visibility_end'] : null;

            if ($visibilityEnd) {
                $endDate = new DateTime($visibilityEnd);
                $currentDate = new DateTime();
                $endDate->setTime(23, 59, 59);

                if ($currentDate > $endDate) {
                    continue; // Süresi dolmuş banner'ları atla
                }
            }

            $banners = $bannerModel->getBannersByGroupId($grupId);
            if (empty($banners)) {
                continue;
            }

            $bannerData = [];

            foreach ($banners as $banner) {
                if (!isset($banner['active']) || $banner['active'] != 1) {
                    continue; // Aktif olmayan banner'ları atla
                }

                $styleInfo = [];

                if (isset($banner['style_id'])) {
                    $style = $bannerStyleModel->getStyleById($banner['style_id']);
                    if (!empty($style)) {
                        $styleInfo = $style[0];
                    }
                }

                $bannerData[] = [
                    'id' => $banner['id'],
                    'title' => $banner['title'] ?? '',
                    'content' => $banner['content'] ?? '',
                    'image' => $banner['image'] ?? '',
                    'link' => $banner['link'] ?? '',
                    'style' => $styleInfo
                ];
            }

            // Tüm bilgileri bir araya getir
            $bannerType = $bannerTypeModel->getTypeById($displayRule['type_id']);

            $result[] = [
                'type_id' => $displayRule['type_id'],
                'type_name' => !empty($bannerType) ? $bannerType[0]['type_name'] : '',
                'page_id' => $displayRule['page_id'],
                'category_id' => $displayRule['category_id'],
                'group_info' => [
                    'id' => $bannerGroup['id'],
                    'name' => $bannerGroup['group_name'],
                    'group_title' => $bannerGroup['group_title'],
                    'group_desc' => $bannerGroup['group_desc'],
                    'group_view' => $bannerGroup['group_view'],
                    'group_kind' => $bannerGroup['group_kind'],
                    'columns' => $bannerGroup['columns'],
                    'content_alignment' => $bannerGroup['content_alignment'],
                    'style_class' => $bannerGroup['style_class'] ?? '',
                    'background_color' => $bannerGroup['background_color'] ?? '',
                    'group_title_color' => $bannerGroup['group_title_color'] ?? '',
                    'group_desc_color' => $bannerGroup['group_desc_color'] ?? '',
                    'group_full_size' => $bannerGroup['group_full_size'] ?? '',
                    'custom_css' => $bannerGroup['custom_css'] ?? '',
                    'order_num' => $bannerGroup['order_num'],
                    'visibility_start' => $bannerGroup['visibility_start'] ?? null,
                    'visibility_end' => $bannerGroup['visibility_end'] ?? null,
                    'banner_duration' => $bannerGroup['banner_duration'] ?? null,
                    'banner_full_size' => $bannerGroup['banner_full_size'] ?? null
                ],
                'layout_info' => [
                    'id' => $layoutInfo['id'],
                    'name' => $layoutInfo['layout_name'],
                    'layout_group' => $layoutInfo['layout_group'] ?? '',
                    'layout_view' => $layoutInfo['layout_view'] ?? '',
                    'columns' => $layoutInfo['columns'],
                    'max_banners' => $layoutInfo['max_banners']
                ],
                'banners' => $bannerData
            ];
        }

        // Sıralama numarasına göre sırala
        usort($result, function($a, $b) {
            return ($a['group_info']['order_num'] ?? 0) - ($b['group_info']['order_num'] ?? 0);
        });

        return $result;
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
            //Log::write("Kurlar güncellenecek","info");

            try {
                $url = 'https://www.tcmb.gov.tr/kurlar/today.xml';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT,3);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);

                $xml_string = curl_exec($ch);
                $curl_error_no = curl_errno($ch);
                $curl_error = curl_error($ch);

                $now = date('Y-m-d H:i:s'); // Current date and time

                if ($curl_error_no === 0 && !empty($xml_string)) {
                    $connect_web = @simplexml_load_string($xml_string);
                    if($connect_web === false){
                        Log::write("TCMB XML ayrıştırılamadı. Gelen string: ". substr($xml_string, 0, 1000));
                        $this->db->beginTransaction();

                        $result = $this->db->update("UPDATE urunparabirim SET parabirimkurtarih = ? WHERE parabirimid IN ('2', '3')", [$now]);

                        if (!$result) {
                            $this->db->rollback();
                            Log::write('Error while updating currency rates', 'error');
                        }
                        else {
                            $this->db->commit();
                        }
                    }
                    else {
                        if (isset($connect_web->Currency[0]->BanknoteSelling)) {

                            $this->db->beginTransaction();

                            $usd_selling = str_replace(",", ".", $connect_web->Currency[0]->BanknoteSelling);
                            $usd_selling = number_format($usd_selling, 2, '.', '');

                            $result = $this->db->update("UPDATE urunparabirim SET parabirimkur = ?, parabirimkurtarih = ? WHERE parabirimid = '2'", [$usd_selling, $now]);

                            if (!$result) {
                                $this->db->rollback();
                                Log::write('Error while updating currency rates', 'error');
                            } else {
                                $this->db->commit();
                            }
                        }

                        if (isset($connect_web->Currency[3]->BanknoteSelling)) {

                            $this->db->beginTransaction();

                            $euro_selling = str_replace(",", ".", $connect_web->Currency[3]->BanknoteSelling);
                            $euro_selling = number_format($euro_selling, 2, '.', '');

                            $result = $this->db->update("UPDATE urunparabirim SET parabirimkur = ?, parabirimkurtarih = ? WHERE parabirimid = '3'", [$euro_selling, $now]);

                            if (!$result) {
                                $this->db->rollback();
                                Log::write('Error while updating currency rates', 'error');
                            } else {
                                $this->db->commit();
                            }
                        }

                        $siteConfigVersion = $this->siteConfigVersion;
                        if ($siteConfigVersion == 0) {
                            $this->db->beginTransaction();
                            $this->addSiteConfigVersion();
                            $this->db->commit();
                        }
                        else {
                            $this->db->beginTransaction();
                            $this->updateSiteConfigVersion();
                            $this->db->commit();
                        }

                        $this->siteConfigVersion = $this->siteConfigVersion + 1;
                    }
                }
                else{
                    Log::write("TCMB Kurları güncellenemedi: $curl_error","error");
                }
            } catch (Exception $e) {
                Log::write('Error while updating currency rates: ' . $e->getMessage(),'error');
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

    public function getSiteSettings()
    {
        $sql = "SELECT * FROM site_settings WHERE language_id = :language_id";
        return $this->db->select($sql, ['language_id' => $this->languageId]);
    }
}