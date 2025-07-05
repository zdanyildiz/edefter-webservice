<?php //GÜVENNLİK
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var AdminSession $adminSession
 */

require_once($anadizin."/_y/s/f/fonksiyon.php");

$adminCasper = $adminSession->getAdminCasper();
$loginStatus = $adminCasper->isLoginStatus();


if($loginStatus)
{
	$admin = $adminCasper->getAdmin();


	$yoneticioturum_kimlik		=$admin["sessionID"] ?? null;
	$yoneticioturum_yoneticiid	=$admin["YoneticiID"];
	$yoneticioturum_yetki		=$admin["YoneticiYetki"];
	$yoneticioturum_adsoyad		=$admin["YoneticiAdSoyad"];
	$yoneticioturum_eposta		=$admin["YoneticiEposta"];
	$yoneticioturum_giristarihi	=$admin["yoneticigiristarihi"];
	$yoneticioturum_kilitdurum	=$admin["kilitdurum"];
	$yoneticioturum_resim		=$admin["YoneticiResim"];
	//yönetici oturumu kapatmadan ekranı kitlemiş mi
	if($yoneticioturum_kilitdurum==1)
	{
		if(S(q("klt"))==0)
		{
			if(strpos($admin_simdisayfalink,"/_y/s/guvenlik/kilit.php")===false)git('/_y/s/guvenlik/kilit.php');
		}
	}
	if($yoneticioturum_yetki==0)
	{
		$yoneticiyetkiad="Süper Yönetici";
	}
	elseif($yoneticioturum_yetki==1)
	{
		$yoneticiyetkiad="Yönetici";
	}
	elseif($yoneticioturum_yetki==2)
	{
		$yoneticiyetkiad="Kullanıcı";
	}
	yoneticisongiris();

	//if(!$data)Veri(true);

	$sitetip=teksatir("select sitetip from ayargenel where ayargenelid='1'","sitetip");
	if(S($sitetip)==1)$eticaretdurum=1;

	$dilkontrol=false;
	$dilkontrol=dogrula("dil"," dilsil='0' and dilaktif='1' and anadil='1'");
	if ($dilkontrol==true)
	{
		$dilid=teksatir(" Select dilid from dil Where dilsil='0' and dilaktif='1' and anadil='1'","dilid");
	}
	else
	{
		if(S(q("yonlendir"))==0)
		{
			exit(gitt("/_y/s/s/diller/dilekle.php?yonlendir=1"));
		}
	}

	if(S($dilid)!=0 && $eticaretdurum==1)
	{
		$fiyatayar_s="
			SELECT
				fiyatgoster,eskifiyat,parabirim,taksit,kdv,kredikarti,kapidaodeme,havale
			FROM
				ayarfiyat
			WHERE
				dilid='".$dilid."'
		";
		if($db->select($fiyatayar_s))
		{
			$fiyatayar_t=$db->select($fiyatayar_s)[0];


					$eskifiyatgenel=$fiyatayar_t["eskifiyat"];
					$parabirimgenel=$fiyatayar_t["parabirim"];
					$taksitgenel=$fiyatayar_t["taksit"];
					$kdvgenel=$fiyatayar_t["kdv"];
					$kredikartigenel=$fiyatayar_t["kredikarti"];
					$kapidaodemegenel=$fiyatayar_t["kapidaodeme"];
					$havaleodemegenel=$fiyatayar_t["havale"];

		}
		$hataliislemkk=0;
		$sparisyenikk=0;
		$sparisyenibh=0;
		$sparisyeniko=0;
		$siparisiade=0;
		$siparisiptal=0;
		$siparisdegisim=0;
		$siparistedarik=0;
		$siparishazir=0;
		$siparishazirlaniyor=0;
		$sipariskargo=0;
		$siparisteslim=0;
		$sparisyenikk=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='1' and siparisodemeyontemi='kk' and siparisdurum='2' and siparissil='0'","toplam");
		$sparisyenibh=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='2' and siparisodemeyontemi='bh' and siparisdurum='1' and siparissil='0'","toplam");
		$hataliislemkk=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='0' and siparisodemeyontemi='kk' and siparisdurum!='11' and siparissil='0'","toplam");
		$sparisyeniko=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='3' and siparisodemeyontemi='ko' and siparissil='0'","toplam");
		$siparisiade=teksatir("select count(*) as toplam from uyesiparis where siparisdurum='5' and siparissil='0'","toplam");
		$siparisiptal=teksatir("select count(*) as toplam from uyesiparis where siparisdurum='8' and siparissil='0'","toplam");
		$siparisdegisim=teksatir("select count(*) as toplam from uyesiparis where siparisdurum='7' and siparissil='0'","toplam");
		$siparistedarik=teksatir("select count(*) as toplam from uyesiparis where siparisdurum='9' and siparissil='0'","toplam");
		$siparishazir=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='1' and siparisdurum='0' and siparissil='0'","toplam");
		$siparishazirlaniyor=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='1' and siparisdurum='2' and siparissil='0'","toplam");
		$sipariskargo=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='1' and siparisdurum='3' and siparissil='0'","toplam");
		$siparisteslim=teksatir("select count(*) as toplam from uyesiparis where siparisodemedurum='1' and siparisdurum='4' and siparissil='0'","toplam");

		$okunmamis_yorum=teksatir("SELECT COUNT(yorumbildirim) AS okunmamis FROM yorum WHERE yorumbildirim='0' and yorumsil='0' and uyeid!='0'","okunmamis");
		$okunmamis_mesaj=teksatir("SELECT COUNT(mesajbildirim) AS okunmamis FROM sorusor WHERE mesajbildirim='0' and mesajsil='0' and uyeid!='0'","okunmamis");
		$okunmamis_iletisim=teksatir("SELECT COUNT(formbildirim) AS okunmamis FROM formiletisim WHERE formbildirim='0' and formsil='0'","okunmamis");
		$okunmamis_iptal=teksatir("SELECT COUNT(talepbildirim) AS okunmamis FROM iptaliadedegisim WHERE talepbildirim='0' and talepsil='0' and uyeid!='0' and degisimtur='İptal'","okunmamis");
		$okunmamis_degisim=teksatir("SELECT COUNT(talepbildirim) AS okunmamis FROM iptaliadedegisim WHERE talepbildirim='0' and talepsil='0' and uyeid!='0' and degisimtur='Değişim'","okunmamis");
		$okunmamis_iade=teksatir("SELECT COUNT(talepbildirim) AS okunmamis FROM iptaliadedegisim WHERE talepbildirim='0' and talepsil='0' and uyeid!='0' and degisimtur='İade'","okunmamis");
	}
	else
	{
		$dilid=teksatir("select dilid from dil where anadil='1'","dilid");
		if(S($dilid)==0)$dilid=teksatir("select dilid from dil where dilid>'0'","dilid");
	}
	if(!dogrula("ayarfirma","ayarfirmaid>'0'"))
	{
		$formhata			=1;
		$formhataaciklama	='<h3>Firma bilgisi girmemişsiniz.</h3> Google için ve sitenin doğru çalışması için önemli. Firma bilgilerini girmek için <a href="/_y/s/s/firmabilgileri/unvan.php" class="btn ink-reaction btn-raised btn-xs btn-primary style-warning">tıklayın</a>';
	}
	logoal($dilid);
}
?>
