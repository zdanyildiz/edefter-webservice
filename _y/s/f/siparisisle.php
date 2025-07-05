<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(!BosMu(f("gsiparisid")) && S(f("siparisdurumid"))!=-1)
{
	if(!$data)veri(true);
	?>
	<!DOCTYPE html>
	<html lang="tr">
		<head>
			<meta charset="utf-8">
			<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
		</head>
		<body>
			<script>
				<?php
                $tablo="uyesiparis";
                $sabit="siparisbenzersizid='".f("gsiparisid")."'";
                if(S(f("siparisdurumid"))==10 || S(f("siparisdurumid"))==11)
				{
					$siparisiptal_s="
						Select 
							urunstokkodu,urunadet
						FROM 
							uyesepet 
						WHERE 
							siparisbenzersiz='".f("gsiparisid")."' and sepetsil='1'
					";
					if($data->query($siparisiptal_s))
					{
						$siparisiptal_v=$data->query($siparisiptal_s);unset($siparisiptal_s);
						if($siparisiptal_v->num_rows>0)
						{
							while ($siparisiptal_t=$siparisiptal_v->fetch_assoc())
							{
								$urunstokkodu=$siparisiptal_t["urunstokkodu"];
								$urunadet=$siparisiptal_t["urunadet"];
								$stokadet=teksatir("select urunstok from urunozellikleri where urunstokkodu='".$urunstokkodu."'","urunstok");
								$stokadet=$stokadet+$urunadet;
								guncelle("urunstok",$stokadet,"urunozellikleri","urunstokkodu='".$urunstokkodu."'",50);
							}unset($siparisiptal_t);echo 'alert("Stoklar güncellenmiştir");';
						}unset($siparisiptal_v);
					}else{die("alert('".$data->error."');</script>");}
				}
                elseif(
                        ( S(f("siparisdurumid"))==0 || S(f("siparisdurumid"))==2 )
                        &&
                        f("odemeyontemi")=="bh"
                )
                {
                    guncelle("siparisodemedurum",1,$tablo,$sabit,50);
                }
                if(S(f("siparisdurumid"))==999)
                {
                    //$siparisteslimateposta=$tedarikcieposta;
                }
                else
                {
                    $kargoCode = teksatir("select kargoCode from kargo where kargoid=" . f("kargoid"), "kargoCode");
                    $simdi = date("Y-m-d H:i:s");
                    $sutunlar = "siparisdurum,kargoid,sipariskargotarih,sipariskargoserino,sipariskargotakip,siparisnotalici,siparisnotyonetici,kargoCode";
                    $degerler = f("siparisdurumid") . "|*_" . f("kargoid") . "|*_" . $simdi . "|*_" . f("sipariskargoserino") . "|*_" . f("sipariskargotakip") . "|*_" . f("siparisnotalici") . "|*_" . f("siparisnotyonetici") . "|*_" . $kargoCode;
                    //echo "var deg='".f("gsiparisid").",".f("kargoid").",".$simdi.",".f("sipariskargoserino").",".f("siparisteslimatid").",".f("siparisnotalici").",".f("siparisnotyonetici")."';";
                    guncelle($sutunlar, $degerler, $tablo, $sabit, 50);

                    $uyehesabimlink = coksatir("SELECT link,sayfatip FROM seo INNER JOIN sayfa ON sayfa.benzersizid=seo.benzersizid WHERE (sayfasil=0 and sayfaaktif='1') and sayfatip='17'", "");
                    $hesaplink = $uyehesabimlink["link"];

                    $firmabilgileri = coksatir(
                        "SELECT 
                                ayarfirmaad,ayarfirmakisaad,ayarfirmaeposta,ayarfirmagsm,ayarfirmatelefon,ayarfirmaadres,
                                yersehir.CityName AS sehirad,
                                yerilce.CountyName AS ilcead,
                                yersemt.AreaName AS semtad,
                                yermahalle.NeighborhoodName AS mahallead
                            FROM ayarfirma
                            INNER JOIN yersehir ON yersehir.CityID=ayarfirma.ayarfirmasehir
                            INNER JOIN yerilce ON yerilce.CountyID=ayarfirma.ayarfirmailce
                            INNER JOIN yersemt ON yersemt.AreaID=ayarfirma.ayarfirmasemt
                            INNER JOIN yermahalle ON yermahalle.NeighborhoodID=ayarfirma.ayarfirmamahalle
                            WHERE ayarfirmasil='0'", "");
                    $firmaad = $firmabilgileri["ayarfirmaad"];
                    $ayarfirmakisaad = $firmabilgileri["ayarfirmakisaad"];
                    $ayarfirmaeposta = $firmabilgileri["ayarfirmaeposta"];
                    $ayarfirmagsm = $firmabilgileri["ayarfirmagsm"];
                    $ayarfirmatelefon = $firmabilgileri["ayarfirmatelefon"];
                    $ayarfirmaadres = $firmabilgileri["ayarfirmaadres"];
                    $sehirad = $firmabilgileri["sehirad"];
                    $ilcead = $firmabilgileri["ilcead"];
                    $semtad = $firmabilgileri["semtad"];
                    $mahallead = $firmabilgileri["mahallead"];

                    $siparisbilgileri = coksatir("SELECT 
                            siparisteslimateposta,
                            siparisteslimatgsm,
                            siparisteslimatad,
                            siparisteslimatsoyad,
                            siparisurunidler,
                            siparisurunadlar,
                            siparisurunadetler,
                            siparisurunbedenler,
                            siparisurunrenkler,
                            siparisurunmalzemeler,
                            kargoid,sipariskargotakip 
                        FROM uyesiparis WHERE siparisbenzersizid='" . f("gsiparisid") . "'", "");

                    $siparisteslimateposta = $siparisbilgileri["siparisteslimateposta"];
                    $siparisteslimatgsm = $siparisbilgileri["siparisteslimatgsm"];
                    $siparisteslimatad = $siparisbilgileri["siparisteslimatad"];
                    $siparisteslimatsoyad = $siparisbilgileri["siparisteslimatsoyad"];

                    $sipariskargoid = $siparisbilgileri["kargoid"];
                    $sipariskargotakip = $siparisbilgileri["sipariskargotakip"];
                    $siparisurunidler = $siparisbilgileri["siparisurunidler"];
                    $siparisurunadlar = $siparisbilgileri["siparisurunadlar"];
                    $siparisurunadetler = $siparisbilgileri["siparisurunadetler"];
                    $siparisurunbedenler = $siparisbilgileri["siparisurunbedenler"];
                    $siparisurunrenkler = $siparisbilgileri["siparisurunrenkler"];
                    $siparisurunmalzemeler = $siparisbilgileri["siparisurunmalzemeler"];

                    $siparisurunidler_ayikla 		= explode(",", $siparisurunidler);
                    $siparisurunadlar_ayikla 		= explode("||", $siparisurunadlar);
                    $siparisurunmalzeme_ayikla 	    = explode("||", $siparisurunmalzemeler);
                    $siparisurunadetler_ayikla 		= explode("||", $siparisurunadetler);
                    $siparisurunbedenler_ayikla 	= explode("||", $siparisurunbedenler);
                    $siparisurunrenkler_ayikla 		= explode("||", $siparisurunrenkler);
                    $siparisuruntoplam 				= count($siparisurunidler_ayikla);
                    $mailurun="";
                    for($i=0; $i<$siparisuruntoplam;$i++)
                    {
                        $mailurun=$mailurun.
                            $siparisurunadlar_ayikla[$i]." | ".
                            $siparisurunmalzeme_ayikla[$i].", ".
                            $siparisurunrenkler_ayikla[$i]." renk, ".
                            $siparisurunbedenler_ayikla[$i]." beden ".
                            $siparisurunadetler_ayikla[$i]." adet "."  <br><br>";
                    }

                    if (S(f("siparisdurumid")) == 2)
                    {
                        $mailsiparisdurum = "Siparişiniz hazırlanıyor";
                    }
                    elseif (S(f("siparisdurumid")) == 3)
                    {
                        $kargoad = coksatir("SELECT kargoad,kargotakiplink FROM kargo WHERE kargoid='" . $sipariskargoid . "'", "");
                        $sipariskargoad = $kargoad["kargoad"];
                        $kargotakiplink = $kargoad["kargotakiplink"];
                        $mailsiparisdurum = '
                            Siparişiniz kargoya teslim edildi. 
                            <a href="' . $kargotakiplink . $sipariskargotakip . '" target="_blank">' . $sipariskargoad . '</a>
                        ';
                    }
                    elseif (S(f("siparisdurumid")) == 4)
                    {
                        $mailsiparisdurum = 'Siparişiniz alıcıya teslim edimiştir.';
                    }
                    elseif (S(f("siparisdurumid")) == 5)
                    {
                        $mailsiparisdurum = 'İade talebiniz alınmıştır';
                    }
                    elseif (S(f("siparisdurumid")) == 6)
                    {
                        $mailsiparisdurum = 'Siparişiniz Tamamlanamamıştır.';
                    }
                    elseif (S(f("siparisdurumid")) == 7)
                    {
                        $mailsiparisdurum = 'Değişim talebiniz alınmıştır.';
                    }
                    elseif (S(f("siparisdurumid")) == 8)
                    {
                        $mailsiparisdurum = 'İptal talebiniz alınmıştır.';
                    }
                    elseif (S(f("siparisdurumid")) == 9)
                    {
                        $mailsiparisdurum = 'Lütfen Tedarik Başlatınız.';
                        $siparisteslimateposta = $siparisbilgileri["siparisteslimateposta"];
                        $siparisteslimatgsm = $siparisbilgileri["siparisteslimatgsm"];
                        $siparisteslimatad = $siparisbilgileri["siparisteslimatad"];
                        $siparisteslimatsoyad = $siparisbilgileri["siparisteslimatsoyad"];
                    }
                    elseif (S(f("siparisdurumid")) == 0)
                    {
                        $mailsiparisdurum = 'Siparişiniz kargoya hazırlanıyor.';
                    }
                    elseif (S(f("siparisdurumid")) == 10)
                    {
                        $mailsiparisdurum = 'Siparişiniz iade edildi.';
                    }
                    elseif (S(f("siparisdurumid")) == 11)
                    {
                        $mailsiparisdurum = 'Siparişiniz iptal edildi';
                    }

                    if(S(q("siparismailgonder"))==1)
                    {
                        $http = "https";
                        require_once($anadizin . "/sistem/fonksiyon/mail-panel-siparis-guncelle-taslak.php");
                        $siparismail = str_replace("[siparisid]", f("gsiparisid"), $siparismail);
                        $siparismail = str_replace("[uyeadsoyad]", $siparisteslimatad . ' ' . $siparisteslimatsoyad, $siparismail);
                        $siparismail = str_replace("[urun]", $mailurun, $siparismail);

                        $siparismail = str_replace("[firmaad]", $ayarfirmakisaad, $siparismail);
                        $siparismail = str_replace("[firmaadres]", $ayarfirmaadres, $siparismail);
                        $siparismail = str_replace("[firmailce]", $ilcead, $siparismail);
                        $siparismail = str_replace("[firmasehir]", $sehirad, $siparismail);
                        $siparismail = str_replace("[hesabimlink]", $hesaplink, $siparismail);

                        $siparismail = str_replace("[kargo]", $mailsiparisdurum, $siparismail);
                        $siparismail = str_replace("[sonuc]", $mailsiparisdurum, $siparismail);
                        $smsicerik = 'Sayın ' . $siparisteslimatad . ' ' . $siparisteslimatsoyad . ' ' . f("gsiparisid") . ' No\'lu siparişinizin durumu: ' . $mailsiparisdurum . ' Ayrıntılı bilgi için https://' . $siteDomain;
                        mailgonder($siparisteslimateposta, $siteDomain . ' Siparişiniz Hakkında', $siparismail);
                        //SMSGonder("",$smsicerik,$siparisteslimatgsm);
                    }
                }

				?>
				$("#btn-popup-sil-kapat",parent.document).click();
				parent.document.location.reload();
			</script>
		</body>
	</html>
<?php } else {echo '<script>alert("bos");</script>';}
if(isset($_SESSION['siparisSql']))
{
    $_SESSION['siparisSql']="";
    unset($_SESSION['siparisSql']);
}
?>
