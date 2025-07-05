<?php
/**
 * @var AdminDatabase $db

 */
function kurulumayarkontrol()
{
	global $anadizin,$domainsayfa,$sqlsayfa,$anahtarsayfa;
	//if (!file_exists($anadizin.'/sistem/veri')){mkdir($anadizin.'/sistem/veri');}
	//domain kontrol
	//if (!file_exists($anadizin.'/sistem/veri/domain')){mkdir($anadizin.'/s/v/domain');}
	$domainsayfa = $anadizin.'/_y/s/f/domain/domain.php';
	//veritabanı kontrol
	//if (!file_exists($anadizin.'/sistem/veri/sql')){mkdir($anadizin.'/s/v/sql');}
	$sqlsayfa = $anadizin.'/_y/s/f/sql/sql.php';
	//anahtar kod kontrol
	//if (!file_exists($anadizin.'/sistem/veri/anahtar')){mkdir($anadizin.'/s/v/anahtar');}
	$anahtarsayfa = $anadizin.'/_y/s/f/anahtar/anahtar.php';

	if (!file_exists($domainsayfa) || !file_exists($sqlsayfa) || !file_exists($anahtarsayfa))
	{
		git('/_y/s/guvenlik/sitekayit.php');
	}
	else
	{
		git('/_y/s/guvenlik/giris.php');
	}
}
function logoal($dilid)
{
	global $db,$logoyazi,$logoresim,$logogenislik,$logoyukseklik,$dilid;
	global $http,$siteDomain,$seoresim,$seoresim_genislik,$seoresim_yukseklik;
	$ayarlogo_s="
		SELECT 
			logoyazi,resim.resim,resimklasorad,ren,rboy 
		FROM 
			ayarlogo 
				inner join resim on 
					resim.resimid=ayarlogo.resimid
					inner join resimklasor on 
						resimklasor.resimklasorid=resim.resimklasorid
		WHERE dilid='".$dilid."'
	";
	if($db->select($ayarlogo_s))
	{
		$ayarlogo_t=$db->select($ayarlogo_s)[0];

				$logoyazi=$ayarlogo_t["logoyazi"];
				$logoresim="/m/r/".$ayarlogo_t["resimklasorad"]."/".$ayarlogo_t["resim"];
				$logogenislik=$ayarlogo_t["ren"];
				$logoyukseklik=$ayarlogo_t["rboy"];


		if(BosMu($seoresim))
		{
			$seoresim=$http."//www.".$siteDomain.$logoresim;
			$seoresim_genislik=$logogenislik;
			$seoresim_yukseklik=$logoyukseklik;
		}

	}
	else
	{
		$logoyazi="Logo Yazı";
		$logoresim="/tema/img/logo/logo.png";
		$logogenislik="150";
		$logoyukseklik="100";
	}
}
function yoneticisongiris()
{
	global $db,$yoneticioturum_kimlik;
	$yoneticitarihguncelle_s="
		UPDATE
			yoneticilerlog
		SET
			YoneticiSonTarih=:YoneticiSonTarih
		WHERE
			YoneticiLogKimlik=:YoneticiLogKimlik
	";
	$params=array(
		'YoneticiSonTarih'=>date("Y-m-d H:i:s"),
		'YoneticiLogKimlik'=>$yoneticioturum_kimlik
	);
	$db->beginTransaction();
	if ($db->update($yoneticitarihguncelle_s,$params))
	{
		$db->commit();
		//hatalogisle("yoneticisongiris",$data->error);
	}
	else
	{
		$db->rollBack();
		//hatalogisle("yoneticisongiris",$db->error);
	}
}
function kilitkontrol()
{
	global $db,$yoneticioturum_yoneticiid	,$admin_simdisayfalink;
	$pinkontrol_d=0;
	$pinkontrol_s="
		Select
			YoneticiPin
		from
			yoneticiler
		where
			YoneticiID='".S($yoneticioturum_yoneticiid)."'
	";
	if ($db->select($pinkontrol_s))
	{
		$pinkontrol_v=$db->select($pinkontrol_s);
		if($pinkontrol_v)$pinkontrol_d=1;
	}

	if($pinkontrol_d==1)
	{
		foreach($pinkontrol_v as $pinkontrol_t)
		{
			$yoneticipin=$pinkontrol_t["YoneticiPin"];
		}
		if(S($yoneticipin)==0)
		{
			if(strpos($admin_simdisayfalink,"_y/s/s/yoneticiler/ekle.php")===false)git('/_y/s/s/yoneticiler/ekle.php?refurl='.q("refurl").'&id='.$yoneticioturum_yoneticiid	.'&klt=1');
			/*
			//die($anahtarkod);
			$url=sifrele("kategori=ayarlar&bolum=yoneticiler&islem=duzenle&id=".$yoneticiid,$anahtarkod);
			$url=urlencode($url);
			header('Location: /_y/?url='.$url);
			*/
		}
		else
		{
			if(strpos($admin_simdisayfalink,"/_y/s/guvenlik/kilit.php")===false)git('/_y/s/guvenlik/kilit.php');
		}
	}
}
function git($adres)
{
	global $admin_simdisayfalink;
	$konum = stripos($admin_simdisayfalink,$adres);
	if($konum === false)exit(header('Location: '.$adres));
}
function yoneticiislemleri($hangiislem,$neyapildi)
{
	global $yoneticioturum_kimlik,$db;

	$simdi=date("Y-m-d H:i:s");
	$YoneticilerLogID = teksatir("SELECT YoneticilerLogID FROM yoneticilerlog WHERE YoneticiLogKimlik='". $yoneticioturum_kimlik ."'","YoneticilerLogID");
	//die($YoneticilerLogID);
	$yl_s="
		INSERT
			into yoneticilerlogislemler(YoneticilerLogID,YoneticiLogIslemTarih,YoneticilerLogIslemListesiID,YoneticilerLogEylemListesiID)
			values('".S($YoneticilerLogID)."','". $simdi ."','". $hangiislem ."','". $neyapildi ."')
	";
	if($db->select($yl_s) !== TRUE)
	{
		//hatalogisle("YöneticiLog",$data->error);
	}
}
function teksatir($sql,$deger)
{
	global $db;
	$sonuc=$db->select($sql);
	if($sonuc)
	{
		return $sonuc[0][$deger];
	}else{
		return "";
		//hatalogisle("Tek satır: $sql",$data->error);
	}
}
function coksatir($sql)
{
	global $db;
	$sonuc=$db->select($sql);
	if($sonuc)
	{
		return $sonuc;
	}else{
		//hatalogisle("Çok Satır: $sql",$data->error);
	}
}
function coksatir_arr($sql)
{
	global $db;
	$sonuc=$db->select($sql);
	if($sonuc)
	{
		return $sonuc;
	}
	else{
		//hatalogisle("Çok Satır: $sql",$data->error);
	}
}
function dogrula($tablo,$degerler)
{
	global $db,$formhata,$formad;


	$dogrula_s="
		Select
			*
		from "
		.$tablo."
		where "
		.$degerler;

	if ($db->select($dogrula_s))
	{
		return true;
	}
	else
	{
		$formhata=1;
		//hatalogisle($formad,$data->error);
		return false;
	}
}
function guncelle($sutunlar,$degerler,$tablo,$sabit,$eylem)
{
	global $db,$formhataaciklama,$formhata;
	$deger=explode("|*_",$degerler);
	$sutun=explode(",",$sutunlar);
	$sql="";
	foreach ($deger as $i => $value)
	{
		$sql .= $sutun[$i]."=:deger".[$i].",";
		$params['deger'.$i]=$deger[$i];
	}
	$sql=rtrim($sql,",");

	$guncelle_s= " UPDATE ". $tablo ." SET ". $sql ." WHERE ". $sabit;

	$db->beginTransaction();
	if($updateStatus = $db->update($guncelle_s,$params))
	{
		$db->commit();
		yoneticiislemleri($eylem,3);
		$formhataaciklama="Güncelleme başarılı!";
		return $updateStatus;
		//hatalogisle($guncelle_s,$formhataaciklama);
	}
	else
	{
		$db->rollBack();
		//$hata=$db->error;
		//hatalogisle($tablo,$hata);
		$formhata=1;
		$formhataaciklama="Güncelleme başarısız";
		return 0;
	}
}
function ekle($sutunlar,$degerler,$tablo,$eylem)
{
	global $db,$formhataaciklama,$formhata;

	$deger=explode("|*_",$degerler);
	$sql="";
	foreach ($deger as $i => $value)
	{
		$sql .= ":deger". $i .",";
		$params['deger'.$i]=$deger[$i];
	}
	$sql=rtrim($sql,",");

	$ekle_s= " INSERT INTO ". $tablo ." ( ". $sutunlar .") VALUES (". $sql .")";

	$db->beginTransaction();
	if($addStatus = $db->insert($ekle_s,$params))
	{
		$db->commit();
		yoneticiislemleri($eylem,1);
		$formhataaciklama="Ekleme başarılı!";
		//hatalogisle($ekle_s,$formhataaciklama);
		return $addStatus;
	}
	else
	{
		$db->rollBack();
		//$hata=$data->error;
		//hatalogisle($tablo,$hata);
		$formhata=1;
		$formhataaciklama="Ekleme başarısız";
		return 0;
	}
}
function ekleguncelle($sutunlar,$degerler,$tablo,$sabit,$eylem)
{
	global $db,$formhataaciklama,$formhata;
	$deger=explode("|*_",$degerler);
	$sql="";
	foreach ($deger as $i => $value)
	{
		$sql .= ":deger". [$i] .",";
		$params['deger'.$i]=$deger[$i];
	}
	$sql=rtrim($sql,",");

	$ekle_s= " INSERT INTO ". $tablo ." ( ". $sutunlar .") VALUES (". $sql .") ON DUPLICATE KEY UPDATE '".$sabit."'";

	$db->beginTransaction();
	if($addStatus = $db->insert($ekle_s,$params))
	{
		$db->commit();
		yoneticiislemleri($eylem,1);
		$formhataaciklama="Ekleme başarılı!";
		return $addStatus;
	}
	else
	{
		$db->rollBack();
		$formhata=1;
		$formhataaciklama="Ekleme başarısız";
		return 0;
	}
}

function sil($tablo,$degerler,$eylem)
{
	global $db,$formhata,$formhataaciklama;

	$values = explode(",", $degerler);
	$where = " WHERE ";
	$params = [];
	foreach ($values as $value) {
		$val = explode("='", rtrim($value, "'"));

		$where .= $val[0] . " = :" . $val[0] . " AND ";
		$params[$val[0]] = $val[1];
	}
	$where = rtrim($where, " AND ");

	$sil_s= " DELETE FROM ". $tablo . $where;

	$db->beginTransaction();
	if($deleteStatus = $db->delete($sil_s,$params)>0)
	{
		$db->commit();
		return $deleteStatus;
		//hatalogisle("$tablo (sil)","başarılı");
	}
	else
	{
		$db->rollBack();
		$formhata=1;
		return 0;
		//$formhataaciklama="Silme işlemi başarısız<br>".$data->error;
	}
}
$seokatad="";
function kategorial($id)
{
	global $seokatad,$db;
	$seokat_s="Select kategoriad,ustkategoriid FROM kategori Where kategorisil='0' and kategoriid=:id";
	$params=array('id'=>$id);
	$seokat_v=$db->select($seokat_s,$params);
	if($seokat_v)
	{
		foreach($seokat_v as $seokat_t)
		{
			$seoustkatid=$seokat_t['ustkategoriid'];
			if(BosMu($seokatad)){$seokatad=$seokat_t['kategoriad'];}else{$seokatad=$seokat_t['kategoriad']."/".$seokatad;}
			if($seoustkatid!=0)kategorial($seoustkatid);
		}
	}
}
function birlerBul($birlerBasamagi)
{
	$deger="";
	switch ($birlerBasamagi) {
		case "0": $deger = "sıfır "; break;
		case "1": $deger = "bir "; break;
		case "2": $deger = "iki "; break;
		case "3": $deger = "üç "; break;
		case "4": $deger = "dört "; break;
		case "5": $deger = "beş "; break;
		case "6": $deger = "altı "; break;
		case "7": $deger = "yedi "; break;
		case "8": $deger = "sekiz "; break;
		case "9": $deger = "dokuz "; break;
		default:return $deger;
	}
	return $deger;
}
function onlarBul($onlarBasamagi)
{
	$deger="";
	switch ($onlarBasamagi)
	{
		case "1": $deger = "on "; break;
		case "2": $deger = "yirmi "; break;
		case "3": $deger = "otuz "; break;
		case "4": $deger = "kırk "; break;
		case "5": $deger = "elli "; break;
		case "6": $deger = "altmış "; break;
		case "7": $deger = "yetmiş "; break;
		case "8": $deger = "seksen "; break;
		case "9": $deger = "doksan "; break;
	}
	return $deger;
}
function yaziyaCevir($a)
{
	//Aşağıdaki fonksiyonda bir sayı okunurken kullanılan ifadeler dikkate alınarak,
	// rakamların 0 ile 9 arasında olması durumunda nasıl ifade edileceği
	//gösterilmiştir. switch case yerine if else de kullanarak aynı işlemleri yapabilirdik.
	//Aşağıdaki fonksiyonda ise yine sayının okunuşu düşünülerek yazılmıştır.

	$sayi=""; $onluk=""; $birlik=""; $yuzluk=""; $binlik="";
	$onbinlik="";$yuzbinlik=""; $milyon=""; $milyar="";
	$basamaksay = 0;
	$kurusvasmi=strpos($a,",");
	$kurus="";
	if($kurusvasmi!==false)
	{
		$kurus=yaziyaCevir(explode(",",$a)[1])." kuruş";
		$a=explode(",",$a)[0];
	}
	$basamaksay = strlen($a);
	if ($basamaksay == 1) {
		$sayi= birlerBul($a);
	}
	// basamak sayısı 2 ise,
	// burada kullanılan substr($a, 0 ,1) ifadesi $a metninin ilk karekterinden
	// bir tane al demektir, sayı 12 ise burada 1 alınacaktır. Numaralandırma
	// dikkat ederseniz sıfırdan başlamaktadır.
	if ($basamaksay == 2)
	{
		$onluk = substr($a,0,1);
		$birlik = substr($a,1,1);

		$sayi= onlarBul($onluk);

		if ($birlik!="0")
			$sayi.=birlerBul($birlik);
	}

	// basamak sayısı 3 ise
	// Sayımız üç basamaklı ise birler, onlar ve yüzler basamağını ayrıştırıp,
	// buna göre ilgili fonksiyonları çağırıyoruz.  Bundan sonraki işlemler
	// hep aynı şekilde benzer mantıkla  tekrarlanıyor. 011, 0234 gibi
	// sayının önünde sıfır varsa hesaba katmıyoruz.

	if ($basamaksay == 3)
	{
		$yuzluk = substr($a,0,1);
		$onluk = substr($a,1, 1);
		$birlik = substr($a, 2, 1);
		if ($yuzluk!="1" && $yuzluk!="0")
			$sayi= birlerBul($yuzluk)."yüz ";
		if ($yuzluk == "1")
			$sayi = "yüz ";
		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	// basamak sayısı 4 ise
	if ($basamaksay == 4)
	{
		$binlik = substr($a,0, 1);
		$yuzluk = substr($a,1, 1);
		$onluk  = substr($a,2, 1);
		$birlik = substr($a,3, 1);
		if ($binlik != "1")
			$sayi = birlerBul($binlik)."bin ";
		if ($binlik == "1")
			$sayi = "bin ";
		if ($yuzluk != "1" && $yuzluk != "0")
			$sayi .= birlerBul($yuzluk) . "yüz ";
		else if ($yuzluk == "1")
			$sayi .= "yüz ";
		else if ($yuzluk == "0")
			$sayi .= "";

		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	// basamak sayısı 5 ise
	if ($basamaksay == 5)
	{
		$onbinlik = substr($a,0, 1);
		$binlik = substr($a,1, 1);
		$yuzluk = substr($a,2, 1);
		$onluk = substr($a,3, 1);
		$birlik = substr($a,4, 1);

		if ($onbinlik != "0")
			$sayi = onlarBul($onbinlik);
		if ($binlik != "0")
			$sayi .= birlerBul($binlik) . "bin ";
		else $sayi .= "bin ";
		if ($yuzluk != "1" && $yuzluk!="0")
			$sayi .= birlerBul($yuzluk) . "yüz ";
		else if ($yuzluk == "1")
			$sayi .= "yüz ";
		else if ($yuzluk == "0")
			$sayi .= "";
		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	// basamak sayısı 6 ise
	if ($basamaksay == 6)
	{
		$yuzbinlik= substr($a,0, 1);
		$onbinlik = substr($a,1, 1);
		$binlik = substr($a,2, 1);
		$yuzluk = substr($a,3, 1);
		$onluk = substr($a,4, 1);
		$birlik = substr($a,5, 1);
		if ($yuzbinlik != "0" && $yuzbinlik != "1")
			$sayi = birlerBul($yuzbinlik) ."yüz ";
		else if ($yuzbinlik == "1")
			$sayi = "yüz ";
		else $sayi .= "";
		if ($onbinlik != "0")
			$sayi .= onlarBul($onbinlik);
		if ($binlik != "0")
			$sayi .= birlerBul($binlik) . "bin ";
		else $sayi .= "bin ";
		if ($yuzluk != "1" && $yuzluk != "0")
			$sayi .= birlerBul($yuzluk) . "yüz ";
		else if ($yuzluk == "1")
			$sayi .= "yüz ";
		else if ($yuzluk == "0")
			$sayi .= "";
		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	// basamak sayısı 7 ise
	if ($basamaksay == 7)
	{
		$milyon = substr($a,0, 1);
		$yuzbinlik = substr($a,1, 1);
		$onbinlik = substr($a,2, 1);
		$binlik = substr($a,3, 1);
		$yuzluk = substr($a,4, 1);
		$onluk = substr($a,5, 1);
		$birlik =substr($a,6, 1);

		if ($milyon != "0")
			$sayi = birlerBul($milyon) . "milyon ";
		else $sayi = "";
		if ($yuzbinlik != "0")
			$sayi .= birlerBul($yuzbinlik) ."yüz ";
		else $sayi .= "";
		if ($onbinlik != "0")
			$sayi .= onlarBul($onbinlik);
		if ($binlik != "0")
			$sayi .= birlerBul($binlik) . "bin ";
		else $sayi .= "bin ";
		if ($yuzluk != "1" && $yuzluk != "0")
			$sayi .= birlerBul($yuzluk) . "yüz ";
		else if ($yuzluk == "1")
			$sayi .= "yüz ";
		else if ($yuzluk == "0")
			$sayi .= "";
		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	// basamak sayısı 8 ise
	if ($basamaksay == 8)
	{
		$onmilyon=substr($a,0, 1);
		$milyon = substr($a,1, 1);
		$yuzbinlik = substr($a,2, 1);
		$onbinlik = substr($a,3, 1);
		$binlik = substr($a,4, 1);
		$yuzluk = substr($a,5, 1);
		$onluk = substr($a,6, 1);
		$birlik =substr($a,7, 1);

		if ($onmilyon != "0")
			$sayi = onlarBul($onmilyon);
		if ($milyon != "0")
			$sayi.= birlerBul($milyon) . "milyon ";
		else $sayi = "";
		if ($yuzbinlik != "0")
			$sayi .= birlerBul($yuzbinlik) ."yüz ";
		else $sayi .= "";
		if ($onbinlik != "0")
			$sayi .= onlarBul($onbinlik);
		if ($binlik != "0")
			$sayi .= birlerBul($binlik) . "bin ";
		else $sayi .= "bin ";
		if ($yuzluk != "1" && $yuzluk != "0")
			$sayi .= birlerBul($yuzluk) . "yüz ";
		else if ($yuzluk == "1")
			$sayi .= "yüz ";
		else if ($yuzluk == "0")
			$sayi .= "";
		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	// basamak sayısı 9 ise
	if ($basamaksay == 9)
	{
		$yuzmilyon=substr($a,0, 1);
		$onmilyon=substr($a,1, 1);
		$milyon = substr($a,2, 1);
		$yuzbinlik = substr($a,3, 1);
		$onbinlik = substr($a,4, 1);
		$binlik = substr($a,5, 1);
		$yuzluk = substr($a,6, 1);
		$onluk = substr($a,7, 1);
		$birlik =substr($a,8, 1);

		if ($yuzmilyon != "0" && $yuzmilyon != "1")
			$sayi.= birlerBul($yuzmilyon) ."yüz ";
		else if ($yuzmilyon == "1")
			$sayi.= "yüz ";
		else $sayi.= "";
		if ($onmilyon != "0")
			$sayi .= onlarBul($onmilyon);
		if ($milyon != "0")
			$sayi.= birlerBul($milyon) . "milyon ";
		else $sayi = "";
		if ($yuzbinlik != "0")
			$sayi .= birlerBul($yuzbinlik) ."yüz ";
		else $sayi .= "";
		if ($onbinlik != "0")
			$sayi .= onlarBul($onbinlik);
		if ($binlik != "0")
			$sayi .= birlerBul($binlik) . "bin ";
		else $sayi .= "bin";
		if ($yuzluk != "1" && $yuzluk != "0")
			$sayi .= birlerBul($yuzluk) . "yüz ";
		else if ($yuzluk == "1")
			$sayi .= "yüz";
		else if ($yuzluk == "0")
			$sayi .= "";
		if ($onluk != "0")
			$sayi .= onlarBul($onluk);
		if ($birlik != "0")
			$sayi .= birlerBul($birlik);
	}
	if(!BosMu($kurus))$kurus=str_replace("Türk Lirası ","",$kurus);
	return $sayi." Türk Lirası ".$kurus;
}
function sayfala($link,$toplamsayfa,$simdikisayfa)
{
	$gerilink=$simdikisayfa-1;
	if($gerilink<1)$gerilink=1;$geriek="disabled";
	$ilerilink=$simdikisayfa+1;
	$degisken="?";
	if( strpos($link , "?") !== false )$degisken="&";
	if($ilerilink>$toplamsayfa)$ilerilink=$toplamsayfa;$ileriek="disabled";
	echo '<nav><ul class="pagination pagination-gap"><li class="'.$geriek.' page-item"><a class="page-link" href="'.$link.$degisken.'sayfa='.$gerilink.'" aria-label="Previous"><span aria-hidden="true">«</span></a></li>';
	for($i=1;$i<=$toplamsayfa;$i++)
	{
		if($simdikisayfa==$i)
		{
			echo '
 			<li class="active page-item"><a class="page-link" href="'.$link.$degisken.'sayfa='.$i.'">'.$i.' <span class="sr-only">(current)</span></a></li>
        ';
		}else{
			echo '
            <li class="page-item"><a class="page-link" href="'.$link.$degisken.'sayfa='.$i.'">'.$i.'</a></li>
        ';
		}

	}
	echo '<li class="page-item"><a class="'.$ileriek.' page-link" href="'.$link.$degisken.'sayfa='.$ilerilink.'" aria-label="Next"><span aria-hidden="true">»</span></a></li></ul></nav>';
}
//bu fonksiyon yönetici giriş epostasını gönderirken log yazılması içindir.
// hatalogişle burada yemiyor. Çünkü mail fonksiyonu içinde logyaz olarak tanımlanmış
function logyaz($islem,$hata)
{
	global $anadizin,$ziyaretci_benzersiz,$ziyaretci_ip;
	if(BosMu($hata))$hata="data tanımlanmamış";
	if(!file_exists($anadizin.'/log/panel')){mkdir($anadizin.'/log/panel', 0777, true);}
	$logsayfa = $anadizin.'/log/panel/'.date("Y-m-d").'.txt';
	$yazilacak=date("Y-m-d H:i:s").'|'.$ziyaretci_benzersiz.'|'.$ziyaretci_ip.'|'.$islem.'|'. $hata .'*';
	$yazilacak=iconv("UTF-8", "ISO-8859-9",$yazilacak);
	file_put_contents($logsayfa,$yazilacak.PHP_EOL,FILE_APPEND);
	unset($islem,$hata);
}
?>
