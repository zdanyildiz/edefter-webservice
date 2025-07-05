<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

if(isset($_POST["pinpost"])=="pinpost")
{
    $valuemalzeme = strip_tags(trim($_POST['pinad']));
    $valuemalzeme_2=htmlspecialchars($valuemalzeme);
    $id= strip_tags(trim($_POST['id']));
    if (!empty($valuemalzeme))
    {
        $urunmalzeme="SELECT urunmalzemead,urunmalzemeid FROM urunmalzeme WHERE urunmalzemesil='0' and urunmalzemegrupid=2 and (urunmalzemead like '%$valuemalzeme%' or urunmalzemead like '%$valuemalzeme_2%') GROUP BY urunmalzemead LIMIT 10";
        if ($data->query($urunmalzeme))
        {
            $urunmalzeme_v = $data->query($urunmalzeme);
            unset($urunmalzeme);
            if ($urunmalzeme_v->num_rows > 0)
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="pinal" id="0"   type="text"  data-id="'.$id.'" class="pinad form-control" value="Pin Yok"></li>';
                while ($urunmalzeme_t = $urunmalzeme_v->fetch_assoc())
                {
                    echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="'.$urunmalzeme_t["urunmalzemeid"].'" class="pinal" id="'.$urunmalzeme_t["urunmalzemeid"].'"   type="text"  data-id="'.$id.'" class="pinad form-control" value="'.str_replace('"',"''",$urunmalzeme_t["urunmalzemead"]).'"></li>';
                }
                unset($urunmalzeme_t);
            }
            else
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px">Hiç bir sonuç bulunamadı</li>';
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="pinal" id="0"   type="text"  data-id="'.$id.'" class="pinad form-control" value="Pin Yok"></li>';
            }
        } else {die($data->error);}
    }
    elseif (empty($valuemalzeme))
    {
        echo '<p class="yok" style="display: contents">Pin Malzeme Adı Giriniz!!!</p>';
    }

}
?>
