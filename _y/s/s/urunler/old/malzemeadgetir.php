<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

if(isset($_POST["malzemepost"])=="malzemepost")
{
    $valuemalzeme = strip_tags(trim($_POST['malzemead']));
    $valuemalzeme_2=htmlspecialchars($valuemalzeme);
    $id= strip_tags(trim($_POST['id']));
    if (!empty($valuemalzeme))
    {
        $urunmalzeme="SELECT urunmalzemead,urunmalzemeid FROM urunmalzeme WHERE urunmalzemesil='0' and (urunmalzemead like '%$valuemalzeme%' or urunmalzemead like '%$valuemalzeme_2%') GROUP BY urunmalzemead LIMIT 20";
        if ($data->query($urunmalzeme))
        {
            $urunmalzeme_v = $data->query($urunmalzeme);
            unset($urunmalzeme);
            if ($urunmalzeme_v->num_rows > 0)
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="malzemeal" id="0"   type="text"  data-id="'.$id.'" class="malzemead form-control" value="Malzeme Yok"></li>';
                while ($urunmalzeme_t = $urunmalzeme_v->fetch_assoc())
                {

                    echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="'.$urunmalzeme_t["urunmalzemeid"].'" class="malzemeal" id="'.$urunmalzeme_t["urunmalzemeid"].'"   type="text"  data-id="'.$id.'" class="malzemead form-control" value="'.str_replace('"',"''",$urunmalzeme_t["urunmalzemead"]).'"></li>';
                }
                unset($urunmalzeme_t);
            }
            else
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px">Hiç bir sonuç bulunamadı</li>';
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="malzemeal" id="0"   type="text"  data-id="'.$id.'" class="malzemead form-control" value="Malzeme Yok"></li>';
            }
        } else {die($data->error);}
    }
    elseif (empty($valuemalzeme))
    {
        echo '<p class="yok" style="display: contents">Malzeme Adı Giriniz!!!</p>';
    }

}
if(isset($_POST["olcupost"])=="olcupost")
{
    $valueolcu = strip_tags(trim($_POST['urunolcuad']));
    $valueolcu_2=htmlspecialchars($valueolcu);
    $id= strip_tags(trim($_POST['id']));
    if (!empty($valueolcu))
    {
        $urunbeden="SELECT urunbedenad,urunbedenid FROM urunbeden WHERE urunbedensil='0' and (urunbedenad like '%$valueolcu%' or urunbedenad like '%$valueolcu_2%') GROUP BY urunbedenad LIMIT 10";
        if ($data->query($urunbeden))
        {
            $urunbeden_v = $data->query($urunbeden);
            unset($urunbeden);
            if ($urunbeden_v->num_rows > 0)
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="olcual" id="0"   type="text"  data-id="'.$id.'" class="olcuad form-control" value="Ölçü Yok"></li>';
                while ($urunbeden_t = $urunbeden_v->fetch_assoc())
                {

                    echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%"  name="'.$urunbeden_t["urunbedenid"].'" class="olcual" id="'.$urunbeden_t["urunbedenid"].'"   type="text" data-id="'.$id.'" class="malzemead form-control" value="'.str_replace('"',"''",$urunbeden_t["urunbedenad"]).'"></li>';
                }
                unset($urunbeden_t);
            }
            else
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px">Hiç bir sonuç bulunamadı</li>';
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="olcual" id="0"   type="text"  data-id="'.$id.'" class="olcuad form-control" value="Ölçü Yok"></li>';
            }
        } else {die($data->error);}
    }
    elseif (empty($valueolcu))
    {
        echo '<p style="display: contents">Ölçü Giriniz!!!</p>';
    }
}
if(isset($_POST["renkpost"])=="renkpost")
{
    $valuerenk = strip_tags(trim($_POST['renkvalue']));
    $valuerenk_2=htmlspecialchars($valuerenk);
    $id= strip_tags(trim($_POST['id']));
    if (!empty($valuerenk))
    {
        $urunrenk="SELECT urunrenkid,urunrenkad FROM urunrenk WHERE urunrenksil='0' and (urunrenkad like '%$valuerenk%' or urunrenkad like '%$valuerenk_2%') GROUP BY urunrenkad LIMIT 10";
        if ($data->query($urunrenk))
        {
            $urunrenk_v = $data->query($urunrenk);
            unset($urunrenk);
            if ($urunrenk_v->num_rows > 0)
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="renkal" id="0"   type="text"  data-id="'.$id.'" class="renkad form-control" value="Renk Yok"></li>';
                while ($urunrenk_t = $urunrenk_v->fetch_assoc())
                {

                    echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px" ><input style="width:100%"  name="'.$urunrenk_t["urunrenkid"].'" class="renkal" id="'.$urunrenk_t["urunrenkid"].'"   type="text" data-id="'.$id.'" class="malzemead form-control" value="'.str_replace('"',"''",$urunrenk_t["urunrenkad"]).'"></li>';
                }
                unset($urunrenk_t);
            }
            else
            {
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px">Hiç bir sonuç bulunamadı</li>';
                echo '<li style="display:block;width:100%;cursor:pointer;margin-left:-14px"><input style="width:100%" name="0" class="renkal" id="0"   type="text"  data-id="'.$id.'" class="renkad form-control" value="Renk Yok"></li>';
            }
        } else {die($data->error);}
    }
    elseif (empty($valuerenk))
    {
        echo '<p style="display: contents">Ölçü Giriniz!!!</p>';
    }
}

?>
