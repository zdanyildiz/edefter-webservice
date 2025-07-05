<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
$xlsx="";$excelveri="";$satir="";
if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
{
	$excelveri=$xlsx->rows();
	if(isset($excelveri))
	{
		$satirtoplam=count($excelveri);
		$saybasla=0;$saybitir=0;
		if($saybasla>=$satirtoplam)
		{
			echo '<h3>ürün özellikleri aktarımı Tamamlandı</h3>';
	 		//die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=8&dosya=/m/r/havuz/urun.xlsx";</script>');
	 		echo '<br>Bir sonraki adım da veriler işlenecek.<br>><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx">DEVAM EDİN</a>';
		}
		foreach ($excelveri as $satirsay => $satir)
		{ 	
		 	
		 	if($satirsay==0)echo "<h3>Aktarım Başlatılıyor ($satirtoplam) Satır</h3>";
		 	if(s(q("satir"))!=0)$saybasla=q("satir");
		 	if($satirsay>$saybasla)
		 	{
		 		//////////////////////////////////////////////
		 		$saybitir++;
		 		//$simdi=date("Y-m-d H:i:s");
		 		//$satisbaslangictarih=$simdi;
		 		//$satisbitistarih=date("Y-m-d",strtotime(date("Y-m-d") . " + 365 day"));
		 		//$aktif=1;
		 		//$resimklasor=2;
		 		//////////////////////////////////////////////
		 		foreach ($satir as $sutunsay => $sutun)
			 	{
			 		if(s(q("sutun"))!=0)$sutunsay=q("sutun");
			 		if($sutunsay==0)//sayfaid
			 		{
			 			$sayfaid=0;$sayfaid=S($sutun);
			 		}
			 		elseif($sutunsay==1)//kategori
			 		{
			 			$kategoriid=0;$kategoriad=$sutun;
			 		}
			 		elseif($sutunsay==2)//marka
			 		{
			 			$markaid=0;$markaad=$sutun;
			 		}
			 		elseif($sutunsay==3)//stokkodu
			 		{
			 			$stokkodu="";$stokkodu=$sutun;
			 			if(BosMu($stokkodu))$stokkodu=SifreUret(20,2);
			 		}
			 		elseif($sutunsay==4)//model
			 		{
			 			$model="";$model=$sutun;
			 		}
			 		elseif($sutunsay==5)//başlık
			 		{
			 			$baslik="";$baslik=$sutun;
			 			if(BosMu($baslik))break 2;
			 		}
			 		elseif($sutunsay==6)//altbaşlık
			 		{
			 			$altbaslik="";$altbaslik=$sutun;
			 		}
			 		elseif($sutunsay==7)//açıklama
			 		{
			 			$aciklama="";$aciklama=$sutun;
			 		}
			 		elseif($sutunsay==8)//alışfiyat
			 		{
			 			$alisfiyat="";$alisfiyat=$sutun;
			 			$alisfiyat=str_replace(",", ".", $alisfiyat);
			 		}
			 		elseif($sutunsay==9)//satışfiyat
			 		{
			 			$fiyat="";$fiyat=$sutun;
			 			$fiyat=str_replace(",", ".", $fiyat);
			 		}
			 		elseif($sutunsay==10)//indirimsizfiyat
			 		{
			 			$indirimsizfiyat="";$indirimsizfiyat=$sutun;
			 			$indirimsizfiyat=str_replace(",", ".", $indirimsizfiyat);
			 		}
			 		elseif($sutunsay==11)//bayifiyat
			 		{
			 			$bayifiyat="";$bayifiyat=$sutun;
			 			$bayifiyat=str_replace(",", ".", $bayifiyat);
			 		}
			 		elseif($sutunsay==12)//stok sayısı
			 		{
			 			$stok="";$stok=$sutun;
			 			if(!BosMu($stok))$stok=S(trim($stok));else $stok=0;
			 		}
			 		elseif($sutunsay==13)//parabirim
			 		{
			 			$parabirim="";$parabirim=$sutun;
			 			$urunparabirimid=0;
			 		}
			 		elseif($sutunsay==14) //renk
			 		{
			 			$renkad="";$renkad=$sutun;
			 			if(!BosMu($renkad)){$renkad=trim($renkad);if($renkad=="-"||$renkad=="_")$renkad="";}
			 			$urunrenkid=0;
			 			$urunrenkgrupid=0;
			 		}
			 		elseif($sutunsay==15) //beden
			 		{
			 			$bedenad="";$bedenad=$sutun;
                        if(!BosMu($bedenad)){$bedenad=trim($bedenad);if($bedenad=="-"||$bedenad=="_")$bedenad="";}
			 			$urunbedenid=0;
			 			$urunbedengrupid=0;
			 		}
                    elseif($sutunsay==16) //malzeme
                    {
                        $malzemead="";$malzemead=$sutun;
                        if(!BosMu($malzemead)){$malzemead=trim($malzemead);if($malzemead=="-"||$malzemead=="-")$malzemead="";}
                        $urunmalzemeid=0;
                        $urunmalzemegrupid=0;
                    }
			 		elseif($sutunsay==17) //resim klasör
			 		{
			 			$hariciresimklasor="";
			 			$hariciresimklasor=$sutun;
			 		}
			 		elseif($sutunsay==18)//resimler
			 		{
			 			$resim="";
			 			$resim=$sutun;
			 			if(!BosMu($resim))$resim=str_replace("'", "", $resim);
			 		}
			 		elseif($sutunsay==19)//dosya
			 		{
			 			$dosya="";
			 			$dosya=$sutun;
			 		}
                    elseif($sutunsay==20)//video
                    {
                        $video="";
                        $video=$sutun;
                    }
			 		elseif($sutunsay==21)//link
                    {
                        $link="";
                        $link=$sutun;
                    }
			 		elseif($sutunsay==22)//aktif
                    {
                        $aktif = 1;
                        $aktif = $sutun;
                    }
                    elseif($sutunsay==23)//minimum satış
                    {
                        if(!BosMu($sutun))
                        {
                            if(!BosMu($altbaslik))$altbaslik = $altbaslik." | Minimum Satış Miktarı".$sutun;
                        }

				 		$simdi=date("Y-m-d H:i:s");
				 		$satisbaslangictarih=$simdi;
				 		$satisbitistarih=date("Y-m-d",strtotime(date("Y-m-d") . " + 365 day"));
				 		$aktarimonay=0;

				 		$sutunlar="
				 			urunid,
				 			kategoriid,
				 			kategoriad,
				 			marka,
				 			markaad,
				 			stokkodu,
				 			model,
				 			baslik,
				 			altbaslik,
				 			aciklama,
				 			alisfiyat,
				 			fiyat,
				 			indirimsizfiyat,
				 			bayifiyat,
				 			stok,
				 			parabirimad,
				 			parabirimid,
				 			renkad,
				 			renkgrupid,
				 			renkid,
				 			bedenad,
				 			bedengrupid,
				 			bedenid,
				 			malzemead,
				 			malzemegrupid,
				 			malzemeid,
				 			resimklasor,
				 			resim,
				 			dosya,
				 			video,
				 			link,
				 			satisbaslangictarih,
				 			satisbitistarih,
				 			aktif,
				 			aktarimonay
				 		";
				 		$degerler=
					 		$sayfaid."|*_".
					 		$kategoriid."|*_".
					 		$kategoriad."|*_".
					 		$markaid."|*_".
					 		$markaad."|*_".
					 		$stokkodu."|*_".
					 		$model."|*_".
					 		$baslik."|*_".
					 		$altbaslik."|*_".
					 		$aciklama."|*_".
					 		$alisfiyat."|*_".
					 		$fiyat."|*_".
					 		$indirimsizfiyat."|*_".
					 		$bayifiyat."|*_".
					 		$stok."|*_".
					 		$parabirim."|*_".
					 		$urunparabirimid."|*_".
					 		$renkad."|*_".
					 		$urunrenkgrupid."|*_".
					 		$urunrenkid."|*_".
					 		$bedenad."|*_".
					 		$urunbedengrupid."|*_".
					 		$urunbedenid."|*_".
                            $malzemead."|*_".
                            S($urunmalzemegrupid)."|*_".
                            S($urunmalzemeid)."|*_".
					 		$hariciresimklasor."|*_".
					 		$resim."|*_".
					 		$dosya."|*_".
					 		$video."|*_".
                            $link."|*_".
					 		$satisbaslangictarih."|*_".
					 		$satisbitistarih."|*_".
					 		S($aktif)."|*_".
					 		$aktarimonay
				 		;//die($sutunlar."<br><br>".$degerler);
				 		//aynı stokkodları eklenmiyor.
				 		if(Dogrula("urunaktar","stokkodu='".$stokkodu."'"))
				 		{
				 			guncelle($sutunlar,$degerler,"urunaktar","stokkodu='".$stokkodu."'",63);
				 			$durum="Güncellendi";
				 		}
				 		else
				 		{
				 			ekle($sutunlar,$degerler,"urunaktar",63);
				 			$durum="Eklendi";
				 		}
				 		
				 		echo "$satirsay ) $durum - $sayfaid - $kategoriad - $markaad - $stokkodu - $model - $baslik".'<br>';
				 		$sayfaid=0;$kategoriid=0;$markaid=0;$stokkodu="";$model="";$baslik="";
			 		}
			 	}

			 	if($saybitir==100)
			 	{
			 		die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
			 	}
		 	}
		 	if($satirsay==($satirtoplam-1))
		 	{
		 		echo '<h3>Toplu ürün verileri aktarıldı</h3>';
		 		//die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=8";</script>');
		 		echo '<br>Bir sonraki adım da veriler işlenecek.<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=3">DEVAM EDİN</a>';
		 	}
		}
        echo '<h3>Toplu ürün verileri aktarıldı</h3>';
        //die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=8";</script>');
        echo '<br>Bir sonraki adım da veriler işlenecek.<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=3">DEVAM EDİN</a>';
	}
}
else{echo SimpleXLSX::parseError();}
?>