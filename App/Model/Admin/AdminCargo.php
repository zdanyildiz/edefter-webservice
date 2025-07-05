<?php
/**
 * kargo
 * kargoid
 * kargoad
 * kargoCode
 * kargoaciklama
 * kargoresim
 * kargotakiplink
 * benzersizid
 * kargosil
 */
/*
 * kargoucret
kargoucretid
kargosabitucret
kargoucretsiz
kargogonderimtipi
kargosuresi
kargokapidaek
kargourunadet
 */
/*
 * kargobolgeleri
kargobolgeid
kargobolgead
 */
/*
 * kargodesiayar
kargodesiayarid
kargoid
kargobolgeid
desi0
desi1
desi2
desi3
desi4
desi5
desi6
desi7
desi8
desi9
desi10
desi11
desi12
desi13
desi14
desi15
desi16
desi17
desi18
desi19
desi20
desi21
desi22
desi23
desi24
desi25
desi26
desi27
desi28
desi29
desi30
desi31
desi32
desi33
desi34
desi35
desi36
desi37
desi38
desi39
desi40
desi41
desi42
desi43
desi44
desi45
desi46
desi47
desi48
desi49
desi50
 */
/*
 * kargosehirler
kargosehirid
kargobolgeid
kargosehirad
kargosehiraktif
 */

class AdminCargo{

    private AdminDatabase $db;

    public function __construct($db){
        $this->db = $db;

        //getCargoPrice(); kontrol edelim eğer boş ise varsayılan veri ekleyelim
        //kargoucretid=1
        //kargosabitucret=0
        //kargoucretsiz=0
        //kargogonderimtipi=0
        //kargosuresi=3
        //kargokapidaek=0
        //kargourunadet=0

        $cargoPrice = $this->getCargoPrice();
        if(empty($cargoPrice)){
            $this->addCargoPrice([
                "fixedPrice" => 0,
                "freeCargo" => 0,
                "cargoType" => 0,
                "cargoTime" => 3,
                "cashOnDelivery" => 0,
                "productCount" => 0
            ]);
        }
    }

    public function getCargos(){
        $query = "SELECT * FROM kargo WHERE kargosil='0' ORDER BY kargoad";
        $data = $this->db->select($query);

        if(!empty($data)){
            //sütun isimlerini ingilizce yapalım
            $data = array_map(function($item){
                $item["cargoID"] = $item["kargoid"];
                $item["cargoName"] = $item["kargoad"];
                $item["cargoCode"] = $item["kargoCode"];
                $item["cargoDescription"] = $item["kargoaciklama"];
                $item["cargoImage"] = $item["kargoresim"];
                $item["cargoTrackingLink"] = $item["kargotakiplink"];
                $item["cargoUniqueId"] = $item["benzersizid"];
                $item["cargoDeleted"] = $item["kargosil"];
                return $item;
            }, $data);
            return $data;
        }else{
            return [];
        }
    }

    public function getCargo($cargoID){
        $query = "SELECT * FROM kargo WHERE kargoid=?";
        $data = $this->db->select($query, [$cargoID]);

        if(!empty($data)){
            $data = array_map(function($item){
                $item["cargoID"] = $item["kargoid"];
                $item["cargoName"] = $item["kargoad"];
                $item["cargoCode"] = $item["kargoCode"];
                $item["cargoDescription"] = $item["kargoaciklama"];
                $item["cargoImage"] = $item["kargoresim"];
                $item["cargoTrackingLink"] = $item["kargotakiplink"];
                $item["cargoUniqueId"] = $item["benzersizid"];
                $item["cargoDeleted"] = $item["kargosil"];
                return $item;
            }, $data);
            return $data[0];
        }else{
            return [];
        }
    }

    public function addCargo($cargoData){

        $query = "INSERT INTO kargo SET 
            kargoad=?,
            kargoCode=?,
            kargoaciklama=?,
            kargoresim=?,
            kargotakiplink=?,
            benzersizid=?,
            kargosil=?";

        $this->db->beginTransaction();

        $insert = $this->db->insert($query, [
            $cargoData["cargoName"],
            $cargoData["cargoCode"],
            $cargoData["cargoDescription"],
            $cargoData["cargoImage"],
            $cargoData["cargoTrackingLink"],
            $cargoData["cargoUniqueId"],
            $cargoData["cargoDeleted"]
        ]);

        if($insert){
            $this->db->commit();
            return $insert;
        }else{
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCargo($cargoData){

        $query = "UPDATE kargo SET 
            kargoad=?,
            kargoCode=?,
            kargoaciklama=?,
            kargoresim=?,
            kargotakiplink=?,
            benzersizid=?,
            kargosil=?
            WHERE kargoid=?";

        $this->db->beginTransaction();

        $update = $this->db->update($query, [
            $cargoData["cargoName"],
            $cargoData["cargoCode"],
            $cargoData["cargoDescription"],
            $cargoData["cargoImage"],
            $cargoData["cargoTrackingLink"],
            $cargoData["cargoUniqueId"],
            $cargoData["cargoDeleted"],
            $cargoData["cargoID"]
        ]);

        if($update){
            $this->db->commit();
            return $update;
        }else{
            $this->db->rollBack();
            return false;
        }
    }

    public function getCargoAreas(){
        $query = "SELECT * FROM kargobolgeleri ";
        $data = $this->db->select($query, []);

        if(!empty($data)){
            //sütun isimlerini ingilizce yapalım
            $data = array_map(function($item){
                $item["areaID"] = $item["kargobolgeid"];
                $item["areaName"] = $item["kargobolgead"];
                return $item;
            }, $data);
            return $data;
        }else{
            return [];
        }
    }

    public function getCargoArea($areaID){
        $query = "SELECT * FROM kargobolgeleri WHERE kargobolgeid=?";
        $data = $this->db->select($query, [$areaID]);

        if(!empty($data)){
            $data = array_map(function($item){
                $item["areaID"] = $item["kargobolgeid"];
                $item["areaName"] = $item["kargobolgead"];
                return $item;
            }, $data);
            return $data[0];
        }else{
            return [];
        }
    }

    public function addCargoArea($areaData){

        $query = "INSERT INTO kargobolgeleri SET 
            kargobolgead=?";

        $this->db->beginTransaction();

        $insert = $this->db->insert($query, [
            $areaData["areaName"]
        ]);

        if($insert){
            $this->db->commit();
            return $insert;
        }else{
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCargoArea($areaData){

        $query = "UPDATE kargobolgeleri SET 
            kargobolgead=?
            WHERE kargobolgeid=?";

        $this->db->beginTransaction();

        $update = $this->db->update($query, [
            $areaData["areaName"],
            $areaData["areaID"]
        ]);

        if($update){
            $this->db->commit();
            return $update;
        }else{
            $this->db->rollBack();
            return false;
        }
    }

    public function getCargoCities(){
        $query = "SELECT * FROM kargosehirler ";
        $data = $this->db->select($query, []);

        if(!empty($data)){
            //sütun isimlerini ingilizce yapalım
            $data = array_map(function($item){
                $item["cityID"] = $item["kargosehirid"];
                $item["cityName"] = $item["kargosehirad"];
                $item["cityActive"] = $item["kargosehiraktif"];
                return $item;
            }, $data);
            return $data;
        }else{
            return [];
        }
    }

    public function getCargoCity($cityID){
        $query = "SELECT * FROM kargosehirler WHERE kargosehirid=?";
        $data = $this->db->select($query, [$cityID]);

        if(!empty($data)){
            $data = array_map(function($item){
                $item["cityID"] = $item["kargosehirid"];
                $item["cityName"] = $item["kargosehirad"];
                $item["cityActive"] = $item["kargosehiraktif"];
                return $item;
            }, $data);
            return $data[0];
        }else{
            return [];
        }
    }

    public function addCargoCity($cityData)
    {

        $query = "INSERT INTO kargosehirler SET
            kargosehirad=?,
            kargosehiraktif=?";

        $this->db->beginTransaction();

        $insert = $this->db->insert($query, [
            $cityData["cityName"],
            $cityData["cityActive"]
        ]);

        if ($insert) {
            $this->db->commit();
            return $insert;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCargoCity($cityData)
    {

        $query = "UPDATE kargosehirler SET
            kargosehirad=?,
            kargosehiraktif=?
            WHERE kargosehirid=?";

        $this->db->beginTransaction();

        $update = $this->db->update($query, [
            $cityData["cityName"],
            $cityData["cityActive"],
            $cityData["cityID"]
        ]);

        if ($update) {
            $this->db->commit();
            return $update;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function getCargoPrice()
    {
        $query = "SELECT * FROM kargoucret ";
        $data = $this->db->select($query, []);

        if (!empty($data)) {
            //sütun isimlerini ingilizce yapalım, türkçe isimleri kaldıralım

            $data = array_map(function ($item) {
                $item["priceID"] = $item["kargoucretid"];
                $item["fixedPrice"] = $item["kargosabitucret"];
                $item["freeCargo"] = $item["kargoucretsiz"];
                $item["cargoType"] = $item["kargogonderimtipi"];
                $item["cargoTime"] = $item["kargosuresi"];
                $item["cashOnDelivery"] = $item["kargokapidaek"];
                $item["productCount"] = $item["kargourunadet"];
                return $item;
            }, $data);
            //türkçe isimleri kaldıralım
            $data = array_map(function ($item) {
                unset($item["kargoucretid"]);
                unset($item["kargosabitucret"]);
                unset($item["kargoucretsiz"]);
                unset($item["kargogonderimtipi"]);
                unset($item["kargosuresi"]);
                unset($item["kargokapidaek"]);
                unset($item["kargourunadet"]);
                return $item;
            }, $data);
            return $data;
        } else {
            return [];
        }
    }

    public function addCargoPrice($cargoPriceData)
    {
        $query = "INSERT INTO kargoucret SET
            kargosabitucret=?,
            kargoucretsiz=?,
            kargogonderimtipi=?,
            kargosuresi=?,
            kargokapidaek=?,
            kargourunadet=?";

        $this->db->beginTransaction();

        $insert = $this->db->insert($query, [
            $cargoPriceData["fixedPrice"],
            $cargoPriceData["freeCargo"],
            $cargoPriceData["cargoType"],
            $cargoPriceData["cargoTime"],
            $cargoPriceData["cashOnDelivery"],
            $cargoPriceData["productCount"]
        ]);

        if ($insert) {
            $this->db->commit();
            return $insert;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCargoPrice($cargoPriceData)
    {
        $query = "UPDATE kargoucret SET
            kargosabitucret=?,
            kargoucretsiz=?,
            kargogonderimtipi=?,
            kargosuresi=?,
            kargokapidaek=?,
            kargourunadet=?
            WHERE kargoucretid=?";

        $this->db->beginTransaction();

        $update = $this->db->update($query, [
            $cargoPriceData["fixedPrice"],
            $cargoPriceData["freeCargo"],
            $cargoPriceData["cargoType"],
            $cargoPriceData["cargoTime"],
            $cargoPriceData["cashOnDelivery"],
            $cargoPriceData["productCount"],
            $cargoPriceData["priceID"]
        ]);

        if ($update) {
            $this->db->commit();
            return $update;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function getCargoDesiSettings()
    {
        $query = "SELECT * FROM kargodesiayar ";
        $data = $this->db->select($query, []);

        if (!empty($data)) {
            //sütun isimlerini ingilizce yapalım
            $data = array_map(function ($item) {
                $item["desiSettingID"] = $item["kargodesiayarid"];
                $item["cargoID"] = $item["kargoid"];
                $item["areaID"] = $item["kargobolgeid"];
                $item["desi"] = [];
                for ($i = 0; $i <= 50; $i++) {
                    $item["desi"][] = $item["desi" . $i];
                }
                return $item;
            }, $data);
            return $data;
        } else {
            return [];
        }
    }

    public function addCargoDesiSetting($desiSettingData)
    {
        $query = "INSERT INTO kargodesiayar SET
            kargoid=?,
            kargobolgeid=?";

        for ($i = 0; $i <= 50; $i++) {
            $query .= ",desi" . $i . "=?";
        }

        $this->db->beginTransaction();

        $insert = $this->db->insert($query, array_merge([
            $desiSettingData["cargoID"],
            $desiSettingData["areaID"]
        ], $desiSettingData["desi"]));

        if ($insert) {
            $this->db->commit();
            return $insert;
        } else {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCargoDesiSetting($desiSettingData)
    {
        $query = "UPDATE kargodesiayar SET
            kargoid=?,
            kargobolgeid=?";

        for ($i = 0; $i <= 50; $i++) {
            $query .= ",desi" . $i . "=?";
        }

        $query .= " WHERE kargodesiayarid=?";

        $this->db->beginTransaction();

        $update = $this->db->update($query, array_merge([
            $desiSettingData["cargoID"],
            $desiSettingData["areaID"]
        ], $desiSettingData["desi"], [
            $desiSettingData["desiSettingID"]
        ]));

        if ($update) {
            $this->db->commit();
            return $update;
        } else {
            $this->db->rollBack();
            return false;
        }
    }
}