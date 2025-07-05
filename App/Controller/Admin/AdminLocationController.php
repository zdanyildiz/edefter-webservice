<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminSession $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */


$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

include_once MODEL . 'Admin/AdminLocation.php';
$locationModel = new AdminLocation($db);

$locationID = $requestData["id"] ?? null;

if($action == "getCity"){


    if (!isset($locationID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Country ID error'
        ]);
        exit();
    }

    $cities = $locationModel->getCity($locationID);
    if (!empty($cities)) {

        //cityName ve CityID'yi name ve id olarak değiştirelim
        $cities = array_map(function($city){
            return [
                'name' => $city['CityName'],
                'id' => $city['CityID']
            ];
        }, $cities);


        echo json_encode([
            'status' => 'success',
            'location' => $cities
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No city found'
        ]);
        exit();
    }
}
else if($action == "getCounty"){

    if (!isset($locationID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'City ID error'
        ]);
        exit();
    }

    $counties = $locationModel->getCounty($locationID);
    if (!empty($counties)) {

        //countyName ve CountyID'yi name ve id olarak değiştirelim
        $counties = array_map(function($county){
            return [
                'name' => $county['CountyName'],
                'id' => $county['CountyID']
            ];
        }, $counties);

        echo json_encode([
            'status' => 'success',
            'location' => $counties
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No county found'
        ]);
        exit();
    }
}
else if($action == "getArea"){

    if (!isset($locationID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'County ID error'
        ]);
        exit();
    }

    $areas = $locationModel->getArea($locationID);
    if (!empty($areas)) {

        //areaName ve AreaID'yi name ve id olarak değiştirelim
        $areas = array_map(function($area){
            return [
                'name' => $area['AreaName'],
                'id' => $area['AreaID']
            ];
        }, $areas);

        echo json_encode([
            'status' => 'success',
            'location' => $areas
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No area found'
        ]);
        exit();
    }
}
else if($action == "getNeighborhood"){

    if (!isset($locationID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Area ID error'
        ]);
        exit();
    }

    $neighborhoods = $locationModel->getNeighborhood($locationID);
    if (!empty($neighborhoods)) {

        //neighborhoodName ve NeighborhoodID'yi name ve id olarak değiştirelim
        $neighborhoods = array_map(function($neighborhood){
            return [
                'name' => $neighborhood['NeighborhoodName'],
                'id' => $neighborhood['NeighborhoodID']
            ];
        }, $neighborhoods);

        echo json_encode([
            'status' => 'success',
            'location' => $neighborhoods
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No neighborhood found'
        ]);
        exit();
    }
}
else if($action == "getPostalCode"){

    if (!isset($locationID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Neighborhood ID error'
        ]);
        exit();
    }

    $postalCodes = $locationModel->getPostalCode($locationID);
    if (!empty($postalCodes)) {
        echo json_encode([
            'status' => 'success',
            'postalCode' => $postalCodes[0]['ZipCode']
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No postal code found'
        ]);
        exit();
    }
}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}
