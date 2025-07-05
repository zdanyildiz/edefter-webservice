<?php

/**
 * Table: ayarsatisdonusumkodu
 * Columns:
 * satisdonusumkodid int AI PK
 * dilid int
 * satisdonusumkodad varchar(50)
 * satisdonusumkod varchar(500)
 * satisdonusumkodsil tinyint(1)
 * benzersizid varchar(20)
 */
class AdminSalesConversionCode
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getSalesConversionCode($languageID)
    {
        $query = "
            SELECT 
                satisdonusumkodid as salesConversionCodeID,
                satisdonusumkodad as salesConversionCodeName,
                satisdonusumkod as salesConversionCodeContent
            FROM 
                ayarsatisdonusumkodu 
            WHERE 
                satisdonusumkodsil = 0 AND dilid = :languageID";
        $params = [
            ':languageID' => $languageID
        ];
        return $this->db->select($query, $params);
    }

    public function addSalesConversionCode($data)
    {
        $query = "
            INSERT INTO ayarsatisdonusumkodu 
            SET 
                dilid = :languageID,
                satisdonusumkodad = :salesConversionCodeName,
                satisdonusumkod = :salesConversionCodeContent,
                benzersizid = :uniqueID,
                satisdonusumkodsil = 0
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':salesConversionCodeName' => $data['salesConversionCodeName'],
            ':salesConversionCodeContent' => $data['salesConversionCodeContent'],
            ':uniqueID' => $data['uniqueID']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateSalesConversionCode($data)
    {
        $query = "
            UPDATE ayarsatisdonusumkodu 
            SET 
                satisdonusumkodad = :salesConversionCodeName,
                satisdonusumkod = :salesConversionCodeContent
            WHERE 
                dilid = :languageID
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':salesConversionCodeName' => $data['salesConversionCodeName'],
            ':salesConversionCodeContent' => $data['salesConversionCodeContent']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteSalesConversionCode($languageID)
    {
        $query = "
            UPDATE ayarsatisdonusumkodu 
            SET 
                satisdonusumkodsil = 1
            WHERE 
                dilid = :languageID
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