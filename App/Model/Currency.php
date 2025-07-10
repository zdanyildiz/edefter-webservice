<?php

class Currency
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getCurrencies()
    {
        $sql = "
            SELECT 
                parabirimid as currencyID,
                parabirimad as currencyName,
                parabirimsimge as currencySymbol,
                parabirimkod as currencyCode,
                parabirimkur as currencyRate,
                parabirimkurtarih as currencyRateDate
            FROM 
                urunparabirim
            WHERE 
                parabirimsil = 0
        ";
        $result = $this->db->select($sql);

        if ($result) {
            return $result;
        }
        return [];
    }
    public function getCurrencySymbolOrCode($currencyId, $type = 'symbol')
    {
        $sql = "
            SELECT 
                parabirimsimge, parabirimkod 
            FROM 
                urunparabirim 
            WHERE 
                parabirimid = :currencyId
        ";
        $result = $this->db->select($sql, ['currencyId' => $currencyId]);

        if ($result) {
            return $type == 'symbol' ? $result[0]['parabirimsimge'] : $result[0]['parabirimkod'];
        }

        return null;
    }

    public function updateCurrencyRates()
    {
        //son güncelleme tarihini alalım, üzerinden 6 saat geçmişse güncellemeye devam edelim
        $lastUpdateDate = $this->db->select("SELECT MAX(parabirimkurtarih) as lastUpdate FROM urunparabirim")[0]['lastUpdate'];
        $lastUpdateDate = date('Y-m-d H:i:s', strtotime($lastUpdateDate));
        $lastUpdateDate = date('Y-m-d H:i:s', strtotime($lastUpdateDate));
        $currentDate = date('Y-m-d H:i:s');

        $diff = strtotime($currentDate) - strtotime($lastUpdateDate);
        if($diff < 21600){
            return [];
        }

        // Fetch all currencies using the getCurrencies method
        $currencies = $this->getCurrencies();

        try {
            // Attempt to load the XML data
            $connect_web = @simplexml_load_file('http://www.tcmb.gov.tr/kurlar/today.xml');

            // Check if XML data is loaded successfully
            if ($connect_web === false) {
                // XML data could not be loaded; do not proceed with updates
                return [];
            }

            $now = date('Y-m-d H:i:s'); // Current date and time

            // Create a mapping of CurrencyCode to BanknoteSelling from XML data
            $xmlCurrencies = [];
            foreach ($connect_web->Currency as $xmlCurrency) {
                $currencyCode = (string)$xmlCurrency['CurrencyCode'];
                $banknoteSelling = (string)$xmlCurrency->BanknoteSelling;

                // Handle possible empty BanknoteSelling
                if (empty($banknoteSelling)) {
                    $banknoteSelling = 1;
                } else {
                    $banknoteSelling = str_replace(",", ".", $banknoteSelling);
                    $banknoteSelling = number_format((float)$banknoteSelling, 2, '.', '');
                }
                $xmlCurrencies[$currencyCode] = $banknoteSelling;
            }

            // Begin database transaction
            $this->db->beginTransaction();

            // Iterate over each currency and update rates
            foreach ($currencies as $currency) {
                $currencyCode = $currency['currencyCode'];
                $currencyID = $currency['currencyID'];

                if (isset($xmlCurrencies[$currencyCode])) {
                    $banknoteSelling = $xmlCurrencies[$currencyCode];
                } else {
                    // Currency code not found in XML data; set rate to 1
                    $banknoteSelling = 1;
                }

                // Update the rate and date using the new method
                $updateResult = $this->updateCurrencyRateByCurrencyID($currencyID, $banknoteSelling, $now);

                // Check if the update was successful
                if ($updateResult === -1) {
                    // If update failed, throw an exception to rollback the transaction
                    throw new Exception("Failed to update currency with ID: $currencyID");
                }
            }

            // Commit the transaction
            $this->db->commit();

        } catch (Exception $e) {
            // Roll back the transaction in case of any error
            $this->db->rollback();
            // Log the error
            Log::adminWrite("Error updating currency rates: {$e->getMessage()}", "error");
            // Do not produce errors; just return an empty array
            return [];
        }

        // Fetch updated currency rates from the database
        $updatedCurrencies = $this->getCurrencies();

        // Prepare the return array with updated rates
        $rates = [];
        foreach ($updatedCurrencies as $currency) {
            $rates[$currency['currencyCode']] = $currency['currencyRate'];
        }

        return $rates;
    }
    public function updateCurrencyRateByCurrencyID($currencyID, $rate, $date)
    {
        $sql = "UPDATE urunparabirim SET parabirimkur = ?, parabirimkurtarih = ? WHERE parabirimid = ?";
        $params = [$rate, $date, $currencyID];

        return $this->db->update($sql, $params);
    }


    public function getCurrentRates($currencyId)
    {
        $sql = "
            SELECT 
                parabirimkur
            FROM 
                urunparabirim
            WHERE 
                parabirimid = :currencyId
        ";
        $result = $this->db->select($sql, ['currencyId' => $currencyId]);

        return ($result && !is_null($result[0]['parabirimkur'])) ? $result[0]['parabirimkur'] : 1;
    }
}