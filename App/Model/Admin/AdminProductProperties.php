<?php
/**
 * urunekozellikler
 * ekozellikid
 * ekozellikad
 * ekozellikdeger
 * ekozelliksil
 */

class AdminProductProperties{

    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getProductProperties()
    {
        $sql = "
            SELECT 
                ekozellikid as productPropertyID, ekozellikad as productPropertyName, ekozellikdeger as productPropertyValue
            FROM 
                urunekozellikler 
            WHERE 
                ekozelliksil = 0
        ";

        $result = $this->db->select($sql);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Product properties not found'
            ];
        }
    }

    public function addProductProperty($productPropertyName, $productPropertyValue)
    {
        $sql = "
            INSERT INTO 
                urunekozellikler 
            SET 
                ekozellikad = :productPropertyName, 
                ekozellikdeger = :productPropertyValue,
                ekozelliksil = 0
        ";

        $params = [
            'productPropertyName' => $productPropertyName,
            'productPropertyValue' => $productPropertyValue
        ];

        $result = $this->db->insert($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Product property added',
                'productPropertyID' => $result
            ];
        }
        else {
            return [
                'status' => 'error',
                'message' => 'Product property not added'
            ];
        }
    }

    public function updateProductProperty($productPropertyID, $productPropertyName, $productPropertyValue)
    {
        $sql = "
            UPDATE 
                urunekozellikler 
            SET 
                ekozellikad = :productPropertyName, 
                ekozellikdeger = :productPropertyValue
            WHERE 
                ekozellikid = :productPropertyID
        ";

        $params = [
            ':productPropertyName' => $productPropertyName,
            ':productPropertyValue' => $productPropertyValue,
            ':productPropertyID' => $productPropertyID
        ];

        $this->db->beginTransaction();

        $result = $this->db->update($sql, $params);

        if ($result) {
            $this->db->commit();
            return [
                'status' => 'success',
                'message' => 'Product property updated'
            ];
        } else {
            $this->db->rollBack();
            return [
                'status' => 'error',
                'message' => 'Product property not updated'
            ];
        }
    }

    public function deleteProductProperty($productPropertyID)
    {
        $sql = "
            UPDATE 
                urunekozellikler 
            SET 
                ekozelliksil = 1
            WHERE 
                ekozellikid = :productPropertyID
        ";

        $params = [
            ':productPropertyID' => $productPropertyID
        ];

        $this->db->beginTransaction();

        $result = $this->db->update($sql, $params);

        if ($result) {
            $this->db->commit();
            return [
                'status' => 'success',
                'message' => 'Product property deleted'
            ];
        } else {
            $this->db->rollBack();
            return [
                'status' => 'error',
                'message' => 'Product property not deleted'
            ];
        }
    }

    public function getProductPropertyByID($productPropertyID)
    {
        $sql = "
            SELECT 
                ekozellikid as productPropertyID, ekozellikad as productPropertyName, ekozellikdeger as productPropertyValue
            FROM 
                urunekozellikler 
            WHERE 
                ekozellikid = :productPropertyID
        ";

        $params = [
            ':productPropertyID' => $productPropertyID
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Product property not found'
            ];
        }
    }

    //özellik ya dsa değer arayalım
    public function searchProductProperty($search, $lang = 'tr')
    {

        $sql = "
            SELECT 
                urunekozellikler.ekozellikid AS productPropertyID, 
                urunekozellikler.ekozellikad AS productPropertyName,
                urunekozellikler.ekozellikdeger AS productPropertyValue
            FROM
                urunekozellikler
            WHERE 
                (urunekozellikler.ekozellikad LIKE :search
                OR urunekozellikler.ekozellikdeger LIKE :search1)
                AND urunekozellikler.ekozelliksil = 0
        ";


        $params = [
            ':search' => '%' . $search . '%',
            ':search1' => '%' . $search . '%'
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Product property not found'
            ];
        }
    }

    public function checkProductProperty($productPropertyName, $productPropertyValue){
        $sql = "
            SELECT 
                ekozellikid as productPropertyID
            FROM 
                urunekozellikler 
            WHERE 
                ekozellikad = :productPropertyName
                AND ekozellikdeger = :productPropertyValue
                AND ekozelliksil = 0
        ";

        $params = [
            ':productPropertyName' => $productPropertyName,
            ':productPropertyValue' => $productPropertyValue
        ];

        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Product property exists'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Product property not exists'
            ];
        }
    }
}