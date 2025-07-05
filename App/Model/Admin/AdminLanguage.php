<?php

/**
 * Table: dilsabitleri
 * Columns:
 * dilsabitid int AI PK
 * sabitisim varchar(255)
 * sabitdeger text
 * dilsabitgrup varchar(255)
 */
//dil sabitleri ana dilin sabitlerini tutar.
//örn: 2,_header_eposta_title,E-Posta Gönder,header

/**
 * Table: dilbirlestir
 * Columns:
 * dilbirlestirid int AI PK
 * dilkisa varchar(5)
 * dilsabitid int
 * dilsabitdeger text
 */
//dilbirlestir tablosu dilsabitleri tablosundaki sabitlerin dillerine göre çevirilerini tutar.
class AdminLanguage
{
    private $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

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

    public function getLanguages()
    {
        $query = "
            SELECT 
            dilid as languageID,
            dilad as languageName,
            dilkisa as languageCode,
            anadil as isMainLanguage,
            dilaktif as isActive,
            dilsira as languageOrder
            FROM 
                dil 
            WHERE 
                dilsil=0 and dilaktif=1
            ORDER BY 
                dilid
        ";

        $result = $this->db->select($query);

        if ($result) {
            return $result;
        }
        return [];
    }

    public function getLanguage($languageID)
    {
        $query = "
            SELECT 
                dilid as languageID,
                dilad as languageName,
                dilkisa as languageCode,
                anadil as isMainLanguage,
                dilaktif as isActive
            FROM 
                dil 
            WHERE 
                dilid = :languageID
        ";

        $params = array(
            'languageID' => $languageID
        );

        $result = $this->db->select($query, $params);

        if ($result) {
            return $result[0];
        }

        return [];
    }

    public function getLanguageNameByLanguageCode($languageCode)
    {
        $query = "SELECT dilad FROM dil WHERE dilkisa = :languageCode";
        $params = array(
            'languageCode' => $languageCode
        );
        $result = $this->db->select($query, $params);

        if ($result) {
            return $result[0]['dilad'];
        }

        return '';

    }

    public function getLanguageCode($languageID)
    {
        $query = "SELECT dilkisa FROM dil WHERE dilid = :languageID";
        $params = array(
            'languageID' => $languageID
        );
        $result = $this->db->select($query, $params);

        if ($result) {
            return $result[0]['dilkisa'];
        }
        return "";

    }

    public function isMainLanguage($languageCode)
    {
        $query = "SELECT anadil FROM dil WHERE anadil=1 AND dilkisa = :languageCode";
        $params = array(
            'languageCode' => $languageCode
        );
        $result = $this->db->select($query, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result[0]['anadil']
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Language not found'
            ];
        }
    }

    public function getAllLanguages()
    {
        $query = "SELECT diller FROM dillistesi";
        $result = $this->db->select($query);

        if ($result) {
            return $result[0]['diller'];
        }
        return "";
    }

    public function addLanguage($languageData)
    {
        $sql = "
            INSERT 
                INTO dil (ID ,olusturmatarihi, guncellemetarihi, dilad, dilkisa, anadil, dilsira, dilaktif, dilsil) 
                VALUES (:languageUniqID, :languageAddDate, :languageUpdateDate, :languageName, :languageCode, :isMainLanguage, 0, :isActive, 0)
        ";

        $params = array(
            'languageUniqID' => $languageData['languageUniqID'],
            'languageAddDate' => $languageData['languageAddDate'],
            'languageUpdateDate' => $languageData['languageUpdateDate'],
            'languageName' => $languageData['languageName'],
            'languageCode' => $languageData['languageCode'],
            'isMainLanguage' => $languageData['isMainLanguage'],
            'isActive' => $languageData['isActive']
        );


        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Dil Eklendi',
                'languageID' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Dil eklenemedi'
        ];
    }

    public function updateLanguage($languageData)
    {
        $sql = "
            UPDATE dil 
            SET 
                dilad = :languageName,
                dilkisa = :languageCode,
                anadil = :isMainLanguage,
                dilaktif = :isActive,
                guncellemetarihi = :languageUpdateDate
            WHERE 
                dilid = :languageID
        ";

        $params = array(
            'languageID' => $languageData['languageID'],
            'languageName' => $languageData['languageName'],
            'languageCode' => $languageData['languageCode'],
            'isMainLanguage' => $languageData['isMainLanguage'],
            'isActive' => $languageData['isActive'],
            'languageUpdateDate' => $languageData['languageUpdateDate']
        );

        //$this->db->beginTransaction();

        $result = $this->db->update($sql, $params);

        if ($result > 0) {
            //$this->db->commit();
            return [
                'status' => 'success',
                'message' => 'Dil Güncellendi'
            ];
        } elseif ($result == 0) {
            return [
                'status' => 'success',
                'message' => 'Dil zaten güncel'
            ];
        }

        //$this->db->rollBack();

        return [
            'status' => 'error',
            'message' => 'Dil güncellenemedi'
        ];
    }

    public function deleteLanguage($languageID)
    {
        $sql = "UPDATE dil SET dilsil = 1 WHERE dilid = :languageID";
        $params = array(
            'languageID' => $languageID
        );

        $this->db->beginTransaction();

        $result = $this->db->update($sql, $params);

        if ($result) {
            $this->db->commit();
            return [
                'status' => 'success',
                'message' => 'Dil silindi'
            ];
        }

        $this->db->rollBack();

        return [
            'status' => 'error',
            'message' => 'Dil silinemedi'
        ];
    }

    public function checkLanguage($languageCode)
    {
        $sql = "SELECT dilid,dilaktif FROM dil WHERE dilsil=0 AND dilkisa = :languageCode";

        $params = array(
            'languageCode' => $languageCode
        );

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result[0]
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Language not found'
        ];
    }

    public function updateLanguageStatus($languageCode)
    {
        $sql = "UPDATE dil SET dilaktif = 1 WHERE dilkisa = :languageCode";

        $params = array(
            'languageCode' => $languageCode
        );

        //$this->db->beginTransaction();

        $result = $this->db->update($sql, $params);

        if ($result) {
            //$this->db->commit();
            return [
                'status' => 'success',
                'message' => 'Dil aktif edildi'
            ];
        }

        //$this->db->rollBack();

        return [
            'status' => 'error',
            'message' => 'Dil aktif edilemedi'
        ];
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
        if (!empty($result)) {
            return $result[0]['dilid'];
        }
        return null;
    }

    public function resetMainLanguage()
    {
        $sql = "UPDATE dil SET anadil = 0 WHERE anadil = 1";
        $result = $this->db->update($sql, []);
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Ana dil sıfırlandı'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Ana dil sıfırlanamadı'
        ];
    }

    public function beginTransaction($funcName = "")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName = "")
    {
        $this->db->commit($funcName);
    }

    public function rollBack($funcName = "")
    {
        $this->db->rollBack($funcName);
    }

    /**
     * Table: language_page_mapping
     * Columns:
     * id int AI PK
     * original_page_id int
     * translated_page_id int
     * dilid int
     * translation_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'
     * last_attempt_date DATETIME NULL
     * error_message TEXT NULL
     */
    
    public function addLanguagePageMapping($data)
    {
        $sql = "
            INSERT INTO language_page_mapping (original_page_id, translated_page_id, dilid, translation_status, last_attempt_date)
            VALUES (:originalPageID, :translatedPageID, :languageID, :translationStatus, NOW())
        ";

        $params = array(
            'originalPageID' => $data['originalPageID'],
            'translatedPageID' => $data['translatedPageID'] ?? null,
            'languageID' => $data['languageID'],
            'translationStatus' => $data['translationStatus'] ?? 'pending'
        );

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Sayfa çeviri kaydı eklendi',
                'mappingID' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Sayfa çeviri kaydı eklenemedi'
        ];
    }

    /**
     * Table: language_category_mapping
     * Columns:
     * id int AI PK
     * original_category_id int
     * translated_category_id int
     * dilid int
     * translation_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'
     * last_attempt_date DATETIME NULL
     * error_message TEXT NULL
     */
    
     public function addLanguageCategoryMapping($data)
    {
        $sql = "
            INSERT INTO language_category_mapping (original_category_id, translated_category_id, dilid, translation_status, last_attempt_date)
            VALUES (:originalCategoryID, :translatedCategoryID, :languageID, :translationStatus, NOW())
        ";

        $params = array(
            'originalCategoryID' => $data['originalCategoryID'],
            'translatedCategoryID' => $data['translatedCategoryID'] ?? null,
            'languageID' => $data['languageID'],
            'translationStatus' => $data['translationStatus'] ?? 'pending'
        );

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Kategori çeviri kaydı eklendi',
                'mappingID' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Kategori çeviri kaydı eklenemedi'
        ];
    }
    
    public function getPendingCategoryTranslations(int $limit = 5)
    {
        $sql = "
            SELECT 
                lcm.*,
                k_orig.kategoriad as original_title,
                k_trans.kategoriad as translated_title,
                d.dilad as language_name
            FROM language_category_mapping lcm
            LEFT JOIN kategori k_orig ON lcm.original_category_id = k_orig.kategoriid
            LEFT JOIN kategori k_trans ON lcm.translated_category_id = k_trans.kategoriid
            LEFT JOIN dil d ON lcm.dilid = d.dilid
            WHERE lcm.translation_status = 'pending' 
            ORDER BY lcm.last_attempt_date DESC
            LIMIT :limit
        ";
        return $this->db->select($sql, ['limit' => $limit]);
    }

    public function getPendingPageTranslations(int $limit = 5)
    {
        $sql = "
            SELECT 
                lpm.*,
                s_orig.sayfaad as original_title,
                s_trans.sayfaad as translated_title,
                d.dilad as language_name
            FROM language_page_mapping lpm
            LEFT JOIN sayfa s_orig ON lpm.original_page_id = s_orig.sayfaid
            LEFT JOIN sayfa s_trans ON lpm.translated_page_id = s_trans.sayfaid
            LEFT JOIN dil d ON lpm.dilid = d.dilid
            WHERE lpm.translation_status = 'pending' 
            ORDER BY lpm.last_attempt_date DESC
            LIMIT :limit
        ";
        return $this->db->select($sql, ['limit' => $limit]);
    }

    public function updateCategoryTranslationStatus(int $mappingId, string $status, ?string $errorMessage = null)
    {
        $sql = "
            UPDATE language_category_mapping 
            SET 
                translation_status = :status,
                last_attempt_date = :last_attempt_date,
                error_message = :error_message
            WHERE id = :id
        ";
        $params = [
            'id' => $mappingId,
            'status' => $status,
            'last_attempt_date' => date('Y-m-d H:i:s'),
            'error_message' => $errorMessage
        ];
        return $this->db->update($sql, $params);
    }

    public function updatePageTranslationStatus(int $mappingId, string $status, ?string $errorMessage = null)
    {
        $sql = "
            UPDATE language_page_mapping 
            SET 
                translation_status = :status,
                last_attempt_date = :last_attempt_date,
                error_message = :error_message
            WHERE id = :id
        ";
        $params = [
            'id' => $mappingId,
            'status' => $status,
            'last_attempt_date' => date('Y-m-d H:i:s'),
            'error_message' => $errorMessage
        ];
        return $this->db->update($sql, $params);
    }

    public function createCopyJob($data)
    {
        $sql = "
            INSERT INTO language_copy_jobs (source_language_id, target_language_id, translate_with_ai)
            VALUES (:source_language_id, :target_language_id, :translate_with_ai)
        ";

        $params = [
            'source_language_id' => $data['source_language_id'],
            'target_language_id' => $data['target_language_id'],
            'translate_with_ai'  => $data['translate_with_ai']
        ];

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return ['status' => 'success', 'message' => 'Kopyalama iş emri oluşturuldu.'];
        }

        return ['status' => 'error', 'message' => 'Kopyalama iş emri oluşturulamadı.'];
    }

    public function getPendingCopyJob()
    {
        $sql = "SELECT * FROM language_copy_jobs WHERE status = 'pending' ORDER BY id ASC LIMIT 1";
        $result = $this->db->select($sql);
        return $result ? $result[0] : null;
    }

    public function updateCopyJobStatus(int $jobId, string $status, ?string $errorMessage = null)
    {
        $sql = "
            UPDATE language_copy_jobs 
            SET 
                status = :status,
                error_message = :error_message
            WHERE id = :id
        ";
        $params = [
            'id' => $jobId,
            'status' => $status,
            'error_message' => $errorMessage
        ];
        return $this->db->update($sql, $params);
    }

    public function getTargetCategoryID($targetLangID,$sourceCategoryID)
    {
        $sql = "Select translated_category_id From language_category_mapping where dilid = :languageId and original_category_id = :sourceCategoryId";
        return $this->db->select($sql,['languageId'=>$targetLangID,'sourceCategoryId'=>$sourceCategoryID]);
    }

    public function getTargetPageID($targetLangID,$sourcePageID)
    {
        $sql = "Select translated_page_id From language_page_mapping where dilid = :languageId and original_page_id = :sourcePageId";
        return $this->db->select($sql,['languageId'=>$targetLangID,'sourcePageId'=>$sourcePageID]);
    }

    //dile göre dilsabitlerini getirelim
    public function getLanguageConstants($constantGroup = null)
    {
        $query = "
            SELECT
                dilsabitid as constantID,
                sabitisim as constantName,
                sabitdeger as constantValue,
                dilsabitgrup as constantGroup
            FROM
                dilsabitleri
        ";

        $params = array();

        if ($constantGroup !== null) {
            $query .= " WHERE dilsabitgrup = :constantGroup";
            $params['constantGroup'] = $constantGroup;
        }

        $result = $this->db->select($query, $params);

        if ($result) {
            return $result;
        }

        return [];
    }

    //dilbirlestir tablosundan dil koduna (en,ru) göre dilsabitlerinin çevirileri getirelim. İlgili dilde sabitin karşılığı yoksa ana dil sabiti getirilecek.
    public function getLanguageConstantTranslations($languageCode, $constantGroup = null)
    {
        $query = "
            SELECT
                ds.dilsabitid as constantID,
                ds.sabitisim as constantName,
                ds.sabitdeger as constantValue,
                COALESCE(db.dilbirlestirid, 0) as translationID,
                COALESCE(db.dilkisa, :languageCode1) as languageCode,
                COALESCE(db.dilsabitdeger, ds.sabitdeger) as translationValue,
                ds.dilsabitgrup as constantGroup
            FROM
                dilsabitleri ds
            LEFT JOIN
                dilbirlestir db ON ds.dilsabitid = db.dilsabitid AND db.dilkisa = :languageCode2
        ";

        $params = array(
            'languageCode1' => $languageCode,
            'languageCode2' => $languageCode
        );

        if ($constantGroup !== null) {
            $query .= " WHERE ds.dilsabitgrup = :constantGroup";
            $params['constantGroup'] = $constantGroup;
        }

        $result = $this->db->select($query, $params);

        if ($result) {
            return $result;
        }

        return [];
    }

    public function getLanguageConstantTranslationByID($translationID)
    {
        $query = "
            SELECT
                *
            FROM
                dilbirlestir
            
            WHERE
                dilbirlestirid = :translationID
        ";

        $params = array(
            'translationID' => $translationID
        );

        $result = $this->db->select($query, $params);

        if ($result) {
            return $result;
        }

        return [];
    }

    //yeni dil sabiti ekleme fonksiyonu yapalım
    public function addLanguageConstant($constantData)
    {
        $sql = "
            INSERT INTO dilsabitleri (sabitisim, sabitdeger, dilsabitgrup)
            VALUES (:constantName, :constantValue, :constantGroup)
        ";

        $params = array(
            'constantName' => $constantData['constantName'],
            'constantValue' => $constantData['constantValue'],
            'constantGroup' => $constantData['constantGroup']
        );

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Dil sabiti eklendi',
                'constantID' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Dil sabiti eklenemedi'
        ];
    }

    //dil sabiti güncelleme fonksiyonu yazalım
    public function updateLanguageConstant($constantData)
    {
        $sql = "
            UPDATE dilsabitleri
            SET
                sabitisim = :constantName,
                sabitdeger = :constantValue,
                dilsabitgrup = :constantGroup
            WHERE
                dilsabitid = :constantID
        ";

        $params = array(
            'constantID' => $constantData['constantID'],
            'constantName' => $constantData['constantName'],
            'constantValue' => $constantData['constantValue'],
            'constantGroup' => $constantData['constantGroup']
        );

        $result = $this->db->update($sql, $params);

        if ($result >= 0) {
            return [
                'status' => 'success',
                'message' => 'Dil sabiti güncellendi'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Dil sabiti güncellenemedi'
        ];
    }

    //dilbirlestir tablosuna yeni dil sabiti çevirisi ekleyelim
    public function addLanguageConstantTranslation($translationData)
    {
        $sql = "
            INSERT INTO dilbirlestir (dilkisa, dilsabitid, dilsabitdeger)
            VALUES (:languageCode, :constantID, :translationValue)
        ";

        $params = array(
            'languageCode' => $translationData['languageCode'],
            'constantID' => $translationData['constantID'],
            'translationValue' => $translationData['translationValue']
        );

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Dil sabiti çevirisi eklendi',
                'translationID' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Dil sabiti çevirisi eklenemedi'
        ];
    }

    //dilbirlestir tablosundaki dil sabiti çevirisini güncelleme fonksiyonu yazalım
    public function updateLanguageConstantTranslation($translationData)
    {
        $sql = "
            UPDATE dilbirlestir
            SET
                dilsabitdeger = :translationValue
            WHERE
                dilbirlestirid = :translationID
        ";

        $params = array(
            'translationID' => $translationData['translationID'],
            'translationValue' => $translationData['translationValue']
        );

        $result = $this->db->update($sql, $params);

        if ($result >= 0) {
            return [
                'status' => 'success',
                'message' => 'Dil sabiti çevirisi güncellendi'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Dil sabiti çevirisi güncellenemedi'
        ];
    }

    public function getLanguageConstantGroups()
    {
        $query = "
            SELECT 
                DISTINCT dilsabitgrup as constantGroup
            FROM dilsabitleri
            Order By constantGroup
        ";

        $result = $this->db->select($query);

        if ($result) {
            return $result;
        }

        return [];

    }

    public function checkConstant($constantGroup,$constantName){
        $sql = "
            SELECT 
                dilsabitid as constantID
            FROM
                dilsabitleri
            WHERE
                dilsabitgrup = :constantGroup
                AND sabitisim = :constantName
        ";

        $params = array(
            'constantGroup' => $constantGroup,
            'constantName' => $constantName
        );

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result[0]
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Constant not found'
        ];
    }

    public function deleteLanguageConstant($constantID)
    {
        $sql = "
            DELETE FROM 
                dilsabitleri
            WHERE 
                dilsabitid = :constantID
        ";
        $params = [
            'constantID' => $constantID
        ];
        return $this->db->delete($sql, $params);
    }

    /**
     * Belirli bir sayfa için çeviri durumunu getirir
     */
    public function getPageTranslationStatus($originalPageID, $languageID = null)
    {
        $sql = "
            SELECT 
                lpm.*,
                d.dilad as language_name,
                d.dilkisa as language_code,
                s_orig.sayfaad as original_title,
                s_trans.sayfaad as translated_title
            FROM language_page_mapping lpm
            LEFT JOIN dil d ON lpm.dilid = d.dilid
            LEFT JOIN sayfa s_orig ON lpm.original_page_id = s_orig.sayfaid
            LEFT JOIN sayfa s_trans ON lpm.translated_page_id = s_trans.sayfaid
            WHERE lpm.original_page_id = :originalPageID
        ";
        
        $params = ['originalPageID' => $originalPageID];
        
        if ($languageID) {
            $sql .= " AND lpm.dilid = :languageID";
            $params['languageID'] = $languageID;
        }
        
        $sql .= " ORDER BY lpm.dilid";
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Yeni çeviri işi oluşturur veya mevcut olanı günceller
     */
    public function createOrUpdatePageTranslationJob($originalPageId, $languageId, $status = 'pending')
    {
        // Önce mevcut kaydı kontrol et
        $existing = $this->getPageTranslationStatus($originalPageId, $languageId);
        
        if (!empty($existing)) {
            // Mevcut kayıt var, güncelle
            $result = $this->updatePageTranslationStatus($existing[0]['id'], $status);
            return [
                'status' => 'success',
                'message' => 'Çeviri işi güncellendi',
                'action' => 'updated',
                'mappingID' => $existing[0]['id']
            ];
        } else {
            // Yeni kayıt ekle
            $result = $this->addLanguagePageMapping([
                'originalPageID' => $originalPageId,
                'languageID' => $languageId,
                'translationStatus' => $status
            ]);
            
            if ($result['status'] === 'success') {
                $result['action'] = 'created';
            }
            
            return $result;
        }
    }

    /**
     * Ana dil ID'sini dinamik olarak getirir
     */
    public function getMainLanguageID()
    {
        $sql = "SELECT dilid FROM dil WHERE anadil = 1 LIMIT 1";
        $result = $this->db->select($sql);
        return !empty($result) ? $result[0]['dilid'] : 1;
    }

    /**
     * Çeviri için uygun sayfaları getirir (ana dildeki sayfalar)
     */
    public function getPagesForTranslation($languageId, $limit = 50)
    {
        $mainLanguageId = $this->getMainLanguageId();
        
        $sql = "
            SELECT 
                s.*,
                CASE 
                    WHEN lpm.id IS NOT NULL THEN lpm.translation_status
                    ELSE 'untranslated'
                END as translation_status,
                lpm.id as mapping_id,
                lpm.translated_page_id,
                lpm.last_attempt_date,
                lpm.error_message
            FROM sayfa s
            LEFT JOIN language_page_mapping lpm ON s.sayfaid = lpm.original_page_id AND lpm.dilid = :languageId
            WHERE s.dilid = :mainLanguageId 
            AND s.sayfasil = 0
            ORDER BY s.sayfaid DESC
            LIMIT :limit
        ";
        
        return $this->db->select($sql, [
            'languageId' => $languageId,
            'mainLanguageId' => $mainLanguageId,
            'limit' => $limit
        ]);
    }

    /**
     * Çeviri istatistikleri getirir
     */
    public function getTranslationStatistics($languageId = null)
    {
        $sql = "
            SELECT 
                d.dilid,
                d.dilad as language_name,
                d.dilkisa as language_code,
                COUNT(lpm.id) as total_translations,
                SUM(CASE WHEN lpm.translation_status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN lpm.translation_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN lpm.translation_status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM dil d
            LEFT JOIN language_page_mapping lpm ON d.dilid = lpm.dilid
            WHERE d.dilsil = 0 AND d.dilaktif = 1
        ";
        
        if ($languageId) {
            $sql .= " AND d.dilid = :languageId";
        }
        
        $sql .= " GROUP BY d.dilid ORDER BY d.dilid";
        
        $params = $languageId ? ['languageId' => $languageId] : [];
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Kategori ve alt kategorilerini hedef dile kopyalar
     * ContentCopier mantığını kullanarak kategori hiyerarşisini korur
     */
    public function copyAndTranslateCategory($originalCategoryID, $targetLanguageID, $translateWithAI = false)
    {
        try {
            // Helper ve diğer modelleri yükle
            include_once Helpers . 'Helper.php';
            $helper = new Helper();
            
            include_once MODEL . 'Admin/AdminCategory.php';
            $adminCategory = new AdminCategory($this->db);
            
            include_once MODEL . 'Admin/AdminSeo.php';
            $adminSeo = new AdminSeo($this->db);
            
            // Orijinal kategoriyi al
            $originalCategory = $adminCategory->getCategory($originalCategoryID);
            if (!$originalCategory) {
                return [
                    'status' => 'error',
                    'message' => 'Orijinal kategori bulunamadı'
                ];
            }
            
            // Zaten çevrilmiş mi kontrol et
            $existingMapping = $this->getCategoryMapping($originalCategoryID, $targetLanguageID);
            if ($existingMapping && $existingMapping['translated_category_id']) {
                return [
                    'status' => 'success',
                    'message' => 'Kategori zaten çevrilmiş',
                    'translatedCategoryID' => $existingMapping['translated_category_id']
                ];
            }
            
            // Yeni kategori verisini hazırla
            $newCategoryUniqID = $helper->generateUniqID();
            $translationStatus = $translateWithAI ? 'pending' : 'completed';
            
            $categoryData = [
                'languageID' => $targetLanguageID,
                'createDate' => date("Y-m-d H:i:s"),
                'updateDate' => date("Y-m-d H:i:s"),
                'topCategoryID' => $originalCategory['topCategoryID'], // Bu daha sonra üst kategori mapping'e göre güncellenecek
                'categoryLayer' => $originalCategory['categoryLayer'] ?? 0,
                'categoryName' => $originalCategory['categoryName'],
                'categoryImageID' => $originalCategory['categoryImageID'] ?? 0,
                'categoryContent' => $originalCategory['categoryContent'] ?? '',
                'categoryLink' => $originalCategory['categoryLink'] ?? '',
                'categoryOrder' => $originalCategory['categoryOrder'] ?? 0,
                'categorySorting' => $originalCategory['categorySorting'] ?? 0,
                'categoryType' => $originalCategory['categoryType'] ?? 0,
                'categoryHomePage' => $originalCategory['categoryHomePage'] ?? 0,
                'categoryActive' => $originalCategory['categoryActive'] ?? 1,
                'categoryDeleted' => 0,
                'categoryUniqID' => $newCategoryUniqID
            ];
            
            // Üst kategori varsa onun çevirisini bul
            if ($originalCategory['topCategoryID'] > 0) {
                $parentMapping = $this->getCategoryMapping($originalCategory['topCategoryID'], $targetLanguageID);
                if ($parentMapping && $parentMapping['translated_category_id']) {
                    $categoryData['topCategoryID'] = $parentMapping['translated_category_id'];
                } else {
                    // Üst kategori çevrilmemişse önce onu çevir (özyineleme)
                    $parentResult = $this->copyAndTranslateCategory($originalCategory['topCategoryID'], $targetLanguageID, $translateWithAI);
                    if ($parentResult['status'] === 'success') {
                        $categoryData['topCategoryID'] = $parentResult['translatedCategoryID'];
                    }
                }
            }
            
            // Transaction başlat
            $this->db->beginTransaction("copyCategory");
            
            // Kategoriyi kopyala
            $addCategoryResult = $adminCategory->insertCategory($categoryData);
            if ($addCategoryResult['status'] === 'error') {
                $this->db->rollback("copyCategory");
                return $addCategoryResult;
            }
            
            $newCategoryID = $addCategoryResult['categoryID'];
            
            // Kategori mapping'i ekle veya güncelle
            if ($existingMapping) {
                // Mevcut kaydı güncelle
                $this->updateCategoryMapping($existingMapping['id'], $newCategoryID, $translationStatus);
            } 
            else {
                // Yeni mapping ekle
                $mappingData = [
                    'originalCategoryID' => $originalCategoryID,
                    'translatedCategoryID' => $newCategoryID,
                    'languageID' => $targetLanguageID,
                    'translationStatus' => $translationStatus
                ];
                $this->addLanguageCategoryMapping($mappingData);
            }
              // SEO bilgisini kopyala
            $originalSeo = $adminSeo->getSeoByUniqId($originalCategory['categoryUniqID']);
            if ($originalSeo) {
                // Hedef dilin kısaltmasını al
                $targetLanguageCode = $this->getLanguageCode($targetLanguageID);
                
                $seoData = $originalSeo;
                $seoData['seoUniqID'] = $newCategoryUniqID;
                
                // SEO link'inde dil kısaltmasını değiştir
                if (!empty($seoData['seoLink']) && !empty($targetLanguageCode)) {
                    // Mevcut link'deki dil kısaltmasını bul ve değiştir
                    // Örnek: /tr/kategori-adi -> /en/kategori-adi
                    $seoData['seoLink'] = preg_replace('/^\/[a-z]{2}\//', "/{$targetLanguageCode}/", $seoData['seoLink']);
                    
                    // Eğer link dil kısaltması ile başlamıyorsa, başına ekle
                    if (!preg_match('/^\/[a-z]{2}\//', $seoData['seoLink'])) {
                        $seoData['seoLink'] = "/{$targetLanguageCode}" . $seoData['seoLink'];
                    }
                }
                
                $adminSeo->insertSeo($seoData);
            }
            
            $this->db->commit("copyCategory");
            
            return [
                'status' => 'success',
                'message' => 'Kategori başarıyla kopyalandı',
                'translatedCategoryID' => $newCategoryID,
                'originalCategoryID' => $originalCategoryID,
                'translationStatus' => $translationStatus
            ];
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback("copyCategory");
            }
            
            return [
                'status' => 'error',
                'message' => 'Kategori kopyalama hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Kategori mapping'i kontrol eder
     */
    public function getCategoryMapping($originalCategoryID, $languageID)
    {
        $sql = "SELECT * FROM language_category_mapping 
                WHERE original_category_id = :categoryID AND dilid = :languageID 
                LIMIT 1";
        
        $result = $this->db->select($sql, [
            'categoryID' => $originalCategoryID,
            'languageID' => $languageID
        ]);
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Kategori mapping'i günceller
     */
    public function updateCategoryMapping($mappingID, $translatedCategoryID, $translationStatus = 'completed')
    {
        $sql = "UPDATE language_category_mapping 
                SET translated_category_id = :translatedCategoryID, 
                    translation_status = :translationStatus,
                    last_attempt_date = NOW()
                WHERE id = :id";
        
        return $this->db->update($sql, [
            'id' => $mappingID,
            'translatedCategoryID' => $translatedCategoryID,
            'translationStatus' => $translationStatus
        ]);
    }
    
    /**
     * Sayfayı hedef dile kopyalar
     * ContentCopier mantığını kullanarak sayfa verilerini korur
     */
    public function copyAndTranslatePage($originalPageID, $targetLanguageID, $translateWithAI = false)
    {
        try {
            // Helper ve diğer modelleri yükle
            include_once Helpers . 'Helper.php';
            $helper = new Helper();
            
            include_once MODEL . 'Admin/AdminPage.php';
            $adminPage = new AdminPage($this->db);
            
            include_once MODEL . 'Admin/AdminSeo.php';
            $adminSeo = new AdminSeo($this->db);
            
            // Orijinal sayfayı al
            $originalPage = $adminPage->getPageById($originalPageID);
            if (!$originalPage) {
                return [
                    'status' => 'error',
                    'message' => 'Orijinal sayfa bulunamadı'
                ];
            }
            
            // Zaten çevrilmiş mi kontrol et
            $existingMapping = $this->getPageMapping($originalPageID, $targetLanguageID);
            if ($existingMapping && $existingMapping['translated_page_id']) {
                return [
                    'status' => 'success',
                    'message' => 'Sayfa zaten çevrilmiş',
                    'translatedPageID' => $existingMapping['translated_page_id']
                ];
            }
            
            // Yeni sayfa verisini hazırla
            $newPageUniqID = $helper->generateUniqID();
            $translationStatus = $translateWithAI ? 'pending' : 'completed';
            
            // Sayfa kategorilerini al
            $sql = "SELECT kategoriid FROM sayfalistekategori WHERE sayfaid = :pageID";
            $pageCategories = $this->db->select($sql, ['pageID' => $originalPageID]);
            
            if (empty($pageCategories)) {
                return [
                    'status' => 'error',
                    'message' => 'Sayfa kategorileri bulunamadı'
                ];
            }
            
            // İlk kategorinin çevrilmiş halini bul (sayfa-kategori ilişkisi için)
            $originalCategoryID = $pageCategories[0]['kategoriid'];
            $categoryMapping = $this->getCategoryMapping($originalCategoryID, $targetLanguageID);
            
            if (!$categoryMapping || !$categoryMapping['translated_category_id']) {
                return [
                    'status' => 'error',
                    'message' => 'Sayfa kategorisi önce çevrilmelidir'
                ];
            }
            
            $translatedCategoryID = $categoryMapping['translated_category_id'];
            
            // Hedef dilin dil kodunu al
            $targetLanguageCode = $this->getLanguageCode($targetLanguageID);
            
            // Sayfa verilerini hazırla
            $pageData = [
                'pageUniqID' => $newPageUniqID,
                'pageCreateDate' => date("Y-m-d H:i:s"),
                'pageUpdateDate' => date("Y-m-d H:i:s"),
                'pageType' => $originalPage['pageType'] ?? 23,
                'pageName' => $originalPage['pageName'] ?? '',
                'pageContent' => $originalPage['pageContent'] ?? '',
                'pageOrder' => $originalPage['pageOrder'] ?? 0,
                'pageLink' => $originalPage['pageLink'] ?? '',
                'pageActive' => $originalPage['pageActive'] ?? 1,
                'pageDeleted' => 0,
                'pageHit' => 0
            ];
            
            // Transaction başlat
            $this->db->beginTransaction("copyPage");
            
            // Sayfayı kopyala
            $addPageResult = $adminPage->insertPage($pageData);
            if ($addPageResult['status'] === 'error') {
                $this->db->rollback("copyPage");
                return $addPageResult;
            }
            
            $newPageID = $addPageResult['pageID'];
            
            // Sayfa-kategori ilişkisini ekle
            $pageCategoryData = [
                'pageID' => $newPageID,
                'categoryID' => $translatedCategoryID
            ];
            
            $addPageCategoryResult = $adminPage->insertPageCategory($pageCategoryData);
            if ($addPageCategoryResult['status'] === 'error') {
                $this->db->rollback("copyPage");
                return [
                    'status' => 'error',
                    'message' => 'Sayfa-kategori ilişkisi eklenemedi: ' . $addPageCategoryResult['message']
                ];
            }
            
            // Sayfa mapping'i ekle veya güncelle
            if ($existingMapping) {
                // Mevcut kaydı güncelle
                $this->updatePageMapping($existingMapping['id'], $newPageID, $translationStatus);
            } else {
                // Yeni mapping ekle
                $mappingData = [
                    'originalPageID' => $originalPageID,
                    'translatedPageID' => $newPageID,
                    'languageID' => $targetLanguageID,
                    'translationStatus' => $translationStatus
                ];
                $this->addLanguagePageMapping($mappingData);
            }
            
            // SEO bilgisini kopyala
            $originalSeo = $adminSeo->getSeoByUniqId($originalPage['benzersizid']);
            if ($originalSeo) {
                $seoData = $originalSeo;
                $seoData['seoUniqID'] = $newPageUniqID;
                
                // SEO URL'ini hedef dil koduna göre güncelle
                if (!empty($seoData['seoLink']) && !empty($targetLanguageCode)) {
                    // URL'deki dil kodunu değiştir (örn: /tr/kategori/sayfa -> /en/kategori/sayfa)
                    $seoLink = $seoData['seoLink'];
                    
                    // URL'in başında dil kodu var mı kontrol et
                    if (preg_match('/^\/([a-z]{2})\/(.*)$/', $seoLink, $matches)) {
                        $currentLangCode = $matches[1];
                        $restOfUrl = $matches[2];
                        
                        // Dil kodunu değiştir
                        $seoData['seoLink'] = '/' . strtolower($targetLanguageCode) . '/' . $restOfUrl;
                    } else {
                        // Dil kodu yoksa başına ekle
                        $seoData['seoLink'] = '/' . strtolower($targetLanguageCode) . $seoLink;
                    }
                }
                
                $adminSeo->insertSeo($seoData);
            }
            
            $this->db->commit("copyPage");
            
            return [
                'status' => 'success',
                'message' => 'Sayfa başarıyla kopyalandı',
                'translatedPageID' => $newPageID,
                'originalPageID' => $originalPageID,
                'translationStatus' => $translationStatus
            ];
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback("copyPage");
            }
            
            return [
                'status' => 'error',
                'message' => 'Sayfa kopyalama hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Sayfa mapping'i kontrol eder
     */
    public function getPageMapping($originalPageID, $languageID)
    {
        $sql = "SELECT * FROM language_page_mapping 
                WHERE original_page_id = :pageID AND dilid = :languageID 
                LIMIT 1";
        
        $result = $this->db->select($sql, [
            'pageID' => $originalPageID,
            'languageID' => $languageID
        ]);
        
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Sayfa mapping'i günceller
     */
    public function updatePageMapping($mappingID, $translatedPageID, $translationStatus = 'completed')
    {
        $sql = "UPDATE language_page_mapping 
                SET translated_page_id = :translatedPageID, 
                    translation_status = :translationStatus,
                    last_attempt_date = NOW()
                WHERE id = :id";
        
        return $this->db->update($sql, [
            'id' => $mappingID,
            'translatedPageID' => $translatedPageID,
            'translationStatus' => $translationStatus
        ]);
    }

    /**
     * Sayfa çeviri işlemini yönetir - kategori kontrolü ve sayfa kopyalama dahil
     */
    public function processPageTranslation($pageID, $targetLanguageID, $translateWithAI = false)
    {
        try {
            // Sayfanın kategorilerini al
            $sql = "SELECT DISTINCT k.kategoriid, k.kategoriad, k.dilid 
                    FROM sayfa s
                    INNER JOIN sayfalistekategori slk ON s.sayfaid = slk.sayfaid 
                    INNER JOIN kategori k ON slk.kategoriid = k.kategoriid 
                    WHERE s.sayfaid = :pageID";
            
            $pageCategories = $this->db->select($sql, ['pageID' => $pageID]);
            
            $processedCategories = [];
            
            // Her kategori için çeviri kontrolü yap
            foreach ($pageCategories as $category) {
                $originalCategoryID = $category['kategoriid'];
                
                // Kategori çevirisi var mı kontrol et
                $categoryMapping = $this->getCategoryMapping($originalCategoryID, $targetLanguageID);
                
                if (!$categoryMapping || !$categoryMapping['translated_category_id']) {
                    // Kategori çevirisi yok, kopyala
                    $copyResult = $this->copyAndTranslateCategory($originalCategoryID, $targetLanguageID, $translateWithAI);
                    
                    if ($copyResult['status'] === 'error') {
                        return [
                            'status' => 'error',
                            'message' => 'Kategori kopyalanamadı: ' . $copyResult['message']
                        ];
                    }
                    
                    $processedCategories[] = [
                        'originalCategoryID' => $originalCategoryID,
                        'translatedCategoryID' => $copyResult['translatedCategoryID'],
                        'action' => 'copied'
                    ];
                } else {
                    $processedCategories[] = [
                        'originalCategoryID' => $originalCategoryID,
                        'translatedCategoryID' => $categoryMapping['translated_category_id'],
                        'action' => 'existing'
                    ];
                }
            }
            
            // Şimdi sayfa çevirisini kontrol et ve gerekirse kopyala
            $existingPageMapping = $this->getPageMapping($pageID, $targetLanguageID);
            $translationStatus = $translateWithAI ? 'pending' : 'completed';
            
            if ($existingPageMapping && $existingPageMapping['translated_page_id']) {
                // Sayfa zaten çevrilmiş
                $pageAction = 'existing';
                $translatedPageID = $existingPageMapping['translated_page_id'];
            } else {
                // Sayfa çevirisi yok, kopyala
                $copyPageResult = $this->copyAndTranslatePage($pageID, $targetLanguageID, $translateWithAI);
                
                if ($copyPageResult['status'] === 'error') {
                    return [
                        'status' => 'error',
                        'message' => 'Sayfa kopyalanamadı: ' . $copyPageResult['message']
                    ];
                }
                
                $pageAction = 'copied';
                $translatedPageID = $copyPageResult['translatedPageID'];
                $translationStatus = $copyPageResult['translationStatus'];
            }
            
            return [
                'status' => 'success',
                'message' => 'Sayfa çeviri işlemi başarıyla tamamlandı',
                'pageAction' => $pageAction,
                'translatedPageID' => $translatedPageID,
                'processedCategories' => $processedCategories,
                'translationStatus' => $translationStatus
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Sayfa çeviri işlemi hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kategori çeviri işlemini yönetir - kategori kopyalama dahil
     */
    public function processCategoryTranslation($originalCategoryID, $targetLanguageID, $translateWithAI = false)
    {
        try {
            // Kategori çevirisini kontrol et ve gerekirse kopyala
            $existingCategoryMapping = $this->getCategoryMapping($originalCategoryID, $targetLanguageID);
            $translationStatus = $translateWithAI ? 'pending' : 'completed';
            
            if ($existingCategoryMapping && $existingCategoryMapping['translated_category_id']) {
                // Kategori zaten çevrilmiş
                $categoryAction = 'existing';
                $translatedCategoryID = $existingCategoryMapping['translated_category_id'];
            } else {
                // Kategori çevirisi yok, kopyala
                $copyCategoryResult = $this->copyAndTranslateCategory($originalCategoryID, $targetLanguageID, $translateWithAI);
                
                if ($copyCategoryResult['status'] === 'error') {
                    return [
                        'status' => 'error',
                        'message' => 'Kategori kopyalanamadı: ' . $copyCategoryResult['message']
                    ];
                }
                
                $categoryAction = 'copied';
                $translatedCategoryID = $copyCategoryResult['translatedCategoryID'];
                $translationStatus = $copyCategoryResult['translationStatus'];
            }
            
            return [
                'status' => 'success',
                'message' => 'Kategori çeviri işlemi başarıyla tamamlandı',
                'categoryAction' => $categoryAction,
                'translatedCategoryID' => $translatedCategoryID,
                'translationStatus' => $translationStatus
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Kategori çeviri işlemi hatası: ' . $e->getMessage()
            ];
        }
    }

    public function getOriginalCategoryId($translatedCategoryId){
        $sql = "
            Select original_category_id From language_category_mapping Where translated_category_id = :translatedCategoryID
        ";
        $params = [
            'translatedCategoryID' => $translatedCategoryId
            ];
        return $this->db->select($sql, $params);
    }

    public function getOriginalPageId($translatedPageId){
        $sql = "
            Select original_page_id From language_page_mapping Where translated_page_id = :translatedPageID
        ";
        $params = [
            'translatedPageID' => $translatedPageId
        ];
        return $this->db->select($sql, $params);
    }
}
