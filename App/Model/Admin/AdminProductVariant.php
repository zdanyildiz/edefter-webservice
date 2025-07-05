<?php
//urunvaryantgrup
//varyantgrupid
//benzersizid
//varyantgrupad
//varyantgrupsil

//urunvaryant
//varyantid
//varyantgrupid
//varyantad
//varyantsira
//varyantsil

class AdminProductVariant
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getVariantGroups($lang="tr")
    {
        $sql = "
            SELECT 
                urunvaryantgrup.varyantgrupid as variantGroupID, 
                urunvaryantgrup.benzersizid as variantGroupUniqID,
                COALESCE(urunvaryantgrup_translate.varyantgrupad, urunvaryantgrup.varyantgrupad) as variantGroupName
            FROM 
                urunvaryantgrup 
                LEFT JOIN 
                    urunvaryantgrup_translate 
                ON 
                    urunvaryantgrup.varyantgrupid = urunvaryantgrup_translate.varyantgrupid 
                    AND urunvaryantgrup_translate.dil = :lang
            WHERE 
                urunvaryantgrup.varyantgrupsil = 0
            ORDER BY 
                urunvaryantgrup.varyantgrupsira
        ";

        $params = [
            'lang' => $lang
        ];


        $result = $this->db->select($sql,$params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Variant groups not found'
            ];
        }
    }
    public function getVariantsGroupByName($variantGroupName,$lang="tr")
    {
        $sql = "
            SELECT 
                urunvaryantgrup.varyantgrupid as variantGroupID, 
                urunvaryantgrup.benzersizid as variantGroupUniqID,
                COALESCE(urunvaryantgrup_translate.varyantgrupad, urunvaryantgrup.varyantgrupad) as variantGroupName
            FROM 
                urunvaryantgrup 
                LEFT JOIN 
                    urunvaryantgrup_translate 
                ON 
                    urunvaryantgrup.varyantgrupid = urunvaryantgrup_translate.varyantgrupid 
                    AND urunvaryantgrup_translate.dil = :lang
            WHERE 
                urunvaryantgrup.varyantgrupad = :variantGroupName
                AND urunvaryantgrup.varyantgrupsil = 0
        ";

        $params = [
            'variantGroupName' => $variantGroupName,
            'lang' => $lang
        ];

        $result = $this->db->select($sql,$params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Variant groups not found'
            ];
        }
    }
    public function getVariantGroup($variantGroupID,$lang="tr")
    {
        $sql = "
            SELECT 
                urunvaryantgrup.varyantgrupid as variantGroupID, 
                urunvaryantgrup.benzersizid as variantGroupUniqID,
                COALESCE(urunvaryantgrup_translate.varyantgrupad, urunvaryantgrup.varyantgrupad) as variantGroupName
            FROM 
                urunvaryantgrup 
                    LEFT JOIN 
                        urunvaryantgrup_translate
                    ON
                        urunvaryantgrup.varyantgrupid = urunvaryantgrup_translate.varyantgrupid
                        AND urunvaryantgrup_translate.dil = :lang
            WHERE 
                urunvaryantgrup.varyantgrupid = :variantGroupID
        ";

        $params = [
            'variantGroupID' => $variantGroupID,
            'lang' => $lang
        ];

        $result = $this->db->select($sql,$params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result[0]
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Variant group not found'
            ];
        }
    }

    public function getVariants($variantGroupID,$lang="tr")
    {
        $sql = "
            SELECT 
                urunvaryant.varyantid as variantID, 
                COALESCE(urunvaryant_translate.varyantad, urunvaryant.varyantad) as variantName, 
                urunvaryant.varyantgrupid as variantGroupID, 
                urunvaryant.varyantsira as variantOrder
            FROM 
                urunvaryant 
                    LEFT JOIN 
                        urunvaryant_translate 
                    ON 
                        urunvaryant.varyantid = urunvaryant_translate.varyantid 
                        AND urunvaryant_translate.dil = :lang
            WHERE 
                urunvaryant.varyantgrupid = :variantGroupID 
                AND urunvaryant.varyantsil = 0
            ORDER BY 
                urunvaryant.varyantsira
        ";


        $params = [
            'variantGroupID' => $variantGroupID,
            'lang' => $lang
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Variants not found'
            ];
        }
    }

    public function getVariantByGroupIDAndName($variantGroupID, $variantName)
    {
        $sql = "
            SELECT 
                urunvaryant.varyantid as variantID, 
                urunvaryant.varyantad as variantName
            FROM 
                urunvaryant
            WHERE 
                urunvaryant.varyantgrupid = :variantGroupID AND urunvaryant.varyantad = :variantName
            ORDER BY 
                urunvaryant.varyantgrupid ASC
        ";
        $params = array(
            ':variantGroupID' => $variantGroupID,
            ':variantName' => $variantName
        );
        $result = $this->db->select($sql, $params);
        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Variant not found'
            ];
        }
    }

    public function getVariantsByGroupID($variantGroupID)
    {
        $sql = "
            SELECT 
                urunvaryant.varyantid as variantID, 
                urunvaryant.varyantad as variantName
            FROM 
                urunvaryant
            WHERE 
                urunvaryant.varyantgrupid = :variantGroupID 
                AND urunvaryant.varyantsil = 0
            ORDER BY 
                urunvaryant.varyantsira
        ";

        $params = [
            'variantGroupID' => $variantGroupID
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'gruba ait varyant bulunamadı'
            ];
        }
    }

    public function getVariantByName($variantName,$lang="tr")
    {
        $sql = "
            SELECT 
                urunvaryant.varyantid as variantID, 
                COALESCE(urunvaryant_translate.varyantad, urunvaryant.varyantad) as variantName, 
                urunvaryant.varyantgrupid as variantGroupID, 
                urunvaryant.varyantsira as variantOrder
            FROM 
                urunvaryant 
                    LEFT JOIN 
                        urunvaryant_translate 
                    ON 
                        urunvaryant.varyantid = urunvaryant_translate.varyantid 
                        AND urunvaryant_translate.dil = :lang
            WHERE 
                urunvaryant.varyantad = :variantName 
                AND urunvaryant.varyantsil = 0
            ORDER BY 
                urunvaryant.varyantsira
        ";

        $params = [
            'variantName' => $variantName,
            'lang' => $lang
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Variants not found'
            ];
        }
    }

    public function sortVariantGroups($variantGroupIDs)
    {
        $sql = "
            UPDATE 
                urunvaryantgrup 
            SET 
                varyantgrupsira = :sira
            WHERE 
                varyantgrupid = :variantGroupID
        ";

        $sira = 1;

        foreach ($variantGroupIDs as $variantGroupID) {
            $params = [
                'sira' => $sira,
                'variantGroupID' => $variantGroupID
            ];

            $this->db->update($sql, $params);

            $sira++;
        }

        return [
            'status' => 'success'
        ];
    }

    public function addVariantGroup($variantGroupName,$variantGroupUniqID)
    {
        $sql = "
            INSERT INTO 
                urunvaryantgrup 
            SET 
                varyantgrupad = :variantGroupName,
                benzersizid = :variantGroupUniqID,
                varyantgrupsil = 0,
                varyantgrupsira = 999
        ";

        $params = [
            'variantGroupName' => $variantGroupName,
            'variantGroupUniqID' => $variantGroupUniqID
        ];



        $variantGroupID = $this->db->insert($sql, $params);

        if (!$variantGroupID) {

            return [
                'status' => 'error',
                'message' => 'Variant group not added'
            ];
        }
        else {

            return [
                'status' => 'success',
                'message' => 'Variant group added',
                'variantGroupID' => $variantGroupID
            ];
        }
    }

    public function updateVariantGroup($variantGroupID,$variantGroupName)
    {
        $sql = "
            UPDATE 
                urunvaryantgrup 
            SET 
                varyantgrupad = :variantGroupName
            WHERE 
                varyantgrupid = :variantGroupID
        ";

        $params = [
            'variantGroupName' => $variantGroupName,
            'variantGroupID' => $variantGroupID
        ];



        $result = $this->db->update($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variant group not updated'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variant group updated',
                'variantGroupID' => $variantGroupID
            ];
        }

    }

    public function addAndUpdateVariantGroupTranslate($variantGroupID,$variantGroupName,$languageCode){

        $sql = "
        INSERT INTO
            urunvaryantgrup_translate
        SET
            varyantgrupid = :variantGroupID,
            varyantgrupad = :variantGroupName,
            dil = :languageCode
        ON DUPLICATE KEY UPDATE
            varyantgrupad = :variantGroupName1
    ";

        $params = [
            'variantGroupID' => $variantGroupID,
            'variantGroupName' => $variantGroupName,
            'languageCode' => $languageCode,
            'variantGroupName1' => $variantGroupName
        ];

        $result = $this->db->insert($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variant group translate not added'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variant group translate added',
                'variantGroupID' => $variantGroupID
            ];
        }
    }

    public function deleteVariantGroup($variantGroupID)
    {
        $sql = "
            UPDATE 
                urunvaryantgrup 
            SET 
                varyantgrupsil = 1
            WHERE 
                varyantgrupid = :variantGroupID
        ";

        $params = [
            'variantGroupID' => $variantGroupID
        ];

        $result = $this->db->update($sql, $params);

        if ($result<0) {
            return [
                'status' => 'error',
                'message' => 'Varyant grubu silinemedi x {$result}'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Varyant grup silme başarılı',
                'variantGroupID' => $variantGroupID
            ];
        }
    }

    public function deleteVariantGroupTranslateByVariantGroupIDAndLanguageCode($variantGroupID,$languageCode)
    {
        $sql = "
            DELETE FROM 
                urunvaryantgrup_translate 
            WHERE 
                varyantgrupid = :variantGroupID 
                AND dil = :languageCode
        ";

        $params = [
            'variantGroupID' => $variantGroupID,
            'languageCode' => $languageCode
        ];

        $result = $this->db->delete($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variant group translate not deleted'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variant group translate deleted',
                'variantGroupID' => $variantGroupID
            ];
        }
    }

    public function deleteVariantGroupTranslateByVariantGroupID($variantGroupID)
    {
        $sql = "
            DELETE FROM 
                urunvaryantgrup_translate 
            WHERE 
                varyantgrupid = :variantGroupID
        ";

        $params = [
            'variantGroupID' => $variantGroupID
        ];

        $result = $this->db->delete($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variant group translate not deleted'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variant group translate deleted',
                'variantGroupID' => $variantGroupID
            ];
        }
    }
    public function deleteVariantsByGroupID($variantGroupID)
    {
        $sql = "
            UPDATE 
                urunvaryant 
            SET 
                varyantsil = 1
            WHERE 
                varyantgrupid = :variantGroupID
        ";

        $params = [
            'variantGroupID' => $variantGroupID
        ];

        $result = $this->db->update($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variants not deleted'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variants deleted',
                'variantGroupID' => $variantGroupID
            ];
        }
    }

    public function deleteVariantTranslateByVariantIDAndLanguageCode($variantID,$languageCode)
    {
        $sql = "
            DELETE FROM 
                urunvaryant_translate 
            WHERE 
                varyantid = :variantID 
                AND dil = :languageCode
        ";

        $params = [
            'variantID' => $variantID,
            'languageCode' => $languageCode
        ];

        $result = $this->db->delete($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variant translate not deleted'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variant translate deleted',
                'variantID' => $variantID
            ];
        }
    }

    public function deleteVariantTranslateByVariantID($variantID)
    {
        $sql = "
            DELETE FROM 
                urunvaryant_translate 
            WHERE 
                varyantid = :variantID
        ";

        $params = [
            'variantID' => $variantID
        ];

        $result = $this->db->delete($sql, $params);

        if (!$result) {
            return [
                'status' => 'error',
                'message' => 'Variant translate not deleted'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Variant translate deleted',
                'variantID' => $variantID
            ];
        }
    }

    public function addVariant($variantName,$variantGroupID)
    {
        $sql = "
            INSERT INTO 
                urunvaryant 
            SET 
                varyantad = :variantName,
                varyantgrupid = :variantGroupID,
                varyantsil = 0,
                varyantsira = 999
        ";

        $params = [
            'variantName' => $variantName,
            'variantGroupID' => $variantGroupID
        ];

        $variantID = $this->db->insert($sql, $params);

        if (!$variantID) {
            return [
                'status' => 'error',
                'message' => 'Varyant eklenemedi'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Varyant ekleme başarılı',
                'variantID' => $variantID
            ];
        }
    }

    public function updateVariant($variantID,$variantName)
    {
        $sql = "
            UPDATE 
                urunvaryant 
            SET 
                varyantad = :variantName
            WHERE 
                varyantid = :variantID
        ";

        $params = [
            'variantName' => $variantName,
            'variantID' => $variantID
        ];

        $result = $this->db->update($sql, $params);

        if ($result<0) {
            return [
                'status' => 'error',
                'message' => 'Varyant güncellenemedi'
            ];
        }
        else {

            if($result == 0){
                return [
                    'status' => 'error',
                    'message' => 'Varyant zaten güncel'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Varyant güncelleme başarılı',
                'variantID' => $variantID
            ];
        }
    }

    public function deleteVariant($variantID)
    {
        $sql = "
            UPDATE 
                urunvaryant 
            SET 
                varyantsil = 1
            WHERE 
                varyantid = :variantID
        ";

        $params = [
            'variantID' => $variantID
        ];

        $result = $this->db->update($sql, $params);

        if ($result<0) {
            return [
                'status' => 'error',
                'message' => 'Varyant silinemedi'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Varyant silme başarılı',
                'variantID' => $variantID
            ];
        }
    }

    public function addAndUpdateVariantTranslate($variantID,$variantName,$languageCode)
    {
        $sql = "
            INSERT INTO 
                urunvaryant_translate 
            SET 
                varyantid = :variantID,
                varyantad = :variantName,
                dil = :languageCode
            ON DUPLICATE KEY UPDATE
                varyantad = :variantName1
        ";

        $params = [
            'variantID' => $variantID,
            'variantName' => $variantName,
            'languageCode' => $languageCode,
            'variantName1' => $variantName
        ];

        $result = $this->db->insert($sql, $params);

        if (!$result) {;
            return [
                'status' => 'error',
                'message' => 'Varyant translate eklenemedi'
            ];
        }
        else {
            return [
                'status' => 'success',
                'message' => 'Varyant translate ekleme başarılı',
                'variantID' => $variantID
            ];
        }
    }

    public function sortVariants($variantIDs)
    {
        $sql = "
            UPDATE 
                urunvaryant 
            SET 
                varyantsira = :sira
            WHERE 
                varyantid = :variantID
        ";

        $sira = 1;

        foreach ($variantIDs as $variantID) {
            $params = [
                'sira' => $sira,
                'variantID' => $variantID
            ];

            $result = $this->db->update($sql, $params);
            if($result<0){
                return [
                    'status' => 'error',
                    'message' => 'Variant güncellenemedi'
                ];
            }

            $sira++;
        }

        return [
            'status' => 'success'
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

    public function rollback($funcName = "")
    {
        $this->db->rollback($funcName);
    }

}