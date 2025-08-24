<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

################# CONFIG ###################################
//ön tanımlı ayarlarımızı yapalım
$config = $config ?? new Config();
$helper = $config->Helper;
$json = $config->Json;

################# DATABASE #################################

include_once DATABASE . "AdminDatabase.php";
$db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

include_once CORE."AdminCasper.php";
include_once MODEL."Admin/AdminSession.php";
$adminSession = new AdminSession($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);

//requestData tanımlayalım. Formlardan gelen tüm get ve post verilerini alalım
$requestData = array_merge($_GET, $_POST);

$adminCasper = $adminSession->getAdminCasper();
if (!$adminCasper instanceof AdminCasper) {
    echo "Admin is not here - Admin Index:20";exit();
}

$action = $requestData['action'] ?? null;
//Log::adminWrite("action: ".$action,"special");
$permissionToPass=false;
if(!empty($action)){
    if($action == "loginWithEmailOrPhone" || $action == "loginWithEmailOrPhoneAndPassword" || $action == "loginWithPIN" || $action == "logOut"){
        $permissionToPass=true;
    }
}

$loginStatus = $adminCasper->isLoginStatus();
if($loginStatus) $permissionToPass = true; // Login yapılmışsa tüm işlemlere izin ver

// Permission kontrolü önce yapılmalı
if(!$permissionToPass && !$loginStatus){
    echo json_encode([
        'status' => 'error',
        'message' => 'Admin Login required'
    ]); exit;
}

$adminCasper->setConfig($config);
$adminSession->updateSession("adminCasper",$adminCasper);

$adminCasper = $adminSession->getAdminCasper();

// Oturum üzerinde ek request data varsa ekleyelim
if(isset($_SESSION['requestData'])){
    $requestData = array_merge($requestData, $_SESSION['requestData']);
    unset($_SESSION['requestData']);
}
define('ADMIN_GLOBAL_INCLUDED', true);
