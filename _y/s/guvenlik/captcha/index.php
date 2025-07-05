<?php
	include("1.php");
?><!DOCTYPE html><html><head><title>Güvenlik Kodu</title><style type="text/css">body {margin:0;padding:0}img{border:solid 1px #ccc;margin:0;width:100%;height:auto}</style></head><body><?='<img id="guvenlikkoduresim" src="/_y/s/guvenlik/captcha' . $_SESSION[$guvenlikoturum]['image_src'] . '" alt="Güvenlik Kodu">'?></body></html>