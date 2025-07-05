<?php
$documentRoot = str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']);
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
define("ROOT", $documentRoot . $directorySeparator);
define("PUBL", ROOT . "Public" . $directorySeparator);
define("APP", ROOT . "App" . $directorySeparator);
define("Helpers", APP . "Helpers" . $directorySeparator);
define("IMG", PUBL . "Image" . $directorySeparator);

require_once Helpers . 'Image.php';


$imagePath = $_GET['imagePath'] ?? null;
if(!is_null($imagePath)){
    $imagePath = IMG. $imagePath;
}
$width = $_GET['width'] ?? null;
$height = $_GET['height'] ?? null;
$crop = $_GET['crop'] ?? null;

if (!$imagePath) {
    die('Image path is required');
}

if (!file_exists($imagePath)) {
    $image = new Image($imagePath);
    $imageNotFound = $image->createImageNotFound(500, 500); // 500x500 boyutunda bir resim oluşturulur
    header('Content-Type: image/png');
    imagepng($imageNotFound);
    imagedestroy($imageNotFound);
    exit;
}

$image = new Image($imagePath);
//die($imagePath);
//die("$width x $height");
$imageOutputPath = $image->resize($width, $height);

if ($crop) {
    $centerCrop = $_GET['centerCrop'] ?? false;
    $startX = $_GET['startX'] ?? 0;
    $startY = $_GET['startY'] ?? 0;
    $cropWidth = $_GET['width'] ?? $width;
    $cropHeight = $_GET['height'] ?? $height;
    $imageOutputPath = $image->resize($cropWidth, $cropHeight,true);

    /*if ($centerCrop) {
        list($originalWidth, $originalHeight) = getimagesize($imagePath);
        $startX = ($originalWidth - $cropWidth) / 2;
        $startY = ($originalHeight - $cropHeight) / 2;
    }

    $imageOutputPath = $image->crop($startX, $startY, $cropWidth, $cropHeight);*/
}// @todo buna bakılacak
//die($imagePath);
$imageType = $image->imageType;
switch ($imageType) {
    case IMAGETYPE_JPEG:
        header('Content-Type: image/jpeg');
        break;
    case IMAGETYPE_GIF:
        header('Content-Type: image/gif');
        break;
    case IMAGETYPE_PNG:
        header('Content-Type: image/png');
        break;
    case IMAGETYPE_WEBP:
        header('Content-Type: image/webp');
        break;
    default:
        header('Content-Type: application/octet-stream');
}
echo file_get_contents($imageOutputPath);