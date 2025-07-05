<?php
/**
 * @var $db Database

 */

// popup için verileri get metoduyla alalım, get ile veri gelmemişse ön tanımlı değer kullanalım
// type ya da mesaj yoksa boş döndürelim
if (!isset($_GET['type']) || !isset($_GET['message'])){
    echo $_SERVER['REQUEST_URI'];
    exit();
}

include_once Helpers . "Popup.php";
if($_GET['languageCode']){
    $languageCode = $_GET['languageCode'];
    $languageModel = new Language($db, $languageCode);
    $languageModel->getTranslations($languageCode);
}


$type = $_GET['type'] ?? 'success';
$message = $_GET['message'] ?? 'İşlem başarılı';
$position = $_GET['position'] ?? 'top-right';
$width = $_GET['width'] ?? '300px';
$height = $_GET['height'] ?? '200px';
$closeButton = $_GET['closeButton'] ?? true;
$autoClose = !((isset($_GET['autoClose']) && $_GET['autoClose'] === 'false'));
$animation = $_GET['animation'] ?? true;

$action = $_GET['action'] ?? 'show';

$popupCss ="";

if(isset($_GET['confirm'])){
    if($_GET['confirm'] == 'delete'){
        $confirmButton = '<button class="confirm">'._sepet_urun_sil_yazi.'</button>';
        $message = $message.$confirmButton;
        $popupCss = '.confirm{background-color: var(--button-color); color: var(--button-text-color); padding: 14px 20px; margin: 8px 0; border: none; cursor: pointer; width: 100%;margin:20px auto} .confirm:hover{opacity: 0.8;}';
    }
}

if($action == 'deleteAddress'){
    $popup = new Popup($type, $message, $position, $width, $height, $closeButton, $autoClose, $animation);
}
elseif ($action == 'show'){
    $popup = new Popup($type, $message, $position, $width, $height, $closeButton, $autoClose, $animation);
}

echo $popup->show();
echo "<style>".$popup->popupCss().$popupCss."</style>";
exit();