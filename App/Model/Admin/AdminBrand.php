<?php
/*
 * urunmarka
markaid
markatariholustur
markatarihguncel
markaad
markaaciklama
markaindirim
markataksit
markapromosyontutari
markasil
benzersizid
 */
class AdminBrand{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllBrands()
    {

        $sql = "SELECT * FROM urunmarka WHERE markasil='0'";
        $result = $this->db->select($sql);

        if ($result) {
            //sütun isimlerini ingilizceye çevirelim
            $result = array_map(function ($item) {
                return array(
                    'brandID' => $item['markaid'],
                    'brandName' => $item['markaad'],
                    'brandDescription' => $item['markaaciklama'],
                    'brandDiscount' => $item['markaindirim'],
                    'brandInstallment' => $item['markataksit'],
                    'brandPromotionAmount' => $item['markapromosyontutari'],
                    'brandUniqueID' => $item['benzersizid']
                );
            }, $result);

            return $result;
        } else {
            return false;
        }
    }

    public function getBrand($brandID)
    {
        $sql = "SELECT * FROM urunmarka WHERE markaid = :brandID";
        $params = array('brandID' => $brandID);
        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result[0]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Brand not found'
            ];
        }
    }

    public function getBrandIdByName($brandName)
    {
        $sql = "SELECT markaid FROM urunmarka WHERE markaad = :brandName";
        $params = [
            'brandName' => $brandName
        ];
        $result = $this->db->select($sql, $params);
        if (!empty($result)) {
            return $result[0]['markaid'];
        }
        return 0;
    }

    public function addBrand($addData)
    {

        $sql = "
            INSERT INTO 
                urunmarka 
            SET 
                markaad = :brandName, 
                markaaciklama = :brandDescription,
                marka_logo = :brandImage,
                benzersizid = :brandUniqID,
                markasil = 0,
                markatariholustur = NOW(),
                markatarihguncel = NOW()    
        ";

        $params = [
            'brandName' => $addData['brandName'],
            'brandDescription' => $addData['brandDescription'],
            'brandImage' => $addData['brandImage'],
            'brandUniqID' => $addData['brandUniqID']
        ];

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Marka eklendi',
                'brandID' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Marka eklenirken bir hata oluştu'
            ];
        }
    }

    public function updateBrand($updateData)
    {
        $sql = "
            UPDATE 
                urunmarka 
            SET 
                markaad = :brandName, 
                markaaciklama = :brandDescription,
                marka_logo = :brandImage,
                markatarihguncel = NOW()
            WHERE 
                markaid = :brandID
        ";

        $params = [
            'brandName' => $updateData['brandName'],
            'brandDescription' => $updateData['brandDescription'],
            'brandImage' => $updateData['brandImage'],
            'brandID' => $updateData['brandID']
        ];

        $result = $this->db->update($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Marka Güncellendi'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Marka Güncellenirken bir hata oluştu'
            ];
        }
    }

    public function deleteBrand($brandID)
    {
        $sql = "
            UPDATE 
                urunmarka 
            SET 
                markasil = 1
            WHERE 
                markaid = :brandID
        ";

        $params = [
            'brandID' => $brandID
        ];

        $result = $this->db->update($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Marka silindi'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Marka silinirken bir hata oluştu'
            ];
        }
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