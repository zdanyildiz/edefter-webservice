<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

################# CONFIG ###################################
//ön tanımlı ayarlarımızı yapalım
$config = new Config();

################# DATABASE #################################

include_once DATABASE . "AdminDatabase.php";
$db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

include_once CORE."AdminCasper.php";
include_once MODEL."Admin/AdminSession.php";
$adminSession = new AdminSession($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);

$adminCasper = $adminSession->getAdminCasper();
if (!$adminCasper instanceof AdminCasper) {
    echo "AdminCasper is not here - Admin Index:20";exit();
}
$adminCasper->setLoginStatus(false);
$adminCasper->setAdmin([]);
$adminSession->updateSession("adminCasper",$adminCasper);

$adminSession->deleteCookie("adminCasper");

if (!isset($_COOKIE["adminCasper"])) {
    //echo "Çerez silinmiş.";
} else {
    /*echo "Çerez hala mevcut: ";
    print_r($_COOKIE["adminCasper"]);*/
}

$adminSession->deleteAdminCasper();
if (!isset($_SESSION["adminCasper"])) {
    //echo "Oturum silinmiş.";
} else {
    /*echo "Oturum hala mevcut: ";
    print_r($_SESSION["adminCasper"]);*/
}
//exit;
exit(header('Location: /_y/'));
?>