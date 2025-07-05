<?php
$logTipiValues = ["site", "panel"];
$logTipi = $_POST["logTipi"];
$baslangicTarihi = $_POST["baslangicTarihi"]/1000;
$bitisTarihi = $_POST["bitisTarihi"]/1000;

if (in_array($logTipi, $logTipiValues, true) && $baslangicTarihi > 0 && $bitisTarihi > 0) {

    $dir = $_SERVER['DOCUMENT_ROOT'] . "/log/{$logTipi}";
    $logFiles = scandir($dir, 1);

    $logData = array();

    foreach ($logFiles as $logFile) {

        if (strlen($logFile) < 3 || $logFile =='.DS_Store') {
            continue;
        }

        $fileName = str_replace(".txt","",$logFile);
        $logFileTime = strtotime($fileName ."GMT");
        //array_push($logData, $baslangicTarihi." >= ".$logFileTime." <=".$bitisTarihi);
        if ($logFileTime< $baslangicTarihi || $logFileTime>$bitisTarihi){
            continue;
        }

        $fileForRead = file_get_contents($dir . "/" . $logFile);
        $fileForRead = mb_convert_encoding($fileForRead, 'HTML-ENTITIES', "UTF-8");

        $logRows = explode("*", $fileForRead);

        foreach ($logRows as $rowText) {
            if (strlen($rowText) == 0 || $rowText=="\n")
                continue;


            $dataRow = explode("|", utf8_encode($rowText));
            if (is_array($dataRow))
                array_push($logData, $dataRow);
        }
    }
    echo json_encode($logData);
}
