<?php
/**
 * @var $db Database
 * @var array $requestData
 * @var Config $config
 */

//print_r($requestData);exit();

$action = $requestData['action'] ?? null;

if(null === $action) {
    Log::write("CookieController: action is required", "error");
    json_encode(['status' => 'error', 'message' => 'Action is required']);
    exit();
}
//Log::write("CookieController: action: $action data: ".json_encode($requestData), "info");
if($action == "createCookie"){
    $name = $requestData['name'] ?? null;
    $value = $requestData['value'] ?? null;

    //boş değilse
    if($name && $value){
        include_once MODEL .'Session.php';
        $session = new Session($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);
        $session->addCookie($name, ['status'=>$value], 12);
        //Log::write("CookieController: status:success createCookie: name: $name, value: $value", "info");
        echo json_encode(['status' => 'success', 'message' => $name.' Cookie created successfully']);
    }else{
        Log::write("CookieController: createCookie: name and value are required", "error");
        echo json_encode(['status' => 'error', 'message' => 'Name and value are required']);
    }
    exit();
}
