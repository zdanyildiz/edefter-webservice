<?php
/**
 * @var Config $config
 */

include_once CORE."AdminCasper.php";
include_once MODEL."Admin/AdminSession.php";

$adminSession = new AdminSession($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);

$adminCasper = $adminSession->getAdminCasper();
if (!$adminCasper instanceof AdminCasper) {
    echo "AdminCasper is not here - Admin Index:20";exit();
}

$adminCasper->setConfig($config);
$adminSession->updateSession("adminCasper",$adminCasper);

$loginStatus = $adminCasper->isLoginStatus();

if(!$loginStatus){
    header("Location: /Admin/login");exit();
}