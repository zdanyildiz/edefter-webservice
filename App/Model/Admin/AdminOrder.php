<?php
//Table: uyesiparis
//Columns:
//siparisid int AI PK
//uyeid int
//siparisbenzersizid char(20)
//siparistariholustur datetime(6)
//siparistarihguncelle datetime(6)
//siparisodemeparabirim varchar(3)
//siparisodemetaksit tinyint
//siparisurunidler varchar(500)
//siparisurunadlar varchar(500)
//siparisurunstokkodlar varchar(500)
//siparisurunkategoriler varchar(500)
//siparisurunfiyatlar varchar(500)
//siparisurunadetler varchar(500)
//siparisteslimatad varchar(50)
//siparisteslimatsoyad varchar(50)
//siparisteslimateposta varchar(100)
//siparisteslimatgsm varchar(50)
//siparisteslimattcno char(11)
//siparisteslimatadresulke varchar(50)
//siparisteslimatadressehir varchar(50)
//siparisteslimatadresilce varchar(50)
//siparisteslimatadressemt varchar(50)
//siparisteslimatadresmahalle varchar(50)
//siparisteslimatadrespostakod varchar(10)
//siparisteslimatadresacik varchar(255)
//siparisteslimatadresulkekod varchar(3)
//siparisfaturaunvan varchar(255)
//siparisfaturavergidairesi varchar(100)
//siparisfaturavergino char(12)
//siparisfaturaad varchar(50)
//siparisfaturasoyad varchar(50)
//siparisfaturaeposta varchar(100)
//siparisfaturagsm varchar(50)
//siparisfaturaadresulke varchar(50)
//siparisfaturaadressehir varchar(50)
//siparisfaturaadresilce varchar(50)
//siparisfaturaadressemt varchar(50)
//siparisfaturaadresmahalle varchar(50)
//siparisfaturaadrespostakod varchar(50)
//siparisfaturaadresacik varchar(255)
//siparisfaturaadresulkekod varchar(3)
//kargoid tinyint
//sipariskargofiyat decimal(8,2)
//sipariskargotarih datetime
//sipariskargoserino varchar(64)
//sipariskargodurum varchar(64)
//sipariskargotakip varchar(64)
//siparisteslimatid varchar(64)
//siparisnotalici mediumtext
//siparisnotyonetici mediumtext
//siparistoplamtutar decimal(8,2)
//sipariskdvtutar decimal(8,2)
//sipariskdvsiztutar decimal(8,2)
//sipariskargodahilfiyat decimal(8,2)
//siparistekcekimindirimorani double
//siparistekcekimindirimlifiyat double
//siparishavaleorani double
//siparishavaleindirimlifiyat decimal(8,2)
//sipariskargoindirim double
//sipariskargoindirimaciklama varchar(100)
//siparispuanindirim double
//siparispuanonceki double
//siparispuanharcanan double
//siparispuankazanilan double
//siparispuankalan double
//siparisodemeyontemi varchar(3)
//siparisodemedurum tinyint
//siparisdurum tinyint(1)
//siparisip varchar(15)
//siparisdekont varchar(25)
//kargoCode varchar(1)
//siparisKargoBarcode longtext
//tempBarcodeNumber varchar(30)
//siparisKargoSevkiyatYapildi tinyint(1)
//kargokod char(50)
//siparissil tinyint(1)

//724 (siparisid)
//438 (uyeid)
//SPRR6M00000000000724 (siparisbenzersizid)
//2024-02-23 12:08:40.000000 (siparistariholustur)
//2024-02-23 12:08:40.000000 (siparistarihguncelle)
//TRY (siparisodemeparabirim)
//1 (siparisodemetaksit)
//3073,3072 (siparisurunidler)
//FOTOSEL GÖVDE BAĞLAMA MENGENESİ (20.0000 adet)||FOTOSEL ÇARPRAZ TUTUCU MENGENESİ (40.0000 adet) (siparisurunadlar)
//214.152.012||214.151.012 (siparisurunstokkodlar)
//Fotosel Bağlantı Elemanları||Fotosel Bağlantı Elemanları (siparisurunkategoriler)
//64.41||64.41 (siparisurunfiyatlar)
//20.0000||40.0000 (siparisurunadetler)
//Resul (siparisteslimatad)
//Sağun (siparisteslimatsoyad)
//info@robotdevreleri.com (siparisteslimateposta)
//5534048114 (siparisteslimatgsm)
//36689222716 (siparisteslimattcno)
//212 (siparisteslimatadresulke)
//7 (siparisteslimatadressehir)
//88 (siparisteslimatadresilce)
//271 (siparisteslimatadressemt)
//6401 (siparisteslimatadresmahalle)
//06932 (siparisteslimatadrespostakod)
//PK: 06932 Ahmetkabaklı cad. 430 sok. Emir sitesi A blok . Kat 5 3/9 Sincan/Ankara (siparisteslimatadresacik)
//90 (siparisteslimatadresulkekod)
//Resul sağun. (siparisfaturaunvan)
//Yok (siparisfaturavergidairesi)
//36689222716 (siparisfaturavergino)
//Resul (siparisfaturaad)
//Sağun (siparisfaturasoyad)
//info@robotdevreleri.com (siparisfaturaeposta)
//5534048114 (siparisfaturagsm)
//212 (siparisfaturaadresulke)
//7 (siparisfaturaadressehir)
//88 (siparisfaturaadresilce)
//271 (siparisfaturaadressemt)
//6401 (siparisfaturaadresmahalle)
//06932 (siparisfaturaadrespostakod)
//PK: 06932 Ahmetkabaklı cad. 430 sok. Emir sitesi A blok . Kat 5 3/9 Sincan/Ankara (siparisfaturaadresacik)
//90 (siparisfaturaadresulkekod)
//0 (kargoid)
//0.00 (sipariskargofiyat)
//2024-03-04 07:44:00 (sipariskargotarih)
// (sipariskargoserino)
// (sipariskargodurum)
// (sipariskargotakip)
// (siparisteslimatid)
// (siparisnotalici)
// (siparisnotyonetici)
//3864.60 (siparistoplamtutar)
//772.92 (sipariskdvtutar)
//3091.68 (sipariskdvsiztutar)
//3864.60 (sipariskargodahilfiyat)
//0 (siparistekcekimindirimorani)
//3864.6 (siparistekcekimindirimlifiyat)
//0 (siparishavaleorani)
//3864.60 (siparishavaleindirimlifiyat)
//0 (sipariskargoindirim)
//0.00 (sipariskargoindirimaciklama)
//0 (siparispuanindirim)
//0 (siparispuanonceki)
//0 (siparispuanharcanan)
//0 (siparispuankazanilan)
//0 (siparispuankalan)
//kk (siparisodemeyontemi)
//1 (siparisodemedurum)
//4 (siparisdurum)
//151.135.181.5	(siparisip)
// (siparisdekont)
// (kargoCode)
// (siparisKargoBarcode)
// (tempBarcodeNumber)
//0 (siparissevkiyatyapildi)
// (kargokod)
//0 (siparissil)

//Table: uyesiparisdurum
//Columns:
//siparisdurumid tinyint(1)
//siparisdurumbaslik varchar(100)

//1	Ödeme Onayı Bekleniyor
//2	Siparişiniz Hazırlanıyor
//3	Sipariş Kargoya Teslim Edildi
//4	Teslimat Yapıldı
//5	İade Talebi Alındı
//6	Tamamlanamamış Sipariş
//7	Değişim Talebi Alındı
//8	İptal Talebi Alındı
//9	Tedarik Ediliyor
//0	Kargoya Hazır
//10 İade Alındı
//11 İptal oldu

class AdminOrder
{
    private AdminDatabase $db;
    private Helper $helper;

    public function __construct($db, Config $config)
    {
        $this->db = $db;
        $this->helper = $config->Helper;
    }

    public function getOrders(int $userId): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE uyeid = :userId";
        $params = [
            "userId" => $userId
        ];
        return $this->db->select($sql, $params);
    }

    public function getOrdersBySearchText(string $searchText): array
    {
        $sql = "
            SELECT 
                * 
            FROM 
                uyesiparis 
            WHERE 
                siparissil=0 and 
                (
                    siparisbenzersizid LIKE :searchText or
                    siparisurunadlar LIKE :searchText1 or
                    siparisurunstokkodlar LIKE :searchText2 or
                    siparisteslimatad LIKE :searchText3 or
                    siparisteslimatsoyad LIKE :searchText4 or
                    siparisteslimateposta LIKE :searchText5 or
                    siparisteslimatgsm LIKE :searchText6 or
                    siparisteslimattcno LIKE :searchText7 or
                    siparisfaturaunvan LIKE :searchText8 or
                    siparisfaturavergino LIKE :searchText9
            
                )
            ORDER BY siparistariholustur DESC
            LIMIT 10
        ";

        $params = [
            "searchText" => "%$searchText%",
            "searchText1" => "%$searchText%",
            "searchText2" => "%$searchText%",
            "searchText3" => "%$searchText%",
            "searchText4" => "%$searchText%",
            "searchText5" => "%$searchText%",
            "searchText6" => "%$searchText%",
            "searchText7" => "%$searchText%",
            "searchText8" => "%$searchText%",
            "searchText9" => "%$searchText%"
        ];


        return $this->db->select($sql, $params);
    }

    public function getAllOrders(): array
    {
        $sql = "SELECT * FROM uyesiparis where siparissil=0 order by siparistariholustur desc";
        return $this->db->select($sql);
    }

    public function getOrder(int $orderId): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE siparisid = :orderId";
        $params = [
            "orderId" => $orderId
        ];
        return $this->db->select($sql, $params);
    }

    public function getOrderByOrderUniqID(string $orderUniqID): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE siparisbenzersizid = :orderUniqID";

        $params = [
            "orderUniqID" => $orderUniqID
        ];

        $data = $this->db->select($sql, $params);
        if($data){
            return $data[0];
        }
        return [];
    }

    public function createOrder(array $orderData): bool
    {
        /*$sql = "
            INSERT 
                INTO 
                    uyesiparis (uyeid, siparisbenzersizid, siparistariholustur, siparistarihguncelle, siparisodemeparabirim, siparisodemetaksit, siparisurunidler, siparisurunadlar, siparisurunstokkodlar, siparisurunkategoriler, siparisurunfiyatlar, siparisurunadetler, siparisteslimatad, siparisteslimatsoyad, siparisteslimateposta, siparisteslimatgsm, siparisteslimattcno, siparisteslimatadresulke, siparisteslimatadressehir, siparisteslimatadresilce, siparisteslimatadressemt, siparisteslimatadresmahalle, siparisteslimatadrespostakod, siparisteslimatadresacik, siparisteslimatadresulkekod, siparisfaturaunvan, siparisfaturavergidairesi, siparisfaturavergino, siparisfaturaad, siparisfaturasoyad, siparisfaturaeposta, siparisfaturagsm, siparisfaturaadresulke, siparisfaturaadressehir, siparisfaturaadresilce, siparisfaturaadressemt, siparisfaturaadresmahalle, siparisfaturaadrespostakod, siparisfaturaadresacik, siparisfaturaadresulkekod, kargoid, sipariskargofiyat, sipariskargotarih, sipariskargoserino, sipariskargodurum, sipariskargotakip, siparisteslimatid, siparisnotalici, siparisnotyonetici, siparistoplamtutar, sipariskdvtutar, sipariskdvsiztutar, sipariskargodahilfiyat, siparistekcekimindirimorani, siparistekcekimindirimlifiyat, siparishavaleorani, siparishavaleindirimlifiyat, sipariskargoindirim, sipariskargoindirimaciklama, siparispuanindirim, siparispuanonceki, siparispuanharcanan, siparispuankazanilan, siparispuankalan, siparisodemeyontemi, siparisodemedurum, siparisdurum, siparisip, siparisdekont, kargoCode, siparisKargoBarcode, tempBarcodeNumber, siparisKargoSevkiyatYapildi, kargokod, siparissil, languageCode) 
                    VALUES (:uyeid, :siparisbenzersizid, :siparistariholustur, :siparistarihguncelle, :siparisodemeparabirim, :siparisodemetaksit, :siparisurunidler, :siparisurunadlar, :siparisurunstokkodlar, :siparisurunkategoriler, :siparisurunfiyatlar, :siparisurunadetler, :siparisteslimatad, :siparisteslimatsoyad, :siparisteslimateposta, :siparisteslimatgsm, :siparisteslimattcno, :siparisteslimatadresulke, :siparisteslimatadressehir, :siparisteslimatadresilce, :siparisteslimatadressemt, :siparisteslimatadresmahalle, :siparisteslimatadrespostakod, :siparisteslimatadresacik, :siparisteslimatadresulkekod, :siparisfaturaunvan, :siparisfaturavergidairesi, :siparisfaturavergino, :siparisfaturaad, :siparisfaturasoyad, :siparisfaturaeposta, :siparisfaturagsm, :siparisfaturaadresulke, :siparisfaturaadressehir, :siparisfaturaadresilce, :siparisfaturaadressemt, :siparisfaturaadresmahalle, :siparisfaturapostakod, :siparisfaturaadresacik, :siparisfaturaadresulkekod, :kargoid, :sipariskargofiyat, :sipariskargotarih, :sipariskargoserino, :sipariskargodurum, :sipariskargotakip, :siparisteslimatid, :siparisnotalici, :siparisnotyonetici, :siparistoplamtutar, :sipariskdvtutar, :sipariskdvsiztutar, :sipariskargodahilfiyat, :siparistekcekimindirimorani, :siparistekcekimindirimlifiyat, :siparishavaorani, :siparishavaleindirimlifiyat, :sipariskargoindirim, :sipariskargoindirimaciklama, :siparispuanindirim, :siparispuanonceki, :siparispuanharcanan, :siparispuankazanilan, :siparispuankalan, :siparisodemeyontemi, :siparisodemedurum, :siparisdurum, :siparisip, :siparisdekont, :kargoCode, :siparisKargoBarcode, :tempBarcodeNumber, :siparissevkiyatyapildi, :kargokod, :siparissil, :languageCode)
        ";*/
        //$orderdata2yı foreach ile dönerek sql sorgusunu oluşturabiliriz.
        $insertFields = "";
        $insertValues = "";
        foreach ($orderData as $field => $value) {
            $insertFields .= "$field, ";
            $insertValues .= ":$field, ";
        }

        // Son virgülleri kaldıralım
        $insertFields = rtrim($insertFields, ", ");
        $insertValues = rtrim($insertValues, ", ");

        // SQL sorgumuzu oluşturalım
        $sql = "INSERT INTO uyesiparis ($insertFields) VALUES ($insertValues)";

        $result = $this->db->insert($sql, $orderData);

        if ($result) {
            return true;
        }

        return false;
    }

    public function updateOrder(string $orderUniqID, array $updateData): bool
    {
        // Güncellenecek alanları ve değerleri SQL sorgusuna ekleyelim
        $updateFields = "";
        foreach ($updateData as $field => $value) {
            $updateFields .= "$field = :$field, ";
        }
        // Son virgülü kaldıralım
        $updateFields = rtrim($updateFields, ", ");

        // SQL sorgumuzu oluşturalım
        $sql = "UPDATE uyesiparis SET $updateFields WHERE siparisbenzersizid = :orderUniqID";

        // Parametrelerimizi oluşturalım
        $params = array_merge($updateData, ["orderUniqID" => $orderUniqID]);

        //Log::adminWrite("updateOrder SQL: $sql | Params: ".json_encode($params), "info");
        // Sorgumuzu çalıştıralım
        return $this->db->update($sql, $params);

    }

    public function deleteOrder(int $orderId): bool
    {
        $sql = "DELETE FROM uyesiparis WHERE siparisid = :orderId";
        $params = [
            "orderId" => $orderId
        ];
        return $this->db->delete($sql, $params);
    }

    public function getOrderLanguageCode($orderUniqID){
        $sql = "SELECT languagecode FROM uyesiparis WHERE siparisbenzersizid = :orderUniqID";
        $params = [
            "orderUniqID" => $orderUniqID
        ];
        $result = $this->db->select($sql, $params);
        if($result){
            return $result[0]["languagecode"];
        }
        return false;
    }

    public function getOrderStatuses(): array
    {
        $sql = "SELECT * FROM uyesiparisdurum";
        return $this->db->select($sql);
    }

    public function createOrderStatus(array $statusData): bool
    {
        $sql = "INSERT INTO uyesiparisdurum (siparisdurumid, siparisdurumbaslik) VALUES (:statusId, :statusTitle)";
        return $this->db->insert($sql, $statusData);
    }

    public function getOrderStatus(int $statusId): array
    {
        $sql = "SELECT * FROM uyesiparisdurum WHERE siparisdurumid = :statusId";
        $params = [
            "statusId" => $statusId
        ];

        $data = $this->db->select($sql, $params);
        if($data){
            return $data[0];
        }
        return [];
    }

    public function updateOrderStatus(int $orderId, int $statusId): bool
    {
        $sql = "UPDATE uyesiparis SET siparisdurum = :statusId WHERE siparisid = :orderId";
        $params = [
            "orderId" => $orderId,
            "statusId" => $statusId
        ];
        return $this->db->update($sql, $params);
    }

    public function updateOrderStatusByUniqID(string $orderUniqId, int $statusId)
    {
        $sql = "UPDATE uyesiparis SET siparisdurum = :statusId WHERE siparisbenzersizid = :orderUniqId";
        $params = [
            "orderUniqId" => $orderUniqId,
            "statusId" => $statusId
        ];

        return $this->db->update($sql, $params);
    }

    public function createOrderUniqID(): string
    {
        //siparis benzersiz id 20 haneden oluşmalıdır.
        //SPR ile başlamalıdır.
        //uyesiparis tablosundaki otomatik sayı en son kaç ise benzersizid sonu +1 olmalıdır.
        //Kalan karakterlerin arası SPR.....727 0 ile doldurulmalıdır.

        $sql = "SELECT siparisid FROM uyesiparis ORDER BY siparisid DESC LIMIT 1";
        $lastOrder = $this->db->select($sql);
        $lastOrderId = $lastOrder[0]["siparisid"];
        $lastOrderId++;
        $limit = 20;
        $lastOrderIDLenght = strlen($lastOrderId);
        $newKey = "SPR".$this->helper->createPassword(3,1);

        $diff = ($limit - $lastOrderIDLenght) - strlen($newKey);

        $lastOrderId = str_pad($lastOrderId, $diff, "0", STR_PAD_LEFT);
        return $newKey.$lastOrderId;
    }

    public function updateOrderPaymentStatus($orderUniqID,$orderPaymentStatus){
        $sql = "UPDATE uyesiparis SET siparisodemedurum = :orderPaymentStatus WHERE siparisbenzersizid = :orderUniqID";
        $params = [
            "orderUniqID" => $orderUniqID,
            "orderPaymentStatus" => $orderPaymentStatus
        ];
        return $this->db->update($sql, $params);
    }

    public function getOrdersByStatus(int $statusId): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE siparissil=0 and siparisdurum = :statusId";
        $params = [
            "statusId" => $statusId
        ];
        $result = $this->db->select($sql, $params);
        if($result){
            return $result;
        }
        return [];
    }

    public function getOrdersByPaymentType(string $paymentType): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE siparissil=0 and siparisodemeyontemi = :paymentType";
        $params = [
            "paymentType" => $paymentType
        ];
        $result = $this->db->select($sql, $params);
        if($result){
            return $result;
        }
        return [];
    }

    public function getOrdersByPaymentTypeAndOrderStatus(string $paymentType, int $orderStatus): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE siparissil=0 and siparisodemeyontemi = :paymentType AND siparisdurum = :orderStatus order by siparistariholustur desc";
        $params = [
            "paymentType" => $paymentType,
            "orderStatus" => $orderStatus
        ];
        $result = $this->db->select($sql, $params);
        if($result){
            return $result;
        }
        return [];
    }

    public function getOrdersByPaymentStatusAndOrderStatus(int $paymentStatus, int $orderStatus): array
    {
        $sql = "SELECT * FROM uyesiparis WHERE siparissil=0 and siparisodemedurum = :paymentStatus AND siparisdurum = :orderStatus order by siparistariholustur desc";
        $params = [
            "paymentStatus" => $paymentStatus,
            "orderStatus" => $orderStatus
        ];
        $result = $this->db->select($sql, $params);
        if($result){
            return $result;
        }
        return [];
    }

    public function getOrdersByPaymentTypeAndOrderStatusCount(string $paymentType, int $orderStatus): int
    {
        $sql = "SELECT COUNT(*) as total FROM uyesiparis WHERE siparissil=0 and siparisodemeyontemi = :paymentType AND siparisdurum = :orderStatus";
        $params = [
            "paymentType" => $paymentType,
            "orderStatus" => $orderStatus
        ];
        $result = $this->db->select($sql, $params);

        if($result){
            return $result[0]["total"];
        }
        return 0;
    }

    public function getOrdersByPaymentStatusAndOrderStatusCount(int $paymentStatus, int $orderStatus): int
    {
        $sql = "SELECT COUNT(*) as total FROM uyesiparis WHERE siparissil=0 and siparisodemedurum = :paymentStatus AND siparisdurum = :orderStatus";
        $params = [
            "paymentStatus" => $paymentStatus,
            "orderStatus" => $orderStatus
        ];
        $result = $this->db->select($sql, $params);
        if($result){
            return $result[0]["total"];
        }
        return 0;
    }

    //en çok sipariş verilen 10 şehri getirelim

    public function getMostOrderedCities(): array
    {
        $sql = "
            SELECT 
                siparisteslimatadressehir, COUNT(siparisteslimatadressehir) as total 
            FROM 
                uyesiparis 
            WHERE 
                siparissil=0 and siparisdurum=4
            GROUP BY 
                siparisteslimatadressehir 
            ORDER BY 
                total DESC LIMIT 10";
        return $this->db->select($sql);
    }

    //en çok alışveri yapan üyeye göre toplam siparis sayılarını getirelim

    public function getMostOrderedUsers(): array
    {
        $sql = "
            SELECT 
                siparisfaturaunvan, COUNT(uyeid) as total 
            FROM 
                uyesiparis 
            WHERE 
                siparissil=0 and siparisdurum=4
            GROUP BY 
                uyeid 
            ORDER BY 
                total DESC 
            LIMIT 10";
        return $this->db->select($sql);
    }

    //transaction
    public function beginTransaction($funcName = "")
    {
        if (!$this->inTransaction($funcName)){
            Log::adminWrite("Aktif transaction Yok");
            $this->db->beginTransaction($funcName);
            return;
        }
        Log::adminWrite("Aktif transaction var","warning");
    }

    public function commit($funcName="")
    {
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }

    public function inTransaction($funcName="")
    {
        return $this->db->inTransaction($funcName);
    }
}