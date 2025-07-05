<?php
class AdminSupplier
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllSuppliers()
    {
        $sql = "
            SELECT 
                uyetcno as supplierIdentityNumber,
                uyeid as supplierID,
                memberTitle as supplierTitle,
                uyead as supplierName,
                uyesoyad as supplierSurname,
                uyetip as supplierType,
                uyeeposta as supplierEmail,
                uyesifre as supplierPassword,
                uyetelefon as supplierPhone,
                uyeaciklama as supplierDescription,
                uyefaturaad as supplierInvoiceTitle,
                uyefaturavergidairesi as supplierTaxOffice,
                uyefaturavergino as supplierTaxNumber,
                uyeaktif as supplierIsActive
            FROM 
                uye 
            WHERE 
                uyeaktif=1 AND uyesil='0' AND uyetip=2
        ";
        return $this->db->select($sql);
    }

    public function getSupplier($supplierID)
    {
        $sql = "
            SELECT 
                uyetcno as supplierIdentityNumber,
                uyeid as supplierID,
                memberTitle as supplierTitle,
                uyead as supplierName,
                uyesoyad as supplierSurname,
                uyetip as supplierType,
                uyeeposta as supplierEmail,
                uyesifre as supplierPassword,
                uyetelefon as supplierPhone,
                uyeaciklama as supplierDescription,
                uyefaturaad as supplierInvoiceTitle,
                uyefaturavergidairesi as supplierTaxOffice,
                uyefaturavergino as supplierTaxNumber,
                uyeaktif as supplierIsActive
            FROM 
                uye 
            WHERE uyeid=?
        ";
        return $this->db->select($sql, array($supplierID));
    }

    public function addSupplier($supplierData)
    {
        //gerekli veriler şifrelenmiş geliyor, kayıt işlemi yapalım

        $sql = "
            INSERT INTO
                uye
            SET
                benzersizid = :supplierUniqID,
                uyeolusturmatarih = NOW(),
                uyeguncellemetarih = NOW(),
                uyetip = :supplierType,
                uyetcno = :supplierIdentityNumber,
                memberTitle = :supplierTitle,
                uyead = :supplierName,
                uyesoyad = :supplierSurname,
                uyeeposta = :supplierEmail,
                uyetelefon = :supplierPhoneNumber,
                uyesifre = :supplierPassword,
                uyeaciklama = :supplierDescription,
                uyefaturaad = :supplierInvoiceTitle,
                uyefaturavergidairesi = :supplierTaxOffice,
                uyefaturavergino = :supplierTaxNumber,
                uyeaktif = 1,
                uyesil = 0
        ";

        return $this->db->insert($sql, $supplierData);
    }

    public function updateSupplier($supplierData){

        $sql = "
            UPDATE
                uye
            SET
                uyeguncellemetarih = NOW(),
                uyetcno = :supplierIdentityNumber,
                memberTitle = :supplierTitle,
                uyead = :supplierName,
                uyesoyad = :supplierSurname,
                uyeeposta = :supplierEmail,
                uyetelefon = :supplierPhoneNumber,
                uyesifre = :supplierPassword,
                uyeaciklama = :supplierDescription,
                uyefaturaad = :supplierInvoiceTitle,
                uyefaturavergidairesi = :supplierTaxOffice,
                uyefaturavergino = :supplierTaxNumber,
                uyeaktif = :supplierIsActive
            WHERE
                uyeid = :supplierID
        ";

        return $this->db->update($sql, $supplierData);

    }

    public function deleteSupplier($supplierID)
    {
        $sql = "UPDATE uye SET uyeaktif=0,uyesil=1 WHERE uyeid=?";
        return $this->db->update($sql, array($supplierID));
    }
}