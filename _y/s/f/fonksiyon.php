<?php
//güvenlik
$inj = array ('select', 'insert', 'delete', 'update', 'drop table', 'union', 'null', 'SELECT', 'INSERT', 'DELETE', 'UPDATE', 'DROP TABLE', 'UNION', 'NULL','order by','order by');
for ($i = 0; $i < sizeof ($_GET); ++$i)
{
	for ($j = 0; $j < sizeof ($inj); ++$j){
		foreach($_GET as $gets){
			if(preg_match ('/' . $inj[$j] . '/', $gets)){
				$temp = key ($_GET);
				$_GET[$temp] = '';
				die('hatalı karakter');
				continue;
			}
		}
	}
}
/*
if(!isset($_SESSION['admin']) && S(f("teklifform"))==0)
{
	for ($i = 0; $i < sizeof ($_POST); ++$i)
	{
		for ($j = 0; $j < sizeof ($inj); ++$j){
			foreach($_POST as $posts){
				if(preg_match ('/' . $inj[$j] . '/', $posts))
				{
					$temp = key ($_POST);
					$_POST[$temp] = '';
					die('hatalı karakter');
					continue;
				}
			}
		}
	}
}*/
function is_bot($sistema){
 // bots de buscadores
    $bots = array(
         'Googlebot'
        , 'Baiduspider'
        , 'ia_archiver'
        , 'R6_FeedFetcher'
        , 'NetcraftSurveyAgent'
        , 'Sogou web spider'
        , 'bingbot'
        , 'Yahoo! Slurp'
        , 'facebookexternalhit'
        , 'PrintfulBot'
        , 'msnbot'
        , 'Twitterbot'
        , 'UnwindFetchor'
        , 'urlresolver'
        , 'Butterfly'
        , 'TweetmemeBot'
        , 'PaperLiBot'
        , 'MJ12bot'
        , 'AhrefsBot'
        , 'Exabot'
        , 'Ezooms'
        , 'YandexBot'
        , 'SearchmetricsBot'
        , 'picsearch'
        , 'TweetedTimes Bot'
        , 'QuerySeekerSpider'
        , 'ShowyouBot'
        , 'woriobot'
        , 'merlinkbot'
        , 'BazQuxBot'
        , 'Kraken'
        , 'SISTRIX Crawler'
        , 'R6_CommentReader'
        , 'magpie-crawler'
        , 'GrapeshotCrawler'
        , 'PercolateCrawler'
        , 'MaxPointCrawler'
        , 'R6_FeedFetcher'
        , 'NetSeer crawler'
        , 'grokkit-crawler'
        , 'SMXCrawler'
        , 'PulseCrawler'
        , 'Y!J-BRW'
        , '80legs.com/webcrawler'
        , 'Mediapartners-Google'
        , 'Spinn3r'
        , 'InAGist'
        , 'Python-urllib'
        , 'NING'
        , 'TencentTraveler'
        , 'Feedfetcher-Google'
        , 'mon.itor.us'
        , 'spbot'
        , 'Feedly'
        , 'bitlybot'
        , 'ADmantX Platform'
        , 'Niki-Bot'
        , 'Pinterest'
        , 'python-requests'
        , 'DotBot'
        , 'HTTP_Request2'
        , 'linkdexbot'
        , 'A6-Indexer'
        , 'Baiduspider'
        , 'TwitterFeed'
        , 'Microsoft Office'
        , 'Pingdom'
        , 'BTWebClient'
        , 'KatBot'
        , 'SiteCheck'
        , 'proximic'
        , 'Sleuth'
        , 'Abonti'
        , '(BOT for JCE)'
        , 'Baidu'
        , 'Tiny Tiny RSS'
        , 'newsblur'
        , 'updown_tester'
        , 'linkdex'
        , 'baidu'
        , 'searchmetrics'
        , 'genieo'
        , 'majestic12'
        , 'spinn3r'
        , 'profound'
        , 'domainappender'
        , 'VegeBot'
        , 'terrykyleseoagency.com'
        , 'CommonCrawler Node'
        , 'AdlesseBot'
        , 'metauri.com'
        , 'libwww-perl'
        , 'rogerbot-crawler'
        , 'MegaIndex.ru'
    		, 'ltx71'
    		, 'Qwantify'
    		, 'Traackr.com'
    		, 'Re-Animator Bot'
        , 'Pcore-HTTP'
        , 'BoardReader'
        , 'omgili'
        , 'okhttp'
        , 'CCBot'
        , 'Java/1.8'
        , 'semrush.com'
        , 'feedbot'
        , 'CommonCrawler'
        , 'AdlesseBot'
        , 'MetaURI'
        , 'ibwww-perl'
        , 'rogerbot'
        , 'MegaIndex'
        , 'BLEXBot'
        , 'FlipboardProxy'
        , 'techinfo@ubermetrics-technologies.com'
        , 'trendictionbot'
        , 'Mediatoolkitbot'
        , 'trendiction'
        , 'ubermetrics'
        , 'ScooperBot'
        , 'TrendsmapResolver'
        , 'Nuzzel'
        , 'Go-http-client'
        , 'Applebot'
        , 'LivelapBot'
        , 'GroupHigh'
        , 'SemrushBot'
        , 'ltx71'
        , 'commoncrawl'
        , 'istellabot'
        , 'DomainCrawler'
        , 'cs.daum.net'
        , 'StormCrawler'
        , 'GarlikCrawler'
        , 'The Knowledge AI'
        , 'getstream.io/winds'
        , 'YisouSpider'
        , 'archive.org_bot'
        , 'semantic-visions.com'
        , 'FemtosearchBot'
        , '360Spider'
        , 'linkfluence.com'
        , 'glutenfreepleasure.com'
        , 'Gluten Free Crawler'
        , 'YaK/1.0'
        , 'Cliqzbot'
        , 'app.hypefactors.com'
        , 'axios'
        , 'semantic-visions.com'
        , 'webdatastats.com'
        , 'schmorp.de'
        , 'SEOkicks'
        , 'DuckDuckBot'
        , 'Barkrowler'
        , 'ZoominfoBot'
        , 'Linguee Bot'
        , 'Mail.RU_Bot'
        , 'OnalyticaBot'
        , 'Linguee Bot'
        , 'admantx-adform'
        , 'Buck/2.2'
        , 'Barkrowler'
        , 'Zombiebot'
        , 'Nutch'
        , 'SemanticScholarBot'
        , 'Jetslide'
        , 'scalaj-http'
        , 'XoviBot'
        , 'sysomos.com'
        , 'PocketParser'
        , 'newspaper'
        );
 // si lo encuentra devuelve true, si no, false
    foreach($bots as $b)
        {
            if( stripos( $sistema, $b ) !== false ) return true;
        }
    return false;
}
//güvenlik
function BosMu($deger)
{
    if(is_array($deger)) {
        $bdeger = $deger;
    } else {
        $bdeger = $deger !== null ? trim($deger) : '';
    }

    if(empty($bdeger) && $bdeger !== "0" && $bdeger !== 0) {
        return true;
    }

    return false;
}
function q($deger)
{
    global $simdi_adres;

    if(BosMu($deger)) return;
    if(isset($_GET[$deger]))
    {
        $qdeger=$_GET[$deger];
        return addslashes(htmlspecialchars($qdeger));
    }
    elseif($simdi_adres !== null && strpos($simdi_adres, $deger)!==false)
    {
        $qdeger=mb_strrchr($simdi_adres, $deger."=", false);
        $qdeger=str_replace($deger."=", "", $qdeger);
        if(strpos($qdeger,"&")!==false)$qdeger=explode('&',$qdeger)[0];
        return urldecode($qdeger);
    }
}
function f($deger)
{
    if(BosMu($deger)) return;
    if(isset($_POST[$deger]))
    {
        $fdeger=$_POST[$deger];
        if(is_array($_POST[$deger]))return $fdeger; else return addslashes(htmlspecialchars(trim($fdeger)));
    }
}
function S($deger)
{
	if(!BosMu($deger))
	{
		if(strpos($deger," ")===false&&strpos($deger,".")===false&&strpos($deger,",")===false)
		{
			$deger=strval($deger);
			if(ctype_digit($deger))
			{
				return intval($deger);
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}
function Para($deger)
{
    if(!BosMu($deger))
    {
        $deger=trim($deger);
        $deger=str_replace(",",".",$deger);
        if(preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $deger))
        {
            return $deger;
        }
        else
        {
            return "0.00";
        }
    }
    else
    {
        return "0.00";
    }
}
function gitt($adres)
{
	exit(header('Location: '.$adres));
}
function B($deger)
{
	$bdeger=trim($deger); unset($deger);
	if(BosMu($bdeger))
	{
		unset($bdeger); return;
	}
	$buyuk=array("Ş","Ü","Ö","Ç","Ş","Ğ","İ","I","Ö","Ç","Ü");
	$kucuk=array("ş","ü","ö","ç","ş","ğ","i","ı","ö","ç","ü");
	$bdeger=str_replace($kucuk,$buyuk,$bdeger);
	return mb_convert_case($bdeger, MB_CASE_UPPER, "UTF-8").'';;
	unset($bdeger);
}
function K($deger)
{
	$kdeger=trim($deger); unset($deger);
	if(BosMu($kdeger))
	{
		unset($kdeger); return;
	}
	$buyuk=array("Ş","Ü","Ö","Ç","Ş","Ğ","İ","I","Ö","Ç","Ü");
	$kucuk=array("ş","ü","ö","ç","ş","ğ","i","ı","ö","ç","ü");
	$kdeger=str_replace($buyuk,$kucuk,$kdeger);
	return mb_convert_case($kdeger, MB_CASE_LOWER, "UTF-8");
	unset($kdeger);
}
/*function BK($deger)
{
	$bkdeger=trim($deger); unset($deger);
	if(BosMu($bkdeger)){
		unset($bkdeger); return;
	}
	return ucwords($bkdeger);
	unset($bkdeger);
}*/
function BK($metin)
{
    $gelen="";
    $bol = explode(" ",$metin);
    foreach ($bol as $bolen)
    {
        $gelen = $gelen . trtekkelime($bolen) . " ";
    }
    return $gelen;
}
function trtekkelime($gelen)
{
    $uzunluk=strlen($gelen);
    $ilkharf = mb_substr($gelen,0,1,"UTF-8");
    $sonrakiharfler = mb_substr($gelen,1,$uzunluk,"UTF-8");
    $bir = array('ö','ç','i','ş','ğ','ü');
    $iki = array('Ö','Ç','İ','Ş','Ğ','Ü');
    $buyumus = str_replace($bir,$iki,$ilkharf);
    return ucwords($buyumus).K($sonrakiharfler);
}
function TR($deger)
{
	$trdeger=trim($deger); unset($deger);
	if(BosMu($trdeger)){
		unset($trdeger); return;
	}
	$buyuk=array("Ş","Ü","Ö","Ç","Ğ","İ","ş","ü","ö","ç","ğ","ı");
	$kucuk=array("S","U","O","C","G","I","s","u","o","c","g","i");

	$trdeger =str_replace($buyuk,$kucuk,$trdeger);
	return $trdeger;
	unset($trdeger);
}
function BR($deger)
{
	$brdeger=trim($deger); unset($deger);
	if(BosMu($brdeger)){
		unset($breger); return;
	}
	$eski = array("&nbsp;", chr(10), chr(13),"<br />","<br>","<p>","</p>","  ");
	$yeni = array(" ", " ", " "," "," "," "," "," ");
	$brdeger=str_replace($eski,$yeni,$brdeger);
	if(strpos($brdeger, "  ")) $brdeger=str_replace($eski,$yeni,$brdeger);
	unset($eski); unset($yeni);
	return $brdeger;
	unset($brdeger);
}
function SifreUret($deger,$Nasil){

    if($Nasil==0) $chars = "0123456789";
	if($Nasil==1) $chars = "ABCDEFGHJKMNPRSTUVYZQWX";
	if($Nasil==2) $chars = "ABCDEFGHJKMNPRSTUVYZQWX23456789";
	if($Nasil==3) $chars = "abcdefghjklmnoprstuvyzqxABCDEFGHJKLMNOPRSTUVYZQWX0123456789%=*";
	unset($Nasil);
    return substr(str_shuffle($chars),0,$deger);
}
function Duzelt($deger){
	$ddeger=trim($deger); unset($deger);
	if(BosMu($ddeger))
	{
		unset($ddeger);return;
	}
	//$ddeger = TR($ddeger);
	$turkce=array("ş","Ş","ı","İ","I","ü","Ü","ö","Ö","ç","Ç","ğ","Ğ","Ø");
	$duzgun=array("s","s","i","i","i","u","u","o","o","c","c","g","g","cap");
	$ddeger=str_replace($turkce,$duzgun,$ddeger);
	$ddeger = K($ddeger);
	$ddeger = preg_replace("@[^A-Za-z0-9\-._]+@i"," ",$ddeger);
	$ddeger=str_replace(" ","-",$ddeger);
	$ddeger=str_replace("  ","-",$ddeger);
	$ddeger=str_replace("--","-",$ddeger);
	return $ddeger;
	unset($ddeger);
}
function DuzeltS($deger){
	$ddeger=trim($deger); unset($deger);
	if(BosMu($ddeger))
	{
		unset($ddeger);return;
	}
	//$ddeger = TR($ddeger);
    $turkce=array("ş","Ş","ı","İ","I","ü","Ü","ö","Ö","ç","Ç","ğ","Ğ");
    $duzgun=array("s","s","i","i","i","u","u","o","o","c","c","g","g");
	$ddeger=str_replace($turkce,$duzgun,$ddeger);
	$ddeger = K($ddeger);
	$ddeger = preg_replace("@[^A-Za-z0-9/\-._]+@i"," ",$ddeger);
	$ddeger=str_replace(" ","-",$ddeger);
	$ddeger=str_replace("  ","-",$ddeger);
	$ddeger=str_replace("--","-",$ddeger);
	return $ddeger;
	unset($ddeger);
}
function Tarih($deger,$saat)
{
	if(BosMu($deger)) $deger=date('d.m.Y');
	$gunler = array(
		'Pazartesi',
		'Salı',
		'Çarşamba',
		'Perşembe',
		'Cuma',
		'Cumartesi',
		'Pazar'
	);

	$aylar = array(
		'Ocak',
		'Şubat',
		'Mart',
		'Nisan',
		'Mayıs',
		'Haziran',
		'Temmuz',
		'Ağustos',
		'Eylül',
		'Ekim',
		'Kasım',
		'Aralık'
	);

    $ay = $aylar[date('m', strtotime($deger)) - 1];
    $gun = $gunler[date('N', strtotime($deger)) - 1];
    unset($gunler);unset($aylar);
    if($saat==0) return date('j ', strtotime($deger)) . $ay . date(' Y ', strtotime($deger)) . $gun;
    if($saat==1) return date('j ', strtotime($deger)) . $ay . date(' Y ', strtotime($deger)) . $gun . date(' H:i:s', strtotime($deger));
    unset($deger);
}
function Turkce($string){
	if(!BosMu($string))
	{
		$string = stripcslashes($string);
		$string=str_replace("''","'",$string);
		return $string;
	}
}
function AnasayfaDetayTemizle($deger)
{
	$dtdeger=trim($deger);
	if(BosMu($dtdeger)) return;
	$dtdeger=strip_tags($dtdeger);
	return mb_substr($dtdeger,0,240,'utf-8')."...";
	unset($dtdeger);
}
function HtmlTemizle($deger)
{
	$htmldeger=trim($deger);
	if(BosMu($htmldeger)) return;
	$htmldeger=strip_tags($htmldeger);
	return $htmldeger;
}
function CT($deger)
{
	$ctdeger=trim($deger);
	if(BosMu($ctdeger)) return;
	return str_replace('"',"'",$ctdeger);
	unset($ctdeger);
}
function LinkOlustur($SayfaID,$SayfaBaslik,$sTur)
{
	$SayfaID=S($SayfaID);
	if(BosMu($SayfaBaslik)) return;
	if(BosMu($sTur)) return;
	$lSayfaBaslik=Duzelt(K($SayfaBaslik));
	$tSayfaBaslik =str_replace('"',"'",$SayfaBaslik);
	$link = 'href="/'.$lSayfaBaslik.'-'.$SayfaID.$sTur.'.html" title="'.$tSayfaBaslik.'"';
	unset($SayfaID);unset($SayfaBaslik);unset($sTur);unset($tSayfaBaslik);unset($lSayfaBaslik);
	return str_replace("--","-",$link);
}
function ResimGetir($deger)
{
	$rdeger=trim($deger);
	if(BosMu($rdeger)) return;
	global $data;
	$Resimler="";
	$ResimListe_D=0;
	$ResimListe_S="Select ResimID From _ResimListe Where ID='$rdeger' ORDER BY ResimListeID ASC";
	$ResimListe_V = $data->query($ResimListe_S);
	if($ResimListe_V -> num_rows > 0 )
	{$ResimListe_D=1;}

	if(S($ResimListe_D)==1)
	{
		while($ResimListe_Sutun = $ResimListe_V->fetch_assoc())
		{
			$resimid = $ResimListe_Sutun['ResimID'];

			$Resim_D=0;
			$Resim_S="Select Resim,rKlasor,rEn,rBoy From _Resim Where ResimID=".$resimid;
			$Resim_V = $data->query($Resim_S);
			if( $Resim_V -> num_rows > 0 ) $Resim_D=1;

			if($Resim_D==1)
			{
				while($Resim_Sutun = $Resim_V->fetch_assoc())
				{
					$Resim=$Resim_Sutun['Resim'];
					/*echo $Resim_Sutun['Resim'].'<br>';*/

					$rKlasor=$Resim_Sutun['rKlasor'];
					$rEn=$Resim_Sutun['rEn'];
					$rBoy=$Resim_Sutun['rBoy'];
					if($rEn>1080){
						$rBoy=round((1080/$rEn)*$rBoy); $rEn=1080;
					}
					elseif($rEn==0 || $rBoy==0)
					{
						$rEn=600;$rBoy=400;
					}
					elseif($rEn<360){
						$rBoy=round((360/$rEn)*$rBoy); $rEn=360;
					}
					if(S($rKlasor)>0)
					{
						$ResimKlasor_D=0;
						$ResimKlasor_S="Select rkAd From _resimklasor Where rkID=".$rKlasor;
						$ResimKlasor_V = $data->query($ResimKlasor_S);
						if( $ResimKlasor_V -> num_rows > 0 ) $ResimKlasor_D=1;

						if(S($ResimKlasor_D)==1)
						{
							while($ResimKlasor_Sutun = $ResimKlasor_V->fetch_assoc())
							{
								$rKlasor=$ResimKlasor_Sutun['rkAd'];
							}
							unset($ResimKlasor_Sutun);
							if($Resimler=="")
							{
								$Resimler=$rKlasor."/".$Resim."?".$rEn."x".$rBoy;
							}
							else
							{
								$Resimler=$Resimler.",".$rKlasor."/".$Resim."?".$rEn."x".$rBoy;
							}
						}
						unset($ResimKlasor_D,$ResimKlasor_S,$ResimKlasor_V);
					}
				}
				unset($Resim_Sutun);
			}
			unset($Resim_D,$Resim_S,$Resim_V);
		}
		unset($ResimListe_Sutun);
	}
	unset($ResimListe_D,$ResimListe_S,$ResimListe_V);
	return $Resimler;
}
function zamanfarkbul($tarih1, $tarih2, $ayrac)
{
    $result="";
    list($y1, $a1, $g1) = explode($ayrac, $tarih1);
    list($y2, $a2, $g2) = explode($ayrac, $tarih2);
    $t1_timestamp = mktime('0', '0', '0', $a1, $g1, $y1);
    $t2_timestamp = mktime('0', '0', '0', $a2, $g2, $y2);
    if ($t1_timestamp > $t2_timestamp) {
        $result = ($t1_timestamp - $t2_timestamp) / 86400;
    } else
        if ($t2_timestamp > $t1_timestamp) {
            $result = ($t2_timestamp - $t1_timestamp) / 86400;
        }
    return $result;
}
function SayfaIcerikResimBul($deger)
{
	global $data;
	$riDegisken=array('.jpg"','.gif"','.png"','.bmp"');
	$ri_Deger=$deger;//TR()
	$ri_Deger=K($ri_Deger);
	//$ri_Deger=str_replace('"','*',$ri_Deger);

	if(strpos($ri_Deger,'.jpg"')===false && strpos($ri_Deger,'.gif"')===false && strpos($ri_Deger,'.png"')===false && strpos($ri_Deger,'.bmp"')===false) return;
	//die($ri_Deger);
	$riDurum=0;
	if(strpos($ri_Deger,'src="')!==false)
	{
		$basla=strpos($ri_Deger,'src="');
		$tam=strlen($ri_Deger);
		$ri_Deger=substr($ri_Deger,$basla, $tam-$basla);

		if(strpos($ri_Deger,">")!==false)
		{
			$basla=strpos($ri_Deger,">");
			$tam=strlen($ri_Deger);
			$ri_Deger=substr($ri_Deger,0, $basla);
		}
	}
	//die($ri_Deger);
	for ($i = 0; $i < 3; ++$i)
	{
		if (strpos($ri_Deger,$riDegisken[$i])!==false)
		{
			$resimbasla= strpos($ri_Deger,$riDegisken[$i]);
			$resimsonra=substr($ri_Deger,0, $resimbasla);

			if (strpos($resimsonra,'src="')!==false)
			{
				$resimbul=strpos($resimsonra,'src="');

				$resim=substr($resimsonra,$resimbul);

				$resim=str_replace('src="','',$resim).$riDegisken[$i];

				$resim=str_replace('"','',$resim);
				//die($resim);
				$iWidth="600"; $iHeight="400";
				//die($resimbul);
				$resimad =str_replace("/","",mb_strrchr($resim,'/',false));
				$sql = "Select rEn,rBoy From _resim WHERE Resim='".$resimad."'";
				$result = $data->query($sql);
				if ($result->num_rows > 0) {
				    // output data of each row
				    while($row = $result->fetch_assoc())
					{
				        $iWidth=$row["rEn"];$iHeight=$row["rBoy"];
				        if($iWidth>1080){
				        	$iHeight=round((1080/$iWidth)*$iHeight); $iWidth=1080;
				        }
						elseif($iWidth==0 || $iHeight==0)
						{
							$iWidth=600;$iHeight=400;
						}
						elseif($iWidth<360){
							$iHeight=round((360/$iWidth)*$iHeight); $iWidth=360;
						}
					}
				}
				//$GLOBALS['icerik_disresim']=1;
				//echo $resim."?".$iWidth."x".$iHeight; die;
				return $resim."?".$iWidth."x".$iHeight;
			}
		}
		/*else
		{
			die("resimyok");
		}*/
	}
}
function SMSGonder($baslik,$mesaj,$kime)
{
    $username = "globalpozitif";
    $password = "93cf993dacbd1992645ddff652dfc5cf";
    $baslik="GLOBAL PZTF";
    $url= "http://api.sms.digicell.com.tr:8080/api/smspost/v1";

    $smsxml="
        <sms>
            <username>$username</username>
            <password>$password</password>
            <header>$baslik</header>
            <validity>5</validity>
            <message>
                <gsm>
                    <no>$kime</no>
                </gsm>
                <msg><![CDATA[$mesaj]]></msg>
            </message>
        </sms>
    ";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$smsxml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml; charset=utf-8"));
    $http_response = curl_exec($ch);
    //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if(strlen($http_response) == 2){
        //echo "$http_code $http_response\n";
        //return false;
        logyaz("smsgonder","$http_response > $baslik > $kime > $mesaj");
    }else{
        logyaz("smsgonder","Göderim Başarılı > $baslik > $kime > $mesaj");
    }
    $balanceInfo = $http_response;
    //echo "MesajID : $balanceInfo";
    //return $balanceInfo;
}
function curlKullan($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
}
function ayikla($metin,$ayiklabasla,$ayiklabitir,$harictutbasla,$harictutbitir)
{
	$yenimetin="";$ayiklabaslasayi="";$ayiklabitirsayi="";
	$yeniharicmetin="";$harictutbaslasayi="";$harictutbitirsayi="";
	if(!BosMu($metin) && !BosMu($ayiklabasla) && !BosMu($ayiklabitir))
	{
		if(strpos($metin,$ayiklabasla)!==false && strpos($metin,$ayiklabitir)!==false)
		{
			$ayiklabaslasayi=stripos($metin,$ayiklabasla);
			$yenimetin=substr($metin,$ayiklabaslasayi,strlen($metin)-$ayiklabaslasayi);

			if(strpos($yenimetin,$ayiklabitir)!==false)
			{
				$ayiklabitirsayi=stripos($yenimetin,$ayiklabitir,strlen($ayiklabasla));
				$yenimetin =substr($yenimetin, 0,$ayiklabitirsayi+strlen($ayiklabitir));
				//die($yenimetin);
				if(!BosMu($harictutbasla) && !BosMu($harictutbitir))
				{
					//*****************************************
					if(strpos($yenimetin,$harictutbasla)!==false && strpos($yenimetin,$harictutbitir)!==false)
					{
						$harictutbaslasayi=stripos($yenimetin,$harictutbasla);
						$yeniharicmetin=substr($yenimetin,$harictutbaslasayi,strlen($yenimetin)-$harictutbaslasayi);
						if(strpos($yeniharicmetin,$harictutbitir)!==false)
						{
							$harictutbitirsayi=stripos($yeniharicmetin,$harictutbitir);
							$yeniharicmetin =substr($yeniharicmetin, 0,$harictutbitirsayi+strlen($harictutbitir));
							//die($yeniharicmetin);
						}
					}
					//********************************************
				}
				$metin = str_replace($yenimetin, $yeniharicmetin, $metin);
				if(strpos($metin,$ayiklabasla)!==false && strpos($metin,$ayiklabitir)!==false)
				{
					$metin = ayikla($metin,$ayiklabasla,$ayiklabitir,$harictutbasla,$harictutbitir);
				}
			}
		}
	}
	return $metin;
}
function ayikladegistir($metin,$ayiklabasla,$ayiklabitir,$ayiklabaslayerine,$ayiklabitiryerine)
{
	$yenimetin="";$ayiklabaslasayi="";$ayiklabitirsayi="";
	$yeniharicmetin="";$harictutbaslasayi="";$harictutbitirsayi="";
	if(!BosMu($metin) && !BosMu($ayiklabasla) && !BosMu($ayiklabitir))
	{
		if(stripos($metin,$ayiklabasla)!==false && stripos($metin,$ayiklabitir)!==false)
		{
			$ayiklabaslasayi=stripos($metin,$ayiklabasla);
			$yenimetin=substr($metin,$ayiklabaslasayi,strlen($metin)-$ayiklabaslasayi);

			if(strpos($yenimetin,$ayiklabitir)!==false)
			{
				$ayiklabitirsayi=stripos($yenimetin,$ayiklabitir);
				$yenimetin =substr($yenimetin, 0,$ayiklabitirsayi+strlen($ayiklabitir));
				if(!BosMu($ayiklabaslayerine) && !BosMu($ayiklabitiryerine))
				{
					$yeniDmetin=str_ireplace($ayiklabasla, $ayiklabaslayerine, $yenimetin);
					$yeniDmetin=str_ireplace($ayiklabitir, $ayiklabitiryerine, $yeniDmetin);
					$metin = str_ireplace($yenimetin, $yeniDmetin, $metin);

					if(stripos($metin, $ayiklabasla)!==false)
					{
						$metin=ayikladegistir($metin,$ayiklabasla,$ayiklabitir,$ayiklabaslayerine,$ayiklabitiryerine);
					}
				}
				return str_ireplace($yenimetin, $yeniharicmetin, $metin);
			}
		}
	}
	unset($ayiklabaslasayi,$yenimetin,$ayiklabaslasayi,$ayiklabitirsayi,$harictutbaslasayi,$yeniharicmetin,$harictutbaslasayi,$harictutbitirsayi);
	return $metin;
}
function idegistir($metin,$ayiklabasla,$ayiklabitir,$yerinekoy,$degistir,$nereye)
{
	$yenimetin="";$ayiklabaslasayi="";$ayiklabitirsayi="";
	$yeniharicmetin="";$harictutbaslasayi="";$harictutbitirsayi="";
	if(!BosMu($metin) && !BosMu($ayiklabasla) && !BosMu($ayiklabitir))
	{
		if(strpos($metin,$ayiklabasla)!==false && strpos($metin,$ayiklabitir)!==false)
		{
			$ayiklabaslasayi=stripos($metin,$ayiklabasla);
			$ayiklabaslasayi=$ayiklabaslasayi+strlen($ayiklabasla);
			$yenimetin=substr($metin,$ayiklabaslasayi,strlen($metin)-$ayiklabaslasayi);

			if(stripos($yenimetin,$ayiklabitir)!==false)
			{
				$ayiklabitirsayi=stripos($yenimetin,$ayiklabitir);
				$yenimetin =trim(substr($yenimetin, 0,$ayiklabitirsayi));
				if(!BosMu($yerinekoy) && !BosMu($degistir))
				{
					$yenimetin=str_ireplace('|*_', $yenimetin, $degistir);
					if($nereye==-1)
					{
						$yeniDmetin=str_ireplace($yerinekoy, $yenimetin." ".$yerinekoy, $metin);
					}
					else
					{
						$yeniDmetin=str_ireplace($yerinekoy, $yerinekoy." ".$yenimetin, $metin);
					}
					return $yeniDmetin;
				}

			}
		}
	}
	unset($ayiklabaslasayi,$yenimetin,$ayiklabaslasayi,$ayiklabitirsayi,$harictutbaslasayi,$yeniharicmetin,$harictutbaslasayi,$harictutbitirsayi);
	return $metin;
}
function buldegistir($metin,$bulbasla,$bulbitir,$degistirbasla,$degistirbitir)
{
	$yenimetin="";$bulbaslasayi="";$bulbitirsayi="";
	$degistirbaslasayi="";$yeniharicmetin="";$degistirbaslasayi="";$degistirbitirsayi="";
	if(!BosMu($metin) && !BosMu($bulbasla) && !BosMu($bulbitir))
	{
		if(stripos($metin,$bulbasla)!==false && stripos($metin,$bulbitir)!==false)
		{
			$bulbaslasayi=stripos($metin,$bulbasla);
			$yenimetin=substr($metin,$bulbaslasayi,strlen($metin)-$bulbaslasayi);
			//die($yenimetin);
			if(stripos($yenimetin,$bulbitir)!==false)
			{
				$bulbitirsayi=stripos($yenimetin,$bulbitir);
				$yenimetin =substr($yenimetin, 0,$bulbitirsayi+strlen($bulbitir));

				$yeniDmetin=str_ireplace($bulbasla, $degistirbasla, $yenimetin);

				$yeniDmetin1=substr(
					$yeniDmetin,
					stripos($yeniDmetin, $degistirbasla)+strlen($degistirbasla),
					strlen($yeniDmetin)-strlen($degistirbasla)
					);

				$yeniDmetin=$degistirbasla." ".str_ireplace($bulbitir, $degistirbitir,$yeniDmetin1);
				/*$yeniDmetin=str_replace("<img", "<*img", $yeniDmetin);
				$yeniDmetin=str_replace("<amp-img", "<*amp-img", $yeniDmetin);*/
				$metin = str_ireplace($yenimetin, $yeniDmetin, $metin);
				//die($metin);
				if(strpos($metin, $bulbasla)!==false)
				{
					//$metin = str_replace("<*amp-img", "<amp-img", $metin);
					$metin=buldegistir($metin,$bulbasla,$bulbitir,$degistirbasla,$degistirbitir);
				}
			}
		}
	}
	unset($bulbaslasayi,$yenimetin,$bulbaslasayi,$bulbitirsayi,$degistirbaslasayi,$yeniharicmetin,$degistirbaslasayi,$degistirbitirsayi);
	return $metin;
}
function temizledegistir($metin,$bulbasla,$bulbitir)
{
	$yenimetin="";$bulbaslasayi="";$bulbitirsayi="";
	$degistirbaslasayi="";
	if(!BosMu($metin) && !BosMu($bulbasla) && !BosMu($bulbitir))
	{
		if(strpos($metin,$bulbasla)!==false && strpos($metin,$bulbitir)!==false)
		{
			$bulbaslasayi=stripos($metin,$bulbasla);
			$yenimetin=substr($metin,$bulbaslasayi,strlen($metin)-$bulbaslasayi);
			//die("** $yenimetin");
			if(strpos($yenimetin,$bulbitir)!==false)
			{
				$bulbitirsayi=stripos($yenimetin,$bulbitir);
				$yenimetin =substr($yenimetin, 0,$bulbitirsayi+strlen($bulbitir));
				//die("## $yenimetin");
				$metin = str_replace($yenimetin.";", "", $metin);
				$metin = str_replace($yenimetin, "", $metin);
				//die($metin);
				if(strpos($metin, $bulbasla)!==false)
				{
					$metin=temizledegistir($metin,$bulbasla,$bulbitir);
				}
			}
		}
	}
	unset($bulbaslasayi,$yenimetin,$bulbaslasayi,$bulbitirsayi,$degistirbaslasayi);
	return $metin;
}
function icerikresimcevir($icerikgovde)
{
	global $siteDomain,$data,$protokol,$resimklasor,$resimdizin;
	$resimbul=mb_strstr($icerikgovde,'src="');
	if($resimbul)
	{
		//die("son:".$resimbul);
		$resimsonsayi=strpos($resimbul,'>',5);
		if($resimsonsayi!==false)
		{
			$resimson=substr($resimbul,5,$resimsonsayi-5);
			//die($resimson);
			if(strpos($resimson,"srcset")===false)
			{
				//die(substr($resimson,0,4));
				if(substr($resimson,0,4)!="http")
				{
					//777777777777777777777777777777777777
					if(strpos($resimson,'width="*"'))
					{
						$resimsonyeni	=str_replace('width="*" height="*"', 'width="**" height="**"', $resimson);
					}
					else
					{
						$resimsonyeni	=str_replace($resimson, $resimson.'width="**" height="**"', $resimson);
					}

					//die($resimson."<br>".$resimsonyeni);
					$icerikgovde	=str_replace($resimson,$resimsonyeni,$icerikgovde);

					$resimbulsayi 	=strpos($resimbul,'"',5);
					$resimbul 		=substr($resimbul,5,$resimbulsayi-5);
					$resimad 		=substr(strrchr($resimbul,"/"),1);

					$iWidth="600"; $iHeight="400";
					//die($resimbul);
					$sql = "Select rEn,rBoy From _resim WHERE Resim='".$resimad."'";
					$result = $data->query($sql);
					if ($result->num_rows > 0) {
					    // output data of each row
					    while($row = $result->fetch_assoc())
					    {
					        if(S($row["rEn"])!=0)$iWidth=$row["rEn"];
					        if(S($row["rBoy"])!=0)$iHeight=$row["rBoy"];
					    }
					    unset($row);
					    if($iWidth>1000)
					    {
				        	$iHeight=round((1000/$iWidth)*$iHeight); $iWidth=1000;
				        }
					}
					unset($sql,$result);
					//die($resimbul);
					$resim=str_replace($resimklasor, "", $resimbul);
					$resimbulek=' srcset="
					'.$resimdizin.'?g=1080&resim='.$resim.' 1080w, 
					'.$resimdizin.'?g=960&resim='.$resim.' 960w,
					'.$resimdizin.'?g=840&resim='.$resim.' 840w, 
					'.$resimdizin.'?g=720&resim='.$resim.' 720w,
					'.$resimdizin.'?g=600&resim='.$resim.' 600w, 
					'.$resimdizin.'?g=480&resim='.$resim.' 480w,
					'.$resimdizin.'?g=360&resim='.$resim.' 360w" layout="responsive';
					/*$icerikgovde	=str_replace(
						'src="http://'.$siteDomain.'/m/r/'.$resimbul,
						'src="*http://'.$siteDomain.'/m/r/'.$resimbul,
						$icerikgovde
					);*/
					$icerikgovde	=str_replace(
						'src="'.$resimbul,
						'src=*"'.$resimdizin.$resim.'"'.$resimbulek,
						$icerikgovde);
					//die($icerikgovde);
					//$icerikgovde	=str_replace("http://","*http://",$icerikgovde);
					$icerikgovde	=str_replace('width="**"','width="'.$iWidth.'"',$icerikgovde);
					$icerikgovde	=str_replace('height="**"','height="'.$iHeight.'"',$icerikgovde);

					$icerikgovde 	=icerikresimcevir($icerikgovde);
					//die($icerikgovde);
					//777777777777777777777777777777777777
				}
				else
				{
					if(strpos($resimson,'width="*"'))
					{
						$resimsonyeni	=str_replace('width="*" height="*"', 'width="**" height="**"', $resimson);
					}
					else
					{
						$resimsonyeni	=str_replace($resimson, $resimson.'width="**" height="**"', $resimson);
					}
					//die($resimsonyeni);
					$icerikgovde	=str_replace($resimson,$resimsonyeni,$icerikgovde);
					$iWidth="600"; $iHeight="400";
					$resimbulsayi 	=strpos($resimbul,'"',5);
					$resimson 		=substr($resimbul,5,$resimbulsayi-5);
					$icerikgovde	=str_replace(
						'src="'.$resimson,
						'src=*"'.$resimson,
						$icerikgovde);
					//die($icerikgovde);
					//$icerikgovde	=str_replace("http://","*http://",$icerikgovde);
					$icerikgovde	=str_replace('width="**"','width="'.$iWidth.'"',$icerikgovde);
					$icerikgovde	=str_replace('height="**"','height="'.$iHeight.'"',$icerikgovde);

					$icerikgovde 	=icerikresimcevir($icerikgovde);
				}
			}
			unset($resimson,$resimbulsayi,$resimbul,$resimbulek);
		}
		unset($resimsonsayi);
		//die('<img src=http://'.$siteDomain.'/m/r/index.php?resim='.$resimbul.'>');
	}
	unset($resimbul);
	return $icerikgovde;
}
function resimcevir($resim,$baslik)
{
	global $siteDomain,$protokol,$resimdizin,$ampdurum,$resimklasor;
	$rEn="600";$rBoy="400";
	$resimkonumsayi=stripos($resim,"?");
	if($resimkonumsayi)
	{
		$resimkonum=substr($resim,0,$resimkonumsayi);
		$resimboyut=substr($resim,$resimkonumsayi+1,strlen($resim)-$resimkonumsayi);
		$rEnAL=substr($resimboyut,0,stripos($resimboyut,"x"));
		if(S($rEnAL)!=0)$rEn=$rEnAL;
		$rBoyAL=substr($resimboyut,stripos($resimboyut,"x")+1,strlen($resimboyut)-stripos($resimboyut,"x"));
		if(S($rBoyAL)!=0)$rBoy=$rBoyAL;
		unset($rEnAL,$rBoyAL);
	}
	$resim = substr($resim,0,strpos($resim,"?"));
	unset($resimkonumsayi,$resimkonum,$resimboyut);
	if(strpos($resim, "://")===false)
	{
		if(strpos($resim,$resimklasor)!==false)
		{
			$resim=str_replace($resimklasor,"",$resim);
		}
		$resim=
		'<img 
			src="'.$resimdizin.$resim.'"
			srcset="
			'.$resimdizin.'?g=1080&resim='.$resim.' 1080w,
			'.$resimdizin.'?g=960&resim='.$resim.' 960w, 
			'.$resimdizin.'?g=840&resim='.$resim.' 840w,
			'.$resimdizin.'?g=720&resim='.$resim.' 720w, 
			'.$resimdizin.'?g=600&resim='.$resim.' 600w,
			'.$resimdizin.'?g=480&resim='.$resim.' 480w,
			'.$resimdizin.'?g=360&resim='.$resim.' 360w"
			width="'.$rEn.'" height="'.$rBoy.'" layout="responsive"	>';
	}
	else
	{
		$resim=
		'<img 
			src="'.$resim.'"
			width="'.$rEn.'" height="'.$rBoy.'" layout="responsive"	>';
	}

	if(!BosMu($baslik))
	{
		$resim = str_replace("<img", '<img alt="'.CT($baslik).'" ', $resim);
	}
	if($ampdurum==1)
	{
		$resim=str_replace("<img", "<amp-img", $resim);
		$resim=str_replace(">", "></amp-img>", $resim);
	}
	return $resim;
	unset($rEn,$rBoy,$resim,$baslik);
}
function MailGonder($Kime,$mailKonu,$mailIcerik)
{
    global $anadizin,$firmaeposta;
    require_once($anadizin.'/_y/s/f/mail/PHPMailerAutoload.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SetLanguage("tr", $anadizin."/_y/s/f/mail/language");

    $mail->SMTPAuth = true;
    //$mail->AuthType = 'LOGIN';
    $mail->IsHTML(true);
    $mail->XMailer = ' ';

    //$mail->SMTPDebug = 3;
    $mail->CharSet = 'UTF-8';
    //$mail->Headers['Content-type'] = 'text/html; charset=UTF-8';
    $mail->Subject = $mailKonu;
    $mail->MsgHTML($mailIcerik);
    if(!BosMu($firmaeposta))$mail->addReplyTo($firmaeposta);
    $Kimler=explode(",",$Kime);
    $yasakli=array("hotmail.com","outlook.com","msn.com","live.com","gmail.com");

    foreach ($Kimler as $key => $value)
    {
        $mail->AddAddress($value);
        /*if($key==1)
        {
            $mail->ClearReplyTos();
            $mail->addReplyTo($value, 'Müşteri');
        }*/
        if(in_array(explode("@",$value)[1], $yasakli))
        {
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.yandex.com';
            $mail->Port = 465;
            $mail->Username = 'info@makinaelemanlari.com';
            $mail->Password = 'Global2019*';
            $mail->SetFrom('info@makinaelemanlari.com', 'info Makina Elemanlari');
        }
        else
        {
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.yandex.com';
            $mail->Port = 465;
            $mail->Username = 'info@makinaelemanlari.com';
            $mail->Password = 'Global2019*';
            $mail->SetFrom('info@makinaelemanlari.com', 'info Makina Elemanlari');
        }

        if(!$mail->Send())
        {
            $GLOBALS['form_sonuc']=$value.'Form gönderilirken bir hata oluştu. Daha sonra tekrar deneyiz.';
            logyaz("MailGonder","$mailKonu ".$mail->ErrorInfo);
            //die($mail->ErrorInfo);
        } else {
            $GLOBALS['form_sonuc']=$value.'Form başarıyla gönderildi.|';
            logyaz("MailGonder","$mailKonu : Gönderim Başarılı");
        }
        $mail->ClearAddresses();
    }
}
/*
    function sifrele($text, $key)
    {
        if(BosMu($text))return;
        $td = mcrypt_module_open('cast-256', '', 'ecb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $text);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return base64_encode($encrypted_data);
    }
    function coz($m_encoded, $key)
    {
        if(BosMu($m_encoded))return;
        $decoded_64=base64_decode($m_encoded);
        $td = mcrypt_module_open('cast-256', '', 'ecb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $decrypted_data = mdecrypt_generic($td, $decoded_64);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return preg_replace('/[^A-Za-z0-9-._@\/]/', '', $decrypted_data);
    }
*/
function sifrele(string $data, string $key): string
{
    global $anahtarkod;
    if(BosMu($key))$key=$anahtarkod;
    $method = 'AES-256-CBC';
    //$ivSize = openssl_cipher_iv_length($method);
    //$iv = openssl_random_pseudo_bytes($ivSize);
    $key = hash('sha256', $key);
    //iv - encrypt method AES-256-CBC expects 16 bytes
    $iv = substr(hash('sha256', $key), 0, 16);

    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);

    // For storage/transmission, we simply concatenate the IV and cipher text
    $encrypted = base64_encode($iv . $encrypted);

    return $encrypted;
}
function coz(string $data, string $key): string
{
    global $anahtarkod;
    if(BosMu($key))$key=$anahtarkod;
    $method = 'AES-256-CBC';
    $data = base64_decode($data);
    $ivSize = openssl_cipher_iv_length($method);
    //$iv = substr($data, 0, $ivSize);
    $key = hash('sha256', $key);
    $iv = substr(hash('sha256', $key), 0, 16);
    $data = openssl_decrypt(substr($data,16), $method, $key, OPENSSL_RAW_DATA, $iv);

    return $data;
}
function BetweenStr($InputString, $StartStr, $EndStr=0, $StartLoc=0)
{
	if(BosMu($EndStr))
	{
		if (($StartLoc = strpos($InputString, $StartStr)) === false) { return; }
		$url = mb_strrchr ( $InputString , $StartStr ,false );
		return str_replace($StartStr,"",$url);
	}
	else
	{
		if (($StartLoc = strpos($InputString, $StartStr, $StartLoc)) === false) { return; }
		$StartLoc += strlen($StartStr);
		if (!$EndStr) { $EndStr = $StartStr; }
		if (!$EndLoc = strpos($InputString, $EndStr, $StartLoc)) { return; }
		return substr($InputString, $StartLoc, ($EndLoc-$StartLoc));
	}
}
function ipal(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function simdi(){
    return date("Y-m-d H:i:s");
}
?>
