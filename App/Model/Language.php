<?php
class Language
{
    private $db;


    public function __construct(Database $db,$langCode,$langID=null)
    {
        $this->db = $db;
        if(empty($langCode)){

            if(!empty($langID)){

                $langCode = $this->getLanguageCode($langID);
            }
            else{

                $langCode = "tr";
            }
        }
    }

    // Get all translations for given language code
    public function getTranslations($langCode)
    {
        try {
            $langCode = strtolower($langCode);
            $sql = "
                SELECT 
                    dilsabitleri.sabitisim,
                    COALESCE(dilbirlestir.dilsabitdeger, dilsabitleri.sabitdeger) as translation
                FROM
                    dilsabitleri
                    LEFT JOIN dilbirlestir ON dilsabitleri.dilsabitid = dilbirlestir.dilsabitid AND dilbirlestir.dilkisa = :langCode;
            ";

            //check json language file

            $jsonFile = PUBL.'Json/Language/translations-'.$langCode.'.json';

            if(!file_exists($jsonFile)){

                // Fetch all the translations
                $translations = $this->db->select($sql, ['langCode' => $langCode]);

                //klasörler yoksa oluştur
                if(!is_dir(PUBL.'Json/Language/')){
                    mkdir(PUBL.'Json/Language/', 0755, true);
                }

                // Write the array to a JSON file
                file_put_contents(PUBL.'Json/Language/translations-'.$langCode.'.json', json_encode($translations));
            }

            // JSON dosyasını oku
            $json = file_get_contents(PUBL.'Json/Language/translations-'.$langCode.'.json');


            // JSON verisini bir diziye dönüştür
            $translations = json_decode($json, true);
            // Define constants for all translations and add them to an array


            foreach($translations as $key => $value) {
                if (!defined($value['sabitisim'])) {
                    define($value['sabitisim'], $value['translation']);
                }
            }

        }
        catch (\PDOException $e) {

            Log::write($e->getMessage(), "error");
        }
    }

    public function getLanguageID($langCode)
    {
        $sql = "
            SELECT 
                dilid
            FROM
                dil
            WHERE
                dilkisa = :langCode;
        ";

        $result = $this->db->select($sql, ['langCode' => $langCode]);
        if(!empty($result)){
            return $result[0]['dilid'];
        }
        return null;
    }

    public function getLanguageCode($langID)
    {
        $sql = "
            SELECT 
                dilkisa
            FROM
                dil
            WHERE
                dilid = :langID;
        ";

        $result = $this->db->select($sql, ['langID' => $langID]);
        if(!empty($result)){
            return $result[0]['dilkisa'];
        }
        return null;
    }

    public function getLanguages()
    {
        $sql = "
            SELECT 
                *
            FROM
                dil
            WHERE
                dilaktif = 1 and dilsil = 0
        ";

        return $this->db->select($sql);
    }

    public function getMainLanguages()
    {
        $sql = "
            SELECT 
                *
            FROM
                dil
            WHERE
                dilaktif = 1 and dilsil = 0 and anadil = 1
        ";
        return $this->db->select($sql);
    }

    public function getLanguagesWithLink()
    {
        $sql = "
            SELECT 
                dil.*,seo.link
            FROM
                dil
                inner join kategori on kategori.dilid = dil.dilid
                inner join seo on kategori.benzersizid = seo.benzersizid
            WHERE
                dilaktif = 1 and dilsil = 0 and anasayfa=1
            Group By dil.dilid    
        ";

        return $this->db->select($sql);
    }
}
