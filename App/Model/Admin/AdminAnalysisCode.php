<?php

class AdminAnalysisCode
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getAnalysisCode($languageID)
    {
        $query = "
            SELECT 
                ayaranalizid as analysisCodeID,
                analizad as analysisCodeName,
                analiz as analysisCodeContent,
                analizamp as analysisCodeAmp
            FROM 
                ayaranaliz 
            WHERE 
                ayaranalizsil = 0 AND dilid = :languageID
        ";
        $params = [
            ':languageID' => $languageID
        ];
        return $this->db->select($query, $params);
    }

    public function addAnalysisCode($data)
    {
        $query = "
            INSERT INTO ayaranaliz 
            SET 
                dilid = :languageID,
                analizad = :analysisCodeName,
                analiz = :analysisCodeContent,
                analizamp = :analysisCodeAmp,
                benzersizid = :uniqueID,
                ayaranalizsil = 0
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':analysisCodeName' => $data['analysisCodeName'],
            ':analysisCodeContent' => $data['analysisCodeContent'],
            ':analysisCodeAmp' => $data['analysisCodeAmp'],
            ':uniqueID' => $data['uniqueID']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateAnalysisCode($data)
    {
        $query = "
            UPDATE ayaranaliz 
            SET 
                analizad = :analysisCodeName,
                analiz = :analysisCodeContent,
                analizamp = :analysisCodeAmp
            WHERE 
                dilid = :languageID
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':analysisCodeName' => $data['analysisCodeName'],
            ':analysisCodeContent' => $data['analysisCodeContent'],
            ':analysisCodeAmp' => $data['analysisCodeAmp']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteAnalysisCode($languageID)
    {
        $query = "
            UPDATE ayaranaliz 
            SET 
                ayaranalizsil = 1
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