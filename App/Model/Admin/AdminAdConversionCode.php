<?php

/**
 * Table: ad_conversion_code
 * Columns:
 * ad_conversion_code_contentid int AI PK
 * language_id int
 * ad_conversion_code_name varchar(50)
 * ad_conversion_code_content varchar(500)
 * ad_conversion_code_deleted tinyint(1)
 * uniq_id varchar(20)
 */
class AdminAdConversionCode
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
        //tablo yoksa oluşturalım
        $this->createAdConversionTable();
    }

    //tablo oluşturma kodu
    public function createAdConversionTable()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS ad_conversion_code (
                ad_conversion_code_id INT AUTO_INCREMENT PRIMARY KEY,
                language_id INT NOT NULL,
                ad_conversion_code_name VARCHAR(50) NOT NULL,
                ad_conversion_code_head VARCHAR(1000) NOT NULL,
                ad_conversion_code_content VARCHAR(1000) NOT NULL,
                ad_conversion_code_deleted TINYINT(1) NOT NULL,
                uniq_id VARCHAR(20) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        return $this->db->createTable($query);
    }

    public function getAdConversionCode($languageID)
    {
        $query = "
            SELECT 
                ad_conversion_code_id,
                ad_conversion_code_name,
                ad_conversion_code_head,
                ad_conversion_code_content
            FROM 
                ad_conversion_code 
            WHERE 
                ad_conversion_code_deleted = 0 AND language_id = :languageID";
        $params = [
            ':languageID' => $languageID
        ];
        return $this->db->select($query, $params);
    }

    public function addAdConversionCode($data)
    {
        $query = "
            INSERT INTO ad_conversion_code 
            SET 
                language_id = :languageID,
                ad_conversion_code_name = :adConversionCodeName,
                ad_conversion_code_head = :adConversionCodeHead,
                ad_conversion_code_content = :adConversionCodeContent,
                uniq_id = :uniqueID,
                ad_conversion_code_deleted = 0
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':adConversionCodeName' => $data['adConversionCodeName'],
            ':adConversionCodeHead' => $data['adConversionCodeHead'],
            ':adConversionCodeContent' => $data['adConversionCodeContent'],
            ':uniqueID' => $data['uniqueID']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateAdConversionCode($data)
    {
        $query = "
            UPDATE ad_conversion_code 
            SET 
                ad_conversion_code_name = :adConversionCodeName,
                ad_conversion_code_head = :adConversionCodeHead,
                ad_conversion_code_content = :adConversionCodeContent
            WHERE 
                language_id = :languageID
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':adConversionCodeName' => $data['adConversionCodeName'],
            ':adConversionCodeHead' => $data['adConversionCodeHead'],
            ':adConversionCodeContent' => $data['adConversionCodeContent']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteAdConversionCode($languageID)
    {
        $query = "
            UPDATE ad_conversion_code 
            SET 
                ad_conversion_code_deleted = 1
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

    public function rollback()
    {
        $this->db->rollback();
    }
}