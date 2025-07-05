<?php
/**
 * Table: urungrup
 * Columns:
 * urungrupid int AI PK
 * urungrupad varchar(50)
 * urungrupaciklama varchar(1000)
 * urungrupkdv double
 * urungrupindirim double
 * urungrupfiyateski tinyint(1)
 * urungrupfiyatsontarih date
 * urungruptaksit tinyint
 * urungruphediye varchar(500)
 * urungrupaciklamakisa varchar(500)
 * urungrupkargosuresi tinyint
 * urungrupsabitkargoucreti decimal(4,2)
 * urungrupsil tinyint(1)
 * benzersizid char(20)
 */
class AdminProductGroup
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getProductGroup($groupID = null)
    {
        $query = "
            SELECT 
                urungrupid as productGroupID,
                urungrupad as productGroupName,
                urungrupaciklama as productGroupDescription,
                urungrupkdv as productTaxRate,
                urungrupindirim as productDiscountRate,
                urungrupfiyateski as productGroupShowDiscountedPrice,
                urungrupfiyatsontarih as productGroupProductPriceLastDate,
                urungruptaksit as productGroupInstallment,
                urungruphediye as productGroupProductDescription,
                urungrupaciklamakisa as productShortDesc,
                urungrupkargosuresi as productGroupDeliveryTime,
                urungrupsabitkargoucreti as productGroupFixedShippingCost,
                urungrupsil as productGroupDeleted,
                benzersizid as productGroupUniqID
            FROM 
                urungrup 
            WHERE
                urungrupsil=0 and urungrupid = :groupID
        ";
        $params = [
            'groupID' => $groupID
        ];

        $result = $this->db->select($query, $params);

        if ($result) {
            return [
                'status' => "success",
                'data' => $result[0]
            ];
        }
        else {
            return [
                'status' => "error",
                'message' => "Product group not found"
            ];
        }

    }

    public function getProductGroups()
    {
        $sql = "
            SELECT 
                urungrupid as productGroupID,
                urungrupad as productGroupName,
                urungrupaciklama as productGroupDescription,
                urungrupkdv as productTaxRate,
                urungrupindirim as productDiscountRate,
                urungrupfiyateski as productGroupShowDiscountedPrice,
                urungrupfiyatsontarih as productGroupProductPriceLastDate,
                urungruptaksit as productGroupInstallment,
                urungruphediye as productGroupProductDescription,
                urungrupaciklamakisa as productShortDesc,
                urungrupkargosuresi as productGroupDeliveryTime,
                urungrupsabitkargoucreti as productGroupFixedShippingCost,
                urungrupsil as productGroupDeleted,
                benzersizid as productGroupUniqID
            FROM 
                urungrup 
            WHERE
                urungrupsil=0
        ";

        $result = $this->db->select($sql);

        if ($result) {
            return [
                'status' => "success",
                'data' => $result
            ];
        }
        else {
            return [
                'status' => "error",
                'message' => "Product groups not found"
            ];
        }
    }

    public function addProductGroup($insertData){

        $query = "
            INSERT INTO urungrup (
                urungrupad,
                urungrupaciklama,
                urungrupkdv,
                urungrupindirim,
                urungrupfiyateski,
                urungrupfiyatsontarih,
                urungruptaksit,
                urungruphediye,
                urungrupaciklamakisa,
                urungrupkargosuresi,
                urungrupsabitkargoucreti,
                urungrupsil,
                benzersizid
            ) VALUES (
                :productGroupName,
                :productGroupDescription,
                :productGroupTaxRate,
                :productGroupDiscountRate,
                :productGroupShowDiscountedPrice,
                :productGroupProductPriceLastDate,
                :productGroupInstallment,
                :productGroupProductDescription,
                :productGroupProductShortDesc,
                :productGroupProductCargoTime,
                :productGroupFixedShippingCost,
                :productGroupIsDeleted,
                :productGroupUniqID
            )
        ";


        $this->db->beginTransaction();
        $result = $this->db->insert($query, $insertData);

        if ($result) {
            $this->db->commit();
            return [
                'status' => "success",
                'message' => "Product group added successfully",
                'productGroupID' => $result
            ];
        }
        else {
            $this->db->rollBack();
            return [
                'status' => "error",
                'message' => "Product group could not be added"
            ];
        }
    }

    public function updateProductGroup($updateData)
    {

        $query = "
            UPDATE urungrup SET
                urungrupad = :productGroupName,
                urungrupaciklama = :productGroupDescription,
                urungrupkdv = :productGroupTaxRate,
                urungrupindirim = :productGroupDiscountRate,
                urungrupfiyateski = :productGroupShowDiscountedPrice,
                urungrupfiyatsontarih = :productGroupProductPriceLastDate,
                urungruptaksit = :productGroupInstallment,
                urungruphediye = :productGroupProductDescription,
                urungrupaciklamakisa = :productGroupProductShortDesc,
                urungrupkargosuresi = :productGroupProductCargoTime,
                urungrupsabitkargoucreti = :productGroupFixedShippingCost,
                urungrupsil = :productGroupIsDeleted
            WHERE
                urungrupid = :productGroupID
        ";

        $this->db->beginTransaction();

        $result = $this->db->update($query, $updateData);

        if ($result >= 0) {
            if ($result == 0) {
                $this->db->rollBack();
                return [
                    'status' => "success",
                    'message' => "Grup bilgileri güncel"
                ];
            }
            $this->db->commit();
            return [
                'status' => "success",
                'message' => "Grup güncellendi"
            ];
        }
        else {
            $this->db->rollBack();
            return [
                'status' => "error",
                'message' => "Grup güncellenemedi"
            ];
        }
    }

    //deleteProductGroup method, grupsil'i grupid'ye göre 1 yapacağız
    public function deleteProductGroup($groupID)
    {
        $query = "
            UPDATE urungrup SET
                urungrupsil = 1
            WHERE
                urungrupid = :groupID
        ";

        $this->db->beginTransaction();

        $result = $this->db->update($query, ['groupID' => $groupID]);

        if ($result >= 0) {
            if ($result == 0) {
                $this->db->rollBack();
                return [
                    'status' => "success",
                    'message' => "Grup zaten silinmiş"
                ];
            }
            $this->db->commit();
            return [
                'status' => "success",
                'message' => "Grup silindi"
            ];
        }
        else {
            $this->db->rollBack();
            return [
                'status' => "error",
                'message' => "Grup silinemedi"
            ];
        }
    }
}