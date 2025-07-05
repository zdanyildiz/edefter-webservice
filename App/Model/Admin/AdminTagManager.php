<?php

/**
 * Table: tag_manager
 * Columns:
 * tag_manager_contentid int AI PK
 * language_id int
 * tag_manager_name varchar(50)
 * tag_manager_content varchar(500)
 * tag_manager_deleted tinyint(1)
 * uniq_id varchar(20)
 */
class AdminTagManager
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
        $this->createTagManagerTable();
    }

    //tablo olu�turma kodu
    public function createTagManagerTable()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS tag_manager (
                tag_manager_id INT AUTO_INCREMENT PRIMARY KEY,
                language_id INT NOT NULL,
                tag_manager_name VARCHAR(50) NOT NULL,
                tag_manager_head VARCHAR(1000) NOT NULL,
                tag_manager_content VARCHAR(1000) NOT NULL,
                tag_manager_deleted TINYINT(1) NOT NULL,
                uniq_id VARCHAR(20) NOT NULL
            )
        ";
        return $this->db->createTable($query);
    }

    public function getTagManager($languageID)
    {
        $query = "
            SELECT 
                tag_manager_id,
                tag_manager_name,
                tag_manager_head,
                tag_manager_content
            FROM 
                tag_manager 
            WHERE 
                tag_manager_deleted = 0 AND language_id = :languageID";
        $params = [
            ':languageID' => $languageID
        ];
        return $this->db->select($query, $params);
    }

    public function addTagManager($data)
    {
        $query = "
            INSERT INTO tag_manager 
            SET 
                language_id = :languageID,
                tag_manager_name = :tagManagerName,
                tag_manager_head = :tagManagerHead,
                tag_manager_content = :tagManagerContent,
                uniq_id = :uniqueID,
                tag_manager_deleted = 0
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':tagManagerName' => $data['tagManagerName'],
            ':tagManagerHead' => $data['tagManagerHead'],
            ':tagManagerContent' => $data['tagManagerContent'],
            ':uniqueID' => $data['uniqueID']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateTagManager($data)
    {
        $query = "
            UPDATE tag_manager 
            SET 
                tag_manager_name = :tagManagerName,
                tag_manager_head = :tagManagerHead,
                tag_manager_content = :tagManagerContent
            WHERE 
                language_id = :languageID
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':tagManagerName' => $data['tagManagerName'],
            ':tagManagerHead' => $data['tagManagerHead'],
            ':tagManagerContent' => $data['tagManagerContent']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteTagManager($languageID)
    {
        $query = "
            UPDATE tag_manager 
            SET 
                tag_manager_deleted = 1
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