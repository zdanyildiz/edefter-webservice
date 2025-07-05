<?php
$documentRoot = str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']);
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
define("ROOT", $documentRoot . $directorySeparator);
define("PUBL", ROOT . "Public" . $directorySeparator);
define("APP", ROOT . "App" . $directorySeparator);
define("Helpers", APP . "Helpers" . $directorySeparator);
define("FILE", PUBL . "File" . $directorySeparator);
define("fileRoot","/Public/File/");

$fileExtension = $_GET['fileExtension'] ?? null;
$filePath = FILE . $fileExtension . ".png";
$width = $_GET['width'] ?? null;
$height = $_GET['height'] ?? null;

header('Content-Type: image/png');
if (!file_exists($filePath)) {
    $filePath = FILE . "file.png";
    echo file_get_contents($filePath);
}
echo file_get_contents($filePath);