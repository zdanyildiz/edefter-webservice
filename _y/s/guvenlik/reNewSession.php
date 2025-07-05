<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

// Yeni oturum süresini al
$newSessionLifetime = ini_get('session.gc_maxlifetime');

// Yeni oturum süresini döndür
echo $newSessionLifetime;