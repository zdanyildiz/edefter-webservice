<?php

function hatalogisle($islem,$hata)
{

    global $anadizin,$yoneticioturum_kimlik,$yoneticioturum_adsoyad,$data;

    if(!isset($yoneticioturum_kimlik))$yoneticioturum_kimlik="Oturum açılmamış";
    if(!isset($yoneticioturum_adsoyad))$yoneticioturum_adsoyad="Giriş Yapılmamış";

    if(BosMu($hata))$hata="data tanımlanmamış";
    if(!file_exists($anadizin.'/log/site')){mkdir($anadizin.'/log/site', 0777, true);}
    $logsayfa = $anadizin.'/log/panel/'.date("Y-m-d").'.txt';
    $yazilacak =date("Y-m-d H:i:s").'|'.$yoneticioturum_kimlik.'|'.$yoneticioturum_adsoyad.'|'.$islem.'|'. $hata .'*';

    $yazilacak=@iconv(mb_detect_encoding($yazilacak),"UTF-8//TRANSLIT", $yazilacak);
    file_put_contents($logsayfa,$yazilacak.PHP_EOL,FILE_APPEND);

    unset($islem,$hata);
}
?>
