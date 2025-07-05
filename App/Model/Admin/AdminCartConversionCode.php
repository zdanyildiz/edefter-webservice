<?php

/**
 * Table: cart_conversion_code
 * Columns:
 * cart_conversion_code_id int AI PK
 * language_id int
 * cart_conversion_code_name varchar(50)
 * cart_conversion_code varchar(500)
 * cart_conversion_code_deleted tinyint(1)
 * unique_id varchar(20)
 */
class AdminCartConversionCode
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getCartConversionCode($languageID)
    {
        $query = "
            SELECT 
                cart_conversion_code_id as cartConversionCodeID,
                cart_conversion_code_name as cartConversionCodeName,
                cart_conversion_code as cartConversionCodeContent
            FROM 
                cart_conversion_code 
            WHERE 
                cart_conversion_code_deleted = 0 AND language_id = :languageID";
        $params = [
            ':languageID' => $languageID
        ];
        return $this->db->select($query, $params);
    }

    public function addCartConversionCode($data)
    {
        $query = "
            INSERT INTO cart_conversion_code 
            SET 
                language_id = :languageID,
                cart_conversion_code_name = :cartConversionCodeName,
                cart_conversion_code = :cartConversionCodeContent,
                unique_id = :uniqueID,
                cart_conversion_code_deleted = 0
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':cartConversionCodeName' => $data['cartConversionCodeName'],
            ':cartConversionCodeContent' => $data['cartConversionCodeContent'],
            ':uniqueID' => $data['uniqueID']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateCartConversionCode($data)
    {
        $query = "
            UPDATE cart_conversion_code 
            SET 
                cart_conversion_code_name = :cartConversionCodeName,
                cart_conversion_code = :cartConversionCodeContent
            WHERE 
                language_id = :languageID
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':cartConversionCodeName' => $data['cartConversionCodeName'],
            ':cartConversionCodeContent' => $data['cartConversionCodeContent']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteCartConversionCode($languageID)
    {
        $query = "
            UPDATE cart_conversion_code 
            SET 
                cart_conversion_code_deleted = 1
            WHERE 
                language_id = :languageID
        ";

        $params = [
            ':languageID' => $languageID
        ];

        return $this->db->update($query, $params);
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();

    }

    public function rollBack()
    {
        $this->db->rollBack();
    }

}