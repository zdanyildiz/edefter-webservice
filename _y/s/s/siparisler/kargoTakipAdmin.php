<?php
if (isset($_REQUEST["kargoFirmaCode"]) && isset($_REQUEST["kargoTakipNo"])) {

    require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/sistem/kargoTakip/KargoTakipService.php");
    //require_once($_SERVER['DOCUMENT_ROOT']."/sistem/kargoTakip/KargoTakipService.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/sistem/kargoTakip/KargoHareket.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/sistem/kargoTakip/KargoTakipParam.php");

    $kargoTakipService = new KargoTakipService();
    $kargoHareketParam = new KargoTakipParam();
    $kargoHareketParam->setKargoFirmasi($_REQUEST["kargoFirmaCode"]);
    $kargoHareketParam->setKargoTakipNo($_REQUEST["kargoTakipNo"]);

    $kargoHareketList = $kargoTakipService->getKargoHareket($kargoHareketParam);

    if (array_key_exists("isHata", $kargoHareketList)){
        echo "<tr><td colspan='5'>" . $kargoHareketList["aciklama"] . "</td></tr>";
        return;
    }

    //$hareket instance of KargoHareket class;
    foreach ($kargoHareketList as $hareket) {

        if ($hareket instanceof KargoHareket) {?>
            <tr>
                <td><?=$hareket->getTarih()?></td>
                <td><?=$hareket->getBirim()?></td>
                <td><?=$hareket->getIslem()?></td>
                <td><?=$hareket->getDurum()?></td>
                <td><?=$hareket->getAciklama()?></td>
            </tr>
        <?php }
    }
}

?>