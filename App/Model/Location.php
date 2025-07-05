<?php
class Location {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCountryNameById($id) {
        //id sayı değilse gelen değeri aynen geri gönderelim
        if (!is_numeric($id)) {
            return $id;
        }
        $sql = "SELECT CountryName FROM yerulke WHERE CountryID = :id";
        $result = $this->db->select($sql, ['id' => $id]);
        return $result ? $result[0]['CountryName'] : null;
    }

    public function getCityNameById($id) {
        if (!is_numeric($id)) {
            return $id;
        }
        $sql = "SELECT CityName FROM yersehir WHERE CityID = :id";
        $result = $this->db->select($sql, ['id' => $id]);
        return $result ? $result[0]['CityName'] : null;
    }

    public function getCountyNameById($id) {
        if (!is_numeric($id)) {
            return $id;
        }
        $sql = "SELECT CountyName FROM yerilce WHERE CountyID = :id";
        $result = $this->db->select($sql, ['id' => $id]);
        return $result ? $result[0]['CountyName'] : null;
    }

    public function getAreaNameById($id) {
        if (!is_numeric($id)) {
            return $id;
        }
        $sql = "SELECT AreaName FROM yersemt WHERE AreaID = :id";
        $result = $this->db->select($sql, ['id' => $id]);
        return $result ? $result[0]['AreaName'] : null;
    }

    public function getNeighborhoodNameById($id) {
        if (!is_numeric($id)) {
            return $id;
        }
        $sql = "SELECT NeighborhoodName FROM yermahalle WHERE NeighborhoodID = :id";
        $result = $this->db->select($sql, ['id' => $id]);
        return $result ? $result[0]['NeighborhoodName'] : null;
    }

    public function getAllCountries() {
        $sql = "SELECT * FROM yerulke";
        return $this->db->select($sql);
    }

    public function getCity($countryID) {
        $sql = "SELECT * FROM yersehir WHERE CountryID = :countryID";
        return $this->db->select($sql, ['countryID' => $countryID]);
    }

    public function getCounty($cityID) {
        $sql = "SELECT * FROM yerilce WHERE CityID = :cityID";
        return $this->db->select($sql, ['cityID' => $cityID]);
    }

    public function getArea($countyID) {
        $sql = "SELECT * FROM yersemt WHERE CountyID = :countyID";
        return $this->db->select($sql, ['countyID' => $countyID]);
    }

    public function getNeighborhood($areaID) {
        $sql = "SELECT * FROM yermahalle WHERE AreaID = :areaID";
        return $this->db->select($sql, ['areaID' => $areaID]);
    }

    public function getPostalCode($neighborhoodID) {
        $sql = "SELECT ZipCode FROM yermahalle WHERE NeighborhoodID = :neighborhoodID";
        return $this->db->select($sql, ['neighborhoodID' => $neighborhoodID]);
    }

    public function getCountryPhoneCode($CountryID) {
        $sql = "SELECT PhoneCode FROM yerulke WHERE CountryID = :CountryID";
        return $this->db->select($sql, ['CountryID' => $CountryID])[0]['PhoneCode'];
    }

    public function getCountryPhoneCodeByCountryName($CountryName) {
        $sql = "SELECT PhoneCode FROM yerulke WHERE CountryName = :CountryName";
        return $this->db->select($sql, ['CountryName' => $CountryName])[0]['PhoneCode'];
    }
}