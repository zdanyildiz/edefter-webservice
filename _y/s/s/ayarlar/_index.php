<?php
//site ayarları
$domain				= $_SERVER['HTTP_HOST'];
$siteDomain		= "";
$ayarsitessl		= 0;
$ayarsiteprotokol	= "http://";
if($ssl==1)$ayarsiteprotokol="https://";
$sayfaadres	= $ayarsiteprotokol.$siteDomain.$_SERVER['REQUEST_URI'];
$ampdurum	= 0;
if(strpos($sayfaadres,$protokol."amp.")!==false)$ampdurum=1;
$resimklasor= "m/r/";
$resimdizin	= $ayarsiteprotokol.$siteDomain."/".$resimklasor;
?>