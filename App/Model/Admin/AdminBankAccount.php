<?php

/**
 * Table: bankaeft
 * Columns:
 * bankaeftid int AI PK
 * dilid int
 * bankaad varchar(45)
 * hesapadi varchar(100)
 * hesapsube varchar(45)
 * hesapno varchar(45)
 * ibanno varchar(45)
 * benzersizid varchar(20)
 * bankaeftsil tinyint(1)
 */
class AdminBankAccount{

    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getBankAccounts($data)
    {
        $sql = "
            SELECT 
                dilid as languageID,
                bankaeftid as bankAccountID,
                bankaad as bankName,
                hesapadi as accountName,
                hesapsube as branchName,
                hesapno as accountNumber,
                ibanno as ibanNumber,
                benzersizid as uniqueID
            FROM 
                bankaeft 
            WHERE 
                dilid = :languageID AND bankaeftsil = 0";
        $result = $this->db->select($sql, $data);

        return $result;
    }

    public function getBankAccount($bankAccountID)
    {
        $sql = "
            SELECT 
                bankaeftid as bankAccountID,
                dilid as languageID,
                bankaeftid as bankAccountID,
                bankaad as bankName,
                hesapadi as accountName,
                hesapsube as branchName,
                hesapno as accountNumber,
                ibanno as ibanNumber,
                benzersizid as uniqueID
            FROM 
                bankaeft 
            WHERE 
                bankaeftid = :bankAccountID
        ";

        $result = $this->db->select($sql, ["bankAccountID" => $bankAccountID]);

        if ($result) {
            return [
                "status" => "success",
                "data" => $result[0]
            ];
        }
        else {
            return [
                "status" => "error",
                "message" => "Banka hesabı bulunamadı"
            ];
        }
    }

    public function addBankAccount($data)
    {
        $sql = "
            INSERT INTO 
                bankaeft 
            SET 
                dilid = :languageID,
                bankaad = :bankName,
                hesapadi = :accountName,
                hesapsube = :branchName,
                hesapno = :accountNumber,
                ibanno = :ibanNumber,
                benzersizid = :uniqueID,
                bankaeftsil = 0
        ";

        $this->db->beginTransaction();
        $result = $this->db->insert($sql, $data);

        if ($result) {
            $this->db->commit();
            return [
                "status" => "success",
                "message" => "Banka hesabı eklendi",
                "bankAccountID" => $result
            ];
        }
        else {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => "Banka hesabı eklenemedi"
            ];
        }
    }

    public function updateBankAccount($data)
    {
        $sql = "
            UPDATE 
                bankaeft 
            SET 
                bankaad = :bankName,
                hesapadi = :accountName,
                hesapsube = :branchName,
                hesapno = :accountNumber,
                ibanno = :ibanNumber
            WHERE 
                bankaeftid = :bankAccountID";

        $this->db->beginTransaction();
        $result = $this->db->update($sql, $data);

        if ($result>=0) {
            if ($result == 0) {
                $this->db->rollBack();
                return [
                    "status" => "success",
                    "message" => "Banka hesabı Güncel"
                ];
            }

            $this->db->commit();
            return [
                "status" => "success",
                "message" => "Banka hesabı güncellendi"
            ];
        }
        else {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => "Banka hesabı güncellenemedi"
            ];
        }
    }

    public function deleteBankAccount($data)
    {
        $sql = "
            UPDATE 
                bankaeft 
            SET 
                bankaeftsil = 1
            WHERE 
                bankaeftid = :bankAccountID";

        $this->db->beginTransaction();
        $result = $this->db->update($sql, $data);

        if ($result) {
            $this->db->commit();
            return [
                "status" => "success",
                "message" => "Banka hesabı silindi"
            ];
        }
        else {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => "Banka hesabı silinemedi"
            ];
        }
    }
}