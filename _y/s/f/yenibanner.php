<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");

if(S(q("dil"))!=0 && !BosMu(q("kategori")))
{
	veri(true);
	$bannerslogan="#";
	$banneryazi="#";
	$bannerlink="#";
	$bannerresim="#";
	$bannerad="Banner";
	$bannergenislik="0";
	$banneryukseklik="0";
	$bannerid="0";
	$sonbannerid=0;

	//$sonbannerid=teksatir("SELECT bannerid FROM banner WHERE bannerkategori='".q("kategori")."' and banneraktif='0' Order By bannerid ASC","bannerid");

	if(S($sonbannerid)==0)
	{
		$sonbannerid=teksatir("SELECT bannerid FROM banner Order By bannerid DESC","bannerid");
		$sonbannerid=$sonbannerid+1;
	}
	//$banner_ozellik=coksatir("SELECT bannerad,banneryukseklik,bannergenislik FROM banner WHERE bannerkategori='".q("kategori")."'");
	//$banneryukseklik=teksatir("SELECT banneryukseklik FROM banner Where bannerkategori='".q("kategori")."'","banneryukseklik");
	//$bannergenislik=teksatir("SELECT bannergenislik FROM banner Where bannerkategori='".q("kategori")."'","bannergenislik");
	$tablo="banner";
	$sutunlar="
		bannerid,
		dilid,
		bannerkategori,
		bannerad,
		bannergenislik,
		banneryukseklik,
		bannerlink,
		bannerresim,
		bannerslogan,
		banneryazi,
		banneraktif
	";
	$degerler=
		$sonbannerid 	."|*_".
		q("dil") 		."|*_".
		q("kategori") 	."|*_".
		q("bannerad") 	."|*_".
		$banneryukseklik."|*_".
		$bannergenislik ."|*_#|*_/tema/img/banner/sehir2.jpg|*_#|*_#|*_1
	";
	ekle($sutunlar,$degerler,$tablo,0);
}else{die("ben öldüm");}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
	</head>
	<body>
		<script>
			$bannerbos='<div class="row"> <div class="col-lg-3 col-md-4"><article class="margin-bottom-xxl"> <h4><?=$bannerad?> </h4> <p><?=$bannergenislik?>x<?=$banneryukseklik?></p> <p>&nbsp;</p> <p></p> <p>&nbsp;</p> <p></p></article> </div> <div class="col-lg-offset-1 col-md-8"><div class="card"> <div class="card-body"><div class="col-md-12"> <div class="form-group"><div class="hbox-column v-top col-md-1"> <a class="btn btn-floating-action ink-reaction" href="#offcanvas-search" id="bhazirekle" data-toggle="offcanvas" title="seç" data-id="[bannerid]"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-left" id="byeniekle" data-toggle="offcanvas" title="ekle" data-id="[bannerid]"><i class="fa fa-plus"></i></a></div> </div></div><div class="col-md-12"> <div class="form-group"><textarea name="bannerslogan[bannerid]" id="bannerslogan[bannerid]" class="form-control">#</textarea><label for="banneraslogan<?=$bannerid?>">Slayt Başlık</label> </div></div><div class="col-md-12"> <div class="form-group"><textarea name="banneryazi[bannerid]" id="banneryazi[bannerid]" class="form-control">#</textarea><label for="banneryazi[bannerid]">Slayt Yazı</label> </div></div><div class="col-md-12"> <div class="form-group"><textarea name="bannerlink[bannerid]" id="bannerlink[bannerid]" class="form-control">#</textarea><label for="bannerlink<?=$bannerid?>">Slayt Link</label> </div></div><div class="col-md-12"> <div class="form-group"><textarea name="bannerresim[bannerid]" id="bannerresim[bannerid]" class="form-control">/tema/img/banner/sehir2.jpg</textarea><label for="bannerresim<?=$bannerid?>">Slayt Resim</label> </div> <div class="form-group"><img src="/tema/img/banner/sehir2.jpg" id="imgbannerresim[bannerid]" style="max-width:100%"> </div></div><div class="col-md-12"> <div class="form-group"><div class="checkbox checkbox-styled"> <label><input name="banneraktif[bannerid]" id="banneraktif[bannerid]" type="checkbox" value="1"><span>Banner Aktif</span> </label></div> </div></div> </div></div><em class="text-caption">Anasayfa Slayt</em> </div></div>';
			$bannerbos=$bannerbos.replace(/\[bannerid]/g, "<?=$sonbannerid?>");
			window.parent.$("#bannerkutu").append($bannerbos);
		</script>
	</body>
</html>
