<?php
class AdminProductQuantityUnit
{
    
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    public function getQuantityUnits()
    {
        $sql = "
            SELECT 
                urunmiktarbirimid, urunmiktarbirimadi 
            FROM 
                urunmiktarbirim
            WHERE 
                urunmiktarbirimsil = 0
        ";
        $result = $this->db->select($sql);

        if ($result) {
            //sütun isimlerini ingilizceye çevirelim
            $result = array_map(function ($item) {
                return array(
                    'quantityUnitID' => $item['urunmiktarbirimid'],
                    'quantityUnitName' => $item['urunmiktarbirimadi']
                );
            }, $result);
            return $result;

        }
        return [];
    }

    public function getQuantityUnit($quantityUnitID)
    {
        $sql = "
            SELECT 
                urunmiktarbirimid, urunmiktarbirimadi 
            FROM 
                urunmiktarbirim 
            WHERE 
                urunmiktarbirimid = :quantityUnitID AND urunmiktarbirimsil = 0
        ";
        $params = array(
            'quantityUnitID' => $quantityUnitID
        );
        $result = $this->db->select($sql, $params);

        if ($result) {
            return [
                'quantityUnitID' => $result[0]['urunmiktarbirimid'],
                'quantityUnitName' => $result[0]['urunmiktarbirimadi']
            ];
        }
        return [];
    }
    
}