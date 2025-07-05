<?php
/*
 * dilid
fiyatgoster
fiyatbayigoster
eskifiyat
parabirim
taksit
kdv
kredikarti
kapidaodeme
havale
tekcekim_indirim_orani
havale_indirim_orani
 */
class AdminPriceSettings
{
    private AdminDatabase $db;
    private int $languageID;

    public function __construct($db,$languageID)
    {
        $this->db = $db;
        $this->languageID = $languageID;
    }

    public function getPriceSettings()
    {
        $query = "
            SELECT 
                dilid as languageID,
                fiyatgoster as showPriceStatus,
                fiyatbayigoster as showPriceToDealer,
                eskifiyat as showOldPrice,
                parabirim as currencyID,
                taksit as installmentStatus,
                kdv as taxRate,
                tekcekim_indirim_orani as singlePaymentDiscountRate,
                havale_indirim_orani as bankTransferDiscountRate,
                kredikarti as creditCardStatus,
                kapidaodeme as cashOnDeliveryStatus,
                havale as bankTransferStatus
            
            FROM 
                ayarfiyat 
            WHERE 
                dilid = :languageID
        ";
        $params = array("languageID" => $this->languageID);

        $result = $this->db->select($query,$params);

        if($result)
        {
            $result = $result[0];

            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        else
        {
            return [
                'status' => 'error',
                'message' => 'Price settings not found'
            ];
        }
    }

    public function addOrUpdatePriceSettings($addData){
        //languageID'ye göre kontrol edeceğiz,gelen languageID varsa update yoksa yeni giriş yapacağız
        //on duplicate key update kullanacağız

        $query = "
            INSERT INTO ayarfiyat 
            SET 
                dilid = :languageID,
                fiyatgoster = :showPriceStatus,
                fiyatbayigoster = :showPriceToDealer,
                eskifiyat = :showOldPrice,
                parabirim = :currencyID,
                taksit = :installmentStatus,
                kdv = :taxRate,
                tekcekim_indirim_orani = :singlePaymentDiscountRate,
                havale_indirim_orani = :bankTransferDiscountRate,
                kredikarti = :creditCardStatus,
                kapidaodeme = :cashOnDeliveryStatus,
                havale = :bankTransferStatus
            ON DUPLICATE KEY UPDATE
                fiyatgoster = :showPriceStatus1,
                fiyatbayigoster = :showPriceToDealer1,
                eskifiyat = :showOldPrice1,
                parabirim = :currencyID1,
                taksit = :installmentStatus1,
                kdv = :taxRate1,
                tekcekim_indirim_orani = :singlePaymentDiscountRate1,
                havale_indirim_orani = :bankTransferDiscountRate1,
                kredikarti = :creditCardStatus1,
                kapidaodeme = :cashOnDeliveryStatus1,
                havale = :bankTransferStatus1
        ";

        $params = $addData;
        $params["showPriceStatus1"] = $addData["showPriceStatus"];
        $params["showPriceToDealer1"] = $addData["showPriceToDealer"];
        $params["showOldPrice1"] = $addData["showOldPrice"];
        $params["currencyID1"] = $addData["currencyID"];
        $params["installmentStatus1"] = $addData["installmentStatus"];
        $params["taxRate1"] = $addData["taxRate"];
        $params["singlePaymentDiscountRate1"] = $addData["singlePaymentDiscountRate"];
        $params["bankTransferDiscountRate1"] = $addData["bankTransferDiscountRate"];
        $params["creditCardStatus1"] = $addData["creditCardStatus"];
        $params["cashOnDeliveryStatus1"] = $addData["cashOnDeliveryStatus"];
        $params["bankTransferStatus1"] = $addData["bankTransferStatus"];

        $result = $this->db->insert($query,$params);

        if(!$result)
        {
            return [
                'status' => 'error',
                'message' => 'Bir sorun oluştu, daha sonra tekrar deneyin'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Fiyat Ayarları güncellendi'
        ];
    }

    public function updatePriceSettings($updateData)
    {
        $query = "
            UPDATE ayarfiyat 
            SET 
                fiyatgoster = :showPriceStatus,
                fiyatbayigoster = :showPriceToDealer,
                eskifiyat = :showOldPrice,
                parabirim = :currencyID,
                taksit = :installmentStatus,
                kdv = :taxRate,
                tekcekim_indirim_orani = :singlePaymentDiscountRate,
                havale_indirim_orani = :bankTransferDiscountRate,
                kredikarti = :creditCardStatus,
                kapidaodeme = :cashOnDeliveryStatus,
                havale = :bankTransferStatus
            WHERE 
                dilid = :languageID
        ";

        $params = $updateData;

        $result = $this->db->update($query,$params);

        if($result<0)
        {
            return [
                'status' => 'error',
                'message' => 'Bir sorun oluştu, daha sonra tekrar deneyin'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Fiyat Ayarları güncellendi'
        ];

    }

    public function addPriceSettings($addData)
    {
        $query = "
            INSERT INTO ayarfiyat 
            SET 
                dilid = :languageID,
                fiyatgoster = :showPriceStatus,
                fiyatbayigoster = :showPriceToDealer,
                eskifiyat = :showOldPrice,
                parabirim = :currencyID,
                taksit = :installmentStatus,
                kdv = :taxRate,
                kredikarti = :creditCardStatus,
                kapidaodeme = :cashOnDeliveryStatus,
                havale = :bankTransferStatus,
                tekcekim_indirim_orani = :singlePaymentDiscountRate,
                havale_indirim_orani = :bankTransferDiscountRate
        ";

        $params = $addData;

        $result = $this->db->insert($query,$params);

        if(!$result)
        {
            return [
                'status' => 'error',
                'message' => 'Bir sorun oluştu, daha sonra tekrar deneyin.'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Fiyat Ayarları güncellendi'
        ];
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