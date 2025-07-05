<?php

class AdminPageType
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
        $this->checkAndCreateTable();
    }

    // Tablonun var olup olmadığını kontrol eder ve yoksa oluşturur
    private function checkAndCreateTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS sayfatip (
            sayfatipid INT AUTO_INCREMENT PRIMARY KEY,
            sayfatipad VARCHAR(50) NOT NULL,
            yetki TINYINT(1) DEFAULT 0,
            gorunum TINYINT(1) DEFAULT 1,
            sayfatipsil TINYINT(1) DEFAULT 0
        ) ENGINE=InnoDB;";

        if (!$this->db->createTable($query)) {
            Log::adminWrite("Error creating table: " . $query, "error");
        }
    }

    // Sayfa tipi ekler
    public function createPageType($pageTypeName, $authority = 0, $view = 1, $pageTypeDeleted = 0)
    {
        $query = "INSERT INTO sayfatip (sayfatipad, yetki, gorunum, sayfatipsil) VALUES (?, ?, ?, ?)";
        $params = [$pageTypeName, $authority, $view, $pageTypeDeleted];
        return $this->db->insert($query, $params);
    }

    // Sayfa tipi günceller
    public function updatePageType($pageTypeID, $pageTypeName, $authority = 0, $view = 1, $pageTypeDeleted = 0)
    {
        $query = "UPDATE sayfatip SET sayfatipad = ?, yetki = ?, gorunum = ?, sayfatipsil = ? WHERE sayfatipid = ?";
        $params = [$pageTypeName, $authority, $view, $pageTypeDeleted, $pageTypeID];
        return $this->db->update($query, $params);
    }

    // Sayfa tipi siler (soft delete için sayfatipsil değerini günceller)
    public function deletePageType($pageTypeID)
    {
        $query = "UPDATE sayfatip SET sayfatipsil = 1 WHERE sayfatipid = ?";
        $params = [$pageTypeID];
        return $this->db->update($query, $params);
    }

    // Tüm sayfa tiplerini getirir
    public function getPageTypes($includeDeleted = false)
    {
        $query = "
            SELECT 
                sayfatipid as pageTypeID,
                sayfatipad as pageTypeName,
                yetki as pageTypePermission,
                gorunum as pageTypeView,
                sayfatipsil as pageTypeDeleted
            FROM 
                sayfatip
        ";

        if (!$includeDeleted) {
            $query .= " WHERE sayfatipsil = 0";
        }
        return $this->db->select($query);
    }

    // Belirli bir sayfa tipini getirir
    public function getPageTypeById($pageTypeID)
    {
        $query = "SELECT 
    
            sayfatipid as pageTypeID,
            sayfatipad as pageTypeName,
            yetki as pageTypePermission,
            gorunum as pageTypeView,
            sayfatipsil as pageTypeDeleted
    
        FROM 
            sayfatip
        WHERE 
            sayfatipid = ?";
        $params = [$pageTypeID];
        return $this->db->select($query, $params);
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

// Kullanım örneği
// $pageTypeModel = new AdminPageType($db);
// $pageTypeModel->createPageType('Blog', 1, 1, 0);
