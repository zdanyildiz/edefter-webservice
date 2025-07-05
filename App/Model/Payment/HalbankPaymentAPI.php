<?php

class HalkbankPaymentAPI
{
    private $apiUrl;
    private $merchantId;
    private $terminalId;
    private $username;
    private $password;
    private $hashKey;

    public function __construct($config)
    {
        $this->apiUrl = $config['apiUrl'];
        $this->merchantId = $config['merchantId'];
        $this->terminalId = $config['terminalId'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->hashKey = $config['hashKey'];
    }

    // Ödeme Al
    public function makePayment($cardInfo, $amount, $orderId)
    {
        $hashData = $this->generateHash($orderId, $amount);
        $postData = [
            'MerchantId' => $this->merchantId,
            'TerminalId' => $this->terminalId,
            'UserName' => $this->username,
            'Password' => $this->password,
            'CardNumber' => $cardInfo['cardNumber'],
            'CardExpiryDate' => $cardInfo['expiryDate'],
            'CardCVV' => $cardInfo['cvv'],
            'Amount' => $amount,
            'OrderId' => $orderId,
            'HashData' => $hashData,
        ];

        return $this->sendRequest('/makePayment', $postData);
    }

    // Ödeme Kontrol
    public function checkPayment($orderId)
    {
        $hashData = $this->generateHash($orderId);
        $postData = [
            'MerchantId' => $this->merchantId,
            'TerminalId' => $this->terminalId,
            'UserName' => $this->username,
            'Password' => $this->password,
            'OrderId' => $orderId,
            'HashData' => $hashData,
        ];

        return $this->sendRequest('/checkPayment', $postData);
    }

    // Ödeme İadesi
    public function refundPayment($orderId, $refundAmount)
    {
        $hashData = $this->generateHash($orderId, $refundAmount);
        $postData = [
            'MerchantId' => $this->merchantId,
            'TerminalId' => $this->terminalId,
            'UserName' => $this->username,
            'Password' => $this->password,
            'OrderId' => $orderId,
            'RefundAmount' => $refundAmount,
            'HashData' => $hashData,
        ];

        return $this->sendRequest('/refundPayment', $postData);
    }

    // Hash Üretimi
    private function generateHash($orderId, $amount = null)
    {
        $data = $this->merchantId . $this->terminalId . $orderId;
        if ($amount !== null) {
            $data .= $amount;
        }
        $data .= $this->hashKey;
        return base64_encode(hash('sha256', $data, true));
    }

    // API isteği gönderme
    private function sendRequest($endpoint, $postData)
    {
        $ch = curl_init($this->apiUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}

// Kullanım Örneği
$config = [
    'apiUrl' => 'https://api.halkbank.com/payment',
    'merchantId' => 'MERCHANT_ID',
    'terminalId' => 'TERMINAL_ID',
    'username' => 'API_USERNAME',
    'password' => 'API_PASSWORD',
    'hashKey' => 'HASH_KEY',
];

$halkbankPayment = new HalkbankPaymentAPI($config);

try {
    // Ödeme yapma örneği
    $cardInfo = [
        'cardNumber' => '1234567812345678',
        'expiryDate' => '1225',
        'cvv' => '123'
    ];
    $response = $halkbankPayment->makePayment($cardInfo, 100.50, 'ORDER1234');
    print_r($response);
} catch (Exception $e) {
    echo 'Hata: ' . $e->getMessage();
}
?>
