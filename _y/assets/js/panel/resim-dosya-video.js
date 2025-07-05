$silid=0;$sildiv="";
$dosyakutu=0;$dosyaustekle=0;$yenidosyaadi="";$dkutular=new Array();
$dkutular=["dosyakutu1","dosyakutu2","dosyakutu3","dosyakutu4","dosyakutu5","dosyakutu6","dosyakutu7","dosyakutu8","dosyakutu9","dosyakutu10","dosyakutu11","dosyakutu12","dosyakutu13","dosyakutu14","dosyakutu15","dosyakutu16","dosyakutu17","dosyakutu18","dosyakutu19","dosyakutu20"];
//excel yükle
$(document).on("click","#urunlistesiekle",function(){$dosyaustekle=0;$dosyakutu="dosyakutu1";});
//excel yükle
function cokludosya($cid,$cadi,$uzanti)
{
	jQuery.each( $dkutular, function( i, val )
	{
		if($dosyaustekle==0)
		{
			if($( "#" + val ).length)
			{
				$dataid=$( "#" + val +" #dsillink" ).data("id");
				if(parseInt($dataid)==0)
				{
					if($("#dosyaid").val()==""){$("#dosyaid").val($cid);}else{$("#dosyaid").val($("#dosyaid").val()+","+$cid);}
					$("#"+ val +" #dsillink").attr("data-id",$cid);
					d=$.now();  $resim="/_y/assets/img/"+$uzanti+".png?"+d;
					$("#"+ val +" #dyer").attr("src",$resim);
					if($yenidosyaadi=="")$yenidosyaadi=$("#dosyaad").val();
					$("#"+ val +" #dad").text($yenidosyaadi);
					$dkutular.splice($dkutular.indexOf(val),1);$( "#dyenikutu" ).click();
					$("#"+ val +" #dsillink").attr("data-dosyakutu",val+i);
					$("#"+ val +" #dhazirekle").attr("data-dosyakutu",val+i);
					$("#"+ val +" #dyeniekle").attr("data-dosyakutu",val+i);
					$("#" + val ).attr("id", val+i);return false;
				}
			}
		}
		else
		{
			if($yenidosyaadi=="")$yenidosyaadi=$("#dosyaad").val();$dosya="/tema/img/s/"+$uzanti+".png";
			CKEDITOR.instances.ckeditor.insertHtml('<a href="/m/r/'+$cadi+'" title="'+$dosyaad+'" target="_blank"><img src="'+$dosya+'" alt="'+$dosyaad+'" width="30" height="30"> '+$yenidosyaadi+' indirmek için tıklayınız</a>');
		}
	});
}
$(document).on("click","#dyenikutu",function()
{
	$kutudurum=0;$dkutular=new Array();
	$dkutular=["dosyakutu1","dosyakutu2","dosyakutu3","dosyakutu4","dosyakutu5","dosyakutu6","dosyakutu7","dosyakutu8","dosyakutu9","dosyakutu10","dosyakutu11","dosyakutu12","dosyakutu13","dosyakutu14","dosyakutu15","dosyakutu16","dosyakutu17","dosyakutu18","dosyakutu19","dosyakutu20",];
	jQuery.each( $dkutular, function( i, val ){if($( "#" + val ).length){$kutudurum=1;return false;}});
	if($kutudurum==1)
	{
		var $div=$('div[id^="dosyakutu"]:last');var num=parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;var $klon=$div.clone().prop('id', 'dosyakutu'+num );

		$div.addClass( "form-group floating-label" );
		$div.after( $klon.html('<div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="dkon"><img id="dyer" class="img-circle size-2" src="/_y/assets/img/file.png" ></div><h1 class="text-light no-margin" id="dad">Dosya Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="dsillink" data-dosyakutu="dosyakutu'+num+'" data-id="0" data-toggle="modal" data-target="#dsimpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#sagdosya" id="dhazirekle" data-dosyakutu="dosyakutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-file" data-toggle="offcanvas" id="dyeniekle" data-dosyakutu="dosyakutu'+num+'" title="ekle"><i class="fa fa-plus"></i></a></div></div>') );
	}
	else
	{
		var $div = $('#dosyagovde');var num = 1;
		$div.append('<div id="dosyakutu'+num+'"><div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="dkon"><img id="dyer" class="img-circle size-2" src="/_y/assets/img/file.png" ></div><h1 class="text-light no-margin" id="dad">Dosya Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="dsillink" data-dosyakutu="dosyakutu'+num+'" data-id="0" data-toggle="modal" data-target="#dsimpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#sagdosya" id="dhazirekle" data-dosyakutu="dosyakutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-file" data-toggle="offcanvas" id="dyeniekle" data-dosyakutu="dosyakutu'+num+'" title="ekle"><i class="fa fa-plus"></i></a></div></div></div>');
	}
});
$(document).on("click","#dyeniekle",function(){$dosyaustekle=0;$dosyakutu=$(this).data("dosyakutu");});
$(document).on("click","#dhazirekle",function(){$dosyaustekle=0;$dosyakutu=$(this).data("dosyakutu");});
$(document).on("click","#dsyeniekle",function(){$dosyaustekle=1;});
$(document).on("click","#dshazirekle",function(){$dosyaustekle=1;});
$(document).on("click","a.dosyasec",function()
{
	$dosyaid=$( this ).data( "id" );$dosyalink=$( this ).data( "link" );$dosyaad=$( this ).data( "ad" );$dosyauzanti=$( this ).data( "uzanti" );
	if($dosyaustekle==0)
	{
		if($("#dosyaid").val()=="")
		{
			$("#dosyaid").val($dosyaid);$("#"+ $dosyakutu +" #dad").text($dosyaad);
			$("#"+ $dosyakutu +" #dsillink").attr("data-id",$dosyaid);d=$.now();
			$dosya="/_y/assets/img/"+$dosyauzanti+".png?"+d;
			$("#"+ $dosyakutu +" #dyer").attr("src",$dosya);$("#sagcanvas").click();
		}
		else
		{
			$oncekiid=$("#"+ $dosyakutu +" #dsillink").data("id");
			if($oncekiid!=0)
			{
				$sildata=$("#dosyaid").val();$silarr=new Array();$silarr.push($sildata);$arr=$sildata.split(',');$silekleid=new Array();
				$silid=$oncekiid;
				if($silid!=0)
				{
					if($silarr.length>0)
					{
						for(var i=0; i<$silarr.length; i++)
						{
							if($silarr[i]!=$silid)
							{
								if(jQuery.inArray( $silarr[i], $silekleid )<0){$silekleid.push($silarr[i]);}
							}
						}
						$sildegerler=$silekleid.toString();
						$ilkharf=$sildegerler.substring(0, 1);
						if($ilkharf==",")$silekleid=$sildegerler.substring(1, $sildegerler.length-1)
						$("#dosyaid").val($silekleid);
					}
				}
			}
			$data = $("#dosyaid").val();$arr = $data.split(',');$ekledurum=1;
			if($arr.length>0){for(var i=0; i< $arr.length; i++){if($arr[i]==$dosyaid){$ekledurum=0;}}}
			if($ekledurum==1)
			{
				$("#dosyaid").val($("#dosyaid").val()+","+$dosyaid);
				$("#"+ $dosyakutu +" #dad").text($dosyaad);
				$("#"+ $dosyakutu +" #dsillink").attr("data-id",$dosyaid);
				d=$.now();$dosya="/_y/assets/img/"+$dosyauzanti+".png?"+d;
				$("#"+ $dosyakutu +" #dyer").attr("src",$dosya);$("#sagcanvas").click();
			}
			else{alert("Bu Dosya Zaten Ekli '"+$dosyaad+"' [ "+$dosyaid+" ] ");}
		}
	}
	else
	{
		CKEDITOR.instances.ckeditor.insertHtml('<a href="/m/r/'+$dosyalink+'" title="'+$dosyaad+'" target="_blank"><img src="/tema/img/s/'+$dosyauzanti+'.png" alt="'+$dosyaad+'" width="30" height="30">'+$dosyaad+' indirmek için tıklayınız</a>');
		$("#sagcanvas").click();
	}
});
$(document).on("click",'a#dsillink',function (){$silid=$(this).data("id");$sildiv=$(this).data("dosyakutu");});
$(document).on("click",'#dsilbutton',function ()
{
	$data=$("#dosyaid").val();$arr=new Array();$arr.push($data);$arr=$data.split(',');$ekleid = new Array();
	if($silid!=0)
	{
		if($arr.length>0)
		{
			for(var i=0; i< $arr.length; i++){if($arr[i]!=$silid){if(jQuery.inArray( $arr[i], $ekleid )<0){$ekleid.push($arr[i]);}}}
			$("#dosyaid").val($ekleid);
		}
	}
	$("#"+$sildiv).remove();$("#btn-popup-dsil-kapat").click();
});
$videokutu=0;$videoustekle=0;$yenivideoadi="";$vkutular=new Array();
$vkutular=["videokutu1","videokutu2","videokutu3","videokutu4","videokutu5","videokutu6","videokutu7","videokutu8","videokutu9","videokutu10","videokutu11","videokutu12","videokutu13","videokutu14","videokutu15","videokutu16","videokutu17","videokutu18","videokutu19","videokutu20"];
function cokluvideo($cid,$cadi,$uzanti)
{
	jQuery.each( $vkutular, function( i, val )
	{
		if($videoustekle==0)
		{
			if($( "#" + val ).length)
			{
				$dataid=$( "#" + val +" #vsillink" ).data("id");
				if(parseInt($dataid)==0)
				{
					if($("#videoid").val()==""){$("#videoid").val($cid);}else{$("#videoid").val($("#videoid").val()+","+$cid);}
					$("#"+ val +" #vsillink").attr("data-id",$cid);
					d=$.now();  $resim="/_y/assets/img/"+$uzanti+".png?"+d;
					$("#"+ val +" #vyer").attr("src",$resim);
					if($yenivideoadi=="")$yenivideoadi=$("#videoad").val();
					$("#"+ val +" #vad").text($yenivideoadi);
					$vkutular.splice($vkutular.indexOf(val),1);$( "#vyenikutu" ).click();
					$("#"+ val +" #vsillink").attr("data-videokutu",val+i);
					$("#"+ val +" #vhazirekle").attr("data-videokutu",val+i);
					$("#"+ val +" #vyeniekle").attr("data-videokutu",val+i);
					$( "#" + val ).attr("id", val+i);return false;
				}
			}
		}
		else
		{
			if($yenivideoadi=="")$yenivideoadi=$("#videoad").val();$video="/tema/img/s/"+$uzanti+".png";
			CKEDITOR.instances.ckeditor.insertHtml('<a href="/m/r/'+$cadi+'" title="'+$videoad+'" target="_blank"><img src="'+$video+'" alt="'+$videoad+'" width="30" height="30"> '+$yenivideoadi+' indirmek için tıklayınız</a>');
		}
	});
}
$(document).on("click","#vyenikutu",function()
{
	$kutudurum=0;$vkutular=new Array();
	$vkutular=["videokutu1","videokutu2","videokutu3","videokutu4","videokutu5","videokutu6","videokutu7","videokutu8","videokutu9","videokutu10","videokutu11","videokutu12","videokutu13","videokutu14","videokutu15","videokutu16","videokutu17","videokutu18","videokutu19","videokutu20",];
	jQuery.each( $vkutular, function( i, val ){if($( "#" + val ).length){$kutudurum=1;return false;}});
	if($kutudurum==1)
	{
		var $div=$('div[id^="videokutu"]:last');var num=parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;var $klon=$div.clone().prop('id', 'videokutu'+num );

		$div.addClass( "form-group floating-label" );
		$div.after( $klon.html('<div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="vkon"><img id="vyer" class="img-circle size-2" src="/_y/assets/img/video.png" ></div><h1 class="text-light no-margin" id="vad">video Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="vsillink" data-videokutu="videokutu'+num+'" data-id="0" data-toggle="modal" data-target="#vsimpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#sagvideo" id="vhazirekle" data-videokutu="videokutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-video" data-toggle="offcanvas" id="vyeniekle" data-videokutu="videokutu'+num+'" title="ekle"><i class="fa fa-plus"></i></a></div></div>') );
	}
	else
	{
		var $div = $('#videogovde');var num = 1;
		$div.append('<div id="videokutu'+num+'"><div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="vkon"><img id="vyer" class="img-circle size-2" src="/_y/assets/img/video.png" ></div><h1 class="text-light no-margin" id="vad">video Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="vsillink" data-videokutu="videokutu'+num+'" data-id="0" data-toggle="modal" data-target="#vsimpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#sagvideo" id="vhazirekle" data-videokutu="videokutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-video" data-toggle="offcanvas" id="vyeniekle" data-videokutu="videokutu'+num+'" title="ekle"><i class="fa fa-plus"></i></a></div></div></div>');
	}
});
$(document).on("click","#vyeniekle",function(){$videoustekle=0;$videokutu=$(this).data("videokutu");});
$(document).on("click","#vhazirekle",function(){$videoustekle=0;$videokutu=$(this).data("videokutu");});
$(document).on("click","#vsyeniekle",function(){$videoustekle=1;});
$(document).on("click","#vshazirekle",function(){$videoustekle=1;});
$(document).on("click","a.videosec",function()
{
	$videoid=$(this).data("id");$videolink=$(this).data("link");$videoad=$(this).data("ad");$videouzanti=$(this).data("uzanti");
	if($videoustekle==0)
	{
		if($("#videoid").val()=="")
		{
			$("#videoid").val($videoid);$("#"+ $videokutu +" #vad").text($videoad);
			$("#"+ $videokutu +" #vsillink").attr("data-id",$videoid);d=$.now();
			$video="/_y/assets/img/"+ $videouzanti +".png?"+d;
			$("#"+ $videokutu +" #vyer").attr("src",$video);$("#sagcanvas").click();
		}
		else
		{
			$oncekiid=$("#"+ $videokutu +" #vsillink").data("id");
			if($oncekiid!=0)
			{
				$sildata=$("#videoid").val();$silarr=new Array();$silarr.push($sildata);$arr=$sildata.split(',');$silekleid=new Array();
				$silid=$oncekiid;
				if($silid!=0)
				{
					if($silarr.length>0)
					{
						for(var i=0; i<$silarr.length; i++)
						{
							if($silarr[i]!=$silid)
							{
								if(jQuery.inArray( $silarr[i], $silekleid )<0){$silekleid.push($silarr[i]);}
							}
						}
						$sildegerler=$silekleid.toString();
						$ilkharf=$sildegerler.substring(0, 1);
						if($ilkharf==",")$silekleid=$sildegerler.substring(1, $sildegerler.length-1)
						$("#videoid").val($silekleid);
					}
				}
			}
			$data = $("#videoid").val();$arr = $data.split(',');$ekledurum=1;
			if($arr.length>0){for(var i=0; i< $arr.length; i++){if($arr[i]==$videoid){$ekledurum=0;}}}
			if($ekledurum==1)
			{
				$("#videoid").val($("#videoid").val()+","+$videoid);
				$("#"+ $videokutu +" #vad").text($videoad);
				$("#"+ $videokutu +" #vsillink").attr("data-id",$videoid);
				d=$.now();$video="/_y/assets/img/"+$videouzanti+".png?"+d;
				$("#"+ $videokutu +" #vyer").attr("src",$video);$("#sagcanvas").click();
			}
			else{alert("Bu video Zaten Ekli '"+$videoad+"' [ "+$videoid+" ] ");}
		}
	}
	else
	{
		CKEDITOR.instances.ckeditor.insertHtml('<a href="/m/r/'+$videolink+'" title="'+$videoad+'" target="_blank"><img src="/tema/img/s/'+$videouzanti+'.png" alt="'+$videoad+'" width="30" height="30">'+$videoad+' indirmek için tıklayınız</a>');
		$("#sagcanvas").click();
	}
});
$(document).on("click",'a#vsillink',function(){$silid=$(this).data("id");$sildiv=$(this).data("videokutu");});
$(document).on("click",'#vsilbutton',function()
{
	$data=$("#videoid").val();$arr=new Array();$arr.push($data);$arr=$data.split(',');$ekleid = new Array();
	if($silid!=0)
	{
		if($arr.length>0)
		{
			for(var i=0; i< $arr.length; i++){if($arr[i]!=$silid){if(jQuery.inArray( $arr[i], $ekleid )<0){$ekleid.push($arr[i]);}}}
			$("#videoid").val($ekleid);
		}
	}
	$("#"+$sildiv).remove();$("#btn-popup-vsil-kapat").click();
});
$resimkutu=0;$resimeskikutu=0;$resimeskiid=0;$resimustekle=0;$ren=0;$rboy=0;$yeniresimadi="";$kutular=new Array();
//$kutular=["resimkutu1","resimkutu2","resimkutu3","resimkutu4","resimkutu5","resimkutu6","resimkutu7","resimkutu8","resimkutu9","resimkutu10","resimkutu11","resimkutu12","resimkutu13","resimkutu14","resimkutu15","resimkutu16","resimkutu17","resimkutu18","resimkutu19","resimkutu20"];
var i=0;$resimadet=0;
$yeni_resim_kutu=''+
	'<div class="col-md-1 text-center" id="resimkutu_[resimid]">\n' +
	'     <input type="hidden" name="resimid[]" value="[resimid]">\n' +
	'     <div class="tile-icond">\n' +
	'     	<img id="ryer" class="size-2" src="/m/r/?resim=[resim_src]&g=80&y=80" alt="">\n' +
	'     </div>\n' +
	'     <div class="tile-text">\n' +
	'     	<a\n' +
	'       	class="btn btn-floating-action ink-reaction"\n' +
	'           id="resim_sillink"\n' +
	'           data-resimkutu="resimkutu_[resimid]"\n' +
	'           data-id="[resimid]"\n' +
	'           data-toggle="modal"\n' +
	'           data-target="#simpleModal"\n' +
	'           title="sil">\n' +
	'           <i class="fa fa-trash"></i>\n' +
	'       </a>\n' +
	'   </div>\n' +
	'</div>';
function cokluresim($cokluresimid,$cokluresimad)
{
	i++;
	//$cokluresimid yüklenen resmin id'si

	//resim kutudan eklenmişse
	if($resimustekle==0)
	{
		$yeni_resim_kutu_deger = $yeni_resim_kutu;
		$yeni_resim_kutu_deger = $yeni_resim_kutu_deger.replaceAll("[resimid]",$cokluresimid);
		$yeni_resim_kutu_deger = $yeni_resim_kutu_deger.replaceAll("[resim_src]",$cokluresimad);
		$("#resimgovde").append($yeni_resim_kutu_deger);
		$("#resimok").click();
		return false;
	}
	else
	{
		$rboy=Math.round((300/$ren)*$rboy);$ren=300;
		$resim="/m/r/"+$cokluresimad;
		if($yeniresimadi==""){$yeniresimadi=$("#resimad").val();}
		CKEDITOR.instances.ckeditor.insertHtml('<img src="'+$resim+'" alt="'+$yeniresimadi+'" width="'+$ren+'" height="'+$rboy+'" >');
		$("#resimok").click();
	}
	//});
}
$(document).on("click",".yenikutu",function()
{
	$kutudurum=0;
	$kutular=new Array();
	$kutular=["resimkutu1","resimkutu2","resimkutu3","resimkutu4","resimkutu5","resimkutu6","resimkutu7","resimkutu8","resimkutu9","resimkutu10","resimkutu11","resimkutu12","resimkutu13","resimkutu14","resimkutu15","resimkutu16","resimkutu17","resimkutu18","resimkutu19","resimkutu20",];
	jQuery.each( $kutular, function( i, val ){if($( "#" + val ).length){$kutudurum=1;return false;}});
	if($kutudurum==1)
	{
		var $div = $('div[id^="resimkutu"]:last');var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
		var $klon = $div.clone().prop('id', 'resimkutu'+num );
		$div.addClass( "form-group floating-label" );
		$div.after( $klon.html('<div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="rkon"><img id="ryer" class="img-circle size-2" src="/_y/assets/img/avatar7.jpg" ></div><h1 class="text-light no-margin" id="rad">Resim Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="sillink" data-resimkutu="resimkutu'+num+'" data-id="0" data-toggle="modal" data-target="#simpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-search" id="hazirekle" data-resimkutu="resimkutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-left" data-toggle="offcanvas" id="yeniekle" data-resimkutu="resimkutu'+num+'" data-id="0" title="ekle"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-floating-action ink-reaction resimhareket" href="javascript:void(0)" id="yukari" data-id="0" data-resimsira="'+num+'" title="Yukarı"><i class="md md-keyboard-arrow-up"></i></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-floating-action ink-reaction resimhareket" href="javascript:void(0)" id="asagi" data-id="0" data-resimsira="'+num+'" title="Aşağı"><i class="md md-keyboard-arrow-down"></i></a></div></div></div>') );
	}
	else
	{
		var $div = $('#resimgovde');var num = 1;
		$div.append('<div id="resimkutu'+num+'"><div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="rkon"><img id="ryer" class="img-circle size-2" src="/_y/assets/img/avatar7.jpg" ></div><h1 class="text-light no-margin" id="rad">Resim Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="sillink" data-resimkutu="resimkutu'+num+'" data-id="0" data-toggle="modal" data-target="#simpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-search" id="hazirekle" data-resimkutu="resimkutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-left" data-toggle="offcanvas" id="yeniekle" data-resimkutu="resimkutu'+num+'" data-id="0" title="ekle"><i class="fa fa-plus"></i></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-floating-action ink-reaction resimhareket" href="javascript:void(0)" id="yukari" data-id="0" data-resimsira="'+num+'" title="Yukarı"><i class="md md-keyboard-arrow-up"></i></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-floating-action ink-reaction resimhareket" href="javascript:void(0)" id="asagi" data-id="0" data-resimsira="'+num+'" title="Aşağı"><i class="md md-keyboard-arrow-down"></i></a></div></div></div>');
	}
});
$(document).on("click","#yeniekle",function(){
	$resimustekle=0;
});
$(document).on("click","#hazirekle",function(){$resimustekle=0;});
$(document).on("click","#syeniekle",function(){$resimustekle=1;});
$(document).on("click","#shazirekle",function(){$resimustekle=1;});
$(document).on("click","a.resimsec",function()
{
	$resimid=$(this).data("id");
	$resimlink =$(this).data("link");
	$resimad=$(this).data("ad");$ren=$(this).data("en");$rboy=$(this).data("boy");
	if($resimustekle==0)
	{
		if($("#resimid").val()=="")
		{
			$("#resimid").val($resimid);$("#"+ $resimkutu +" #rad").text($resimad);$("#"+ $resimkutu +" #sillink").attr("data-id",$resimid);
			d=$.now();$resim="/m/r/"+$resimlink+"?"+d;$("#"+ $resimkutu +" #ryer").attr("src",$resim);
			$("#"+ $resimkutu +" #yukari,#"+ $resimkutu +" #asagi").attr("data-id",$resimid);
			$("#sagcanvas").click();
		}
		else
		{
			$yeni_resim_kutu_deger = $yeni_resim_kutu;
			$yeni_resim_kutu_deger = $yeni_resim_kutu_deger.replaceAll("[resimid]",$resimid);
			$yeni_resim_kutu_deger = $yeni_resim_kutu_deger.replaceAll("[resim_src]",$resimlink);
			$("#resimgovde").append($yeni_resim_kutu_deger);
			//$("#resimok").click();
		}
	}
	else
	{
		$rboy=Math.round((300/$ren)*$rboy);$ren=300;
		CKEDITOR.instances.ckeditor.insertHtml('<img src="/m/r/'+$resimlink+'" title="'+$resimad+'" width="'+$ren+'" height="'+$rboy+'" >')
		$("#sagcanvas").click();
	}
});
$(document).on("click",'a#sillink',function (){$silid=$(this).data("id");$sildiv=$(this).data("resimkutu");});
$(document).on("click",'#silbutton',function ()
{
	$data=$("#resimid").val();$arr=new Array();	$arr.push($data);$arr = $data.split(',');$ekleid = new Array();
	if($silid!=0)
	{
		if($arr.length>0)
		{
			for(var i=0; i< $arr.length; i++)
			{
				if($arr[i]!=$silid)
				{
					if(jQuery.inArray( $arr[i], $ekleid )<0){$ekleid.push($arr[i]);}
				}
			}
			$("#resimid").val($ekleid);
		}
	}
	$("#"+$sildiv).remove();$("#btn-popup-sil-kapat").click();
});
$yuklene="";
Dropzone.autoProcessQueue= true;
Dropzone.options.myawesomedropzoneresim =
	{
		parallelUploads: 10,
		autoProcessQueue: true,
		addRemoveLinks: true,
		maxFiles: 10,
		maxFilesize: 150,
		dictDefaultMessage: "Resimleri yüklemek için bırakın",
		dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
		dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
		dictFileTooBig: "Resim çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
		dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
		dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
		dictCancelUpload: "İptal Et",
		dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
		dictRemoveFile: "Resim Sil",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Daha fazla resim yükleyemezsiniz.",
		acceptedFiles: ".jpeg,.jpg,.png,.gif",

		removedfile: function(file){var _ref;return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;},
		init: function()
		{
			this.on("success", function(file, responseText)
			{
				$resimadi=responseText.replace('"', '');$resimadi=$resimadi.replace('"', '');
				$resimadi=$resimadi.replace('\\', '');res=$resimadi.split("|");
				$resimadi=res[0];
				$resimid=res[1];
				$ren=res[2];
				$rboy=res[3];
				$resimadet++;
				cokluresim($resimid,$resimadi);
				if($("#textModal").css('display') == 'none')
				{
					$("#solcanvas").click();
					$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
					$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
					$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
					this.removeAllFiles();
					$("#resimad").val("");
				}
			});
			this.on("addedfile", function(file)
			{
				if($("#resimad").val().length<1)
				{
					if($("#sayfaad").val())
					{
						$("#resimad").val($("#sayfaad").val());
					}
					else
					{
						$("#textModal").modal('show');
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Adı Girin (En az 3 harf)");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
						$("#solcanvas").modal('show');
						this.removeFile(file);

					}
				}
			});
		}
	};
Dropzone.options.myawesomedropzonedosya =
	{
		parallelUploads: 10,
		autoProcessQueue: true,
		addRemoveLinks: true,
		maxFiles: 10,
		maxFilesize: 150,
		dictDefaultMessage: "Dosyaları yüklemek için bırakın",
		dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
		dictFallbackText: "Dosyalarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
		dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
		dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
		dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
		dictCancelUpload: "İptal Et",
		dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
		dictRemoveFile: "Dosya Sil",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
		acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4,.ogv,.webm,.ods,.odt,.odp,.pdf,.xls,.xlsx,.zip,.excel,.doc,.docx,.csv,.odf,.xml,.ppt,.pptx",

		removedfile: function(file){var _ref;return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;},
		init: function()
		{
			this.on("success", function(file, responseText)
			{
				$resimadi=responseText.replace('"', '');$resimadi=$resimadi.replace('"', '');
				$resimadi=$resimadi.replace('\\', '');res=$resimadi.split("|");
				$resimadi=res[0];
				$resimid=res[1];
				$ren=res[2];
				$rboy=res[3];
				if($.isNumeric($ren))
				{
					cokluresim($resimid,$resimadi);
					if($("#textModal").css('display') == 'none')
					{
						$("#solcanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#resimad").val("");
					}
				}
				else if($ren=="mp4" || $ren=="ogv" || $ren=="webm")
				{
					cokluvideo($resimid,$resimadi,$ren);
					if($("#textModal").css('display') == 'none')
					{
						$("#filecanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Video Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#videoad").val("");
					}
				}
				else if($resimadi=="urun.xls" || $resimadi=="urun.xlsx")
				{
					if($("#textModal").css('display') == 'none')
					{
						$("#filecanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Ürün Listesi Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#adimlink").attr("href", "/_y/s/s/urunler/excel-urun-yukle.php?adim=1&dosya=/m/r/havuz/"+$resimadi);
						$("#textModal").css("display", "block");
					}
				}
				else
				{
					cokludosya($resimid,$resimadi,$ren);
					if($("#textModal").css('display') == 'none')
					{
						$("#filecanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Dosya Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#dosyaad").val("");
					}
				}
			});
			this.on("addedfile", function(file)
			{
				if($yuklene=="dosya")
				{
					if($("#dosyaad").val().length<1)
					{
						if($("#textModal").css('display') == 'none')
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Dosya Adı Girin");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#filecanvas").click();
							this.removeFile(file);
						}
					}
				}
				else if($yuklene=="video")
				{
					if($("#videoad").val().length<1)
					{
						if($("#textModal").css('display') == 'none')
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Video Adı Girin");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#videocanvas").click();
							this.removeFile(file);
						}
					}
				}
				else if($yuklene=="resim")
				{
					if($("#resimad").val().length<1)
					{
						if($("#textModal").css('display') == 'none')
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Adı Girin (En az 3 harf)");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#solcanvas").click();
							this.removeFile(file);
						}
					}
				}
				else if($yuklene=="urunlistesi")
				{

				}
			});
		}
	};
Dropzone.options.myawesomedropzonevideo =
	{
		parallelUploads: 1,
		maxFiles: 1,
		maxFilesize: 200,
		dictDefaultMessage: "Videoları yüklemek için bırakın",
		dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
		dictFallbackText: "Videolarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
		dictFileTooBig: "Video çok büyük ({{filesize}}MB). Maksimum dosya boyutu: {{maxFilesize}} MB.",
		dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
		dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
		dictCancelUpload: "İptal Et",
		dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
		dictRemoveFile: "Video Sil",
		acceptedFiles: ".mp4,.ogv,.webm",

		removedfile: function(file){var _ref;return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;},
		init: function()
		{
			this.on("success", function(file, responseText)
			{
				$resimadi=responseText.replace('"', '');$resimadi=$resimadi.replace('"', '');
				$resimadi=$resimadi.replace('\\', '');res=$resimadi.split("|");
				$resimadi=res[0];
				$resimid=res[1];
				$ren=res[2];
				$rboy=res[3];
				if($.isNumeric($ren))
				{
					cokluresim($resimid,$resimadi);
					if($("#textModal").css('display') == 'none')
					{
						$("#solcanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#resimad").val("");
					}
				}
				else if($ren=="mp4" || $ren=="ogv" || $ren=="webm")
				{
					cokluvideo($resimid,$resimadi,$ren);
					if($("#textModal").css('display') == 'none')
					{
						$("#filecanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Video Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#videoad").val("");
					}
				}
				else if($resimadi=="urun.xls" || $resimadi=="urun.xlsx")
				{
					if($("#textModal").css('display') == 'none')
					{
						$("#filecanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Ürün Listesi Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#adimlink").attr("href", "/_y/s/s/urunler/excel-urun-yukle.php?adim=1&dosya=/m/r/havuz/"+$resimadi);
						$("#textModal").css("display", "block");
					}
				}
				else
				{
					cokludosya($resimid,$resimadi,$ren);
					if($("#textModal").css('display') == 'none')
					{
						$("#filecanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Dosya Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#dosyaad").val("");
					}
				}
			});
			this.on("addedfile", function(file)
			{
				if($yuklene=="dosya")
				{
					if($("#dosyaad").val().length<1)
					{
						if($("#textModal").css('display') == 'none')
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Dosya Adı Girin");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#filecanvas").click();
							this.removeFile(file);
						}
					}
				}
				else if($yuklene=="video")
				{
					if($("#videoad").val().length<1)
					{
						if($("#textModal").css('display') == 'none')
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Video Adı Girin");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#videocanvas").click();
							this.removeFile(file);
						}
					}
				}
				else if($yuklene=="resim")
				{
					if($("#resimad").val().length<1)
					{
						if($("#textModal").css('display') == 'none')
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Adı Girin (En az 3 harf)");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#solcanvas").click();
							this.removeFile(file);
						}
					}
				}
				else if($yuklene=="urunlistesi")
				{

				}
			});
		}
	};
Dropzone.options.myawesomedropzoneurun =
	{
		parallelUploads: 1,
		autoProcessQueue: true,
		addRemoveLinks: true,
		maxFiles: 1,
		maxFilesize: 150,
		dictDefaultMessage: "Listeyi yüklemek için dosyayı sürükleyip bırakın",
		dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
		dictFallbackText: "Dosyalarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
		dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
		dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
		dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
		dictCancelUpload: "İptal Et",
		dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
		dictRemoveFile: "Dosya Sil",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
		acceptedFiles: ".xls,.xlsx",

		removedfile: function(file){var _ref;return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;},
		init: function()
		{
			this.on("success", function(file, responseText)
			{
				$resimadi=responseText.replace('"', '');$resimadi=$resimadi.replace('"', '');
				$resimadi=$resimadi.replace('\\', '');res=$resimadi.split("|");
				$resimadi=res[0];
				$resimid=res[1];
				$ren=res[2];
				$rboy=res[3];
				if($resimadi=="urun.xls" || $resimadi=="urun.xlsx")
				{
					if($("#textModal").css('display') == 'none')
					{
						$("#uruncanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Ürün Listesi Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#adimlink").attr("href", "/_y/s/s/urunler/excel-urun-yukle.php?adim=1&dosya=/m/r/havuz/"+$resimadi);
						$("#textModal").css("display", "block");
					}
				}
			});
			this.on("addedfile", function(file)
			{
				if($yuklene=="urunlistesi")
				{

				}
			});
		}
	};
Dropzone.options.myawesomedropzoneurunfiyat =
	{
		parallelUploads: 1,
		autoProcessQueue: true,
		addRemoveLinks: true,
		maxFiles: 1,
		maxFilesize: 150,
		dictDefaultMessage: "Listeyi yüklemek için dosyayı sürükleyip bırakın",
		dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
		dictFallbackText: "Dosyalarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
		dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
		dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
		dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
		dictCancelUpload: "İptal Et",
		dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
		dictRemoveFile: "Dosya Sil",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
		acceptedFiles: ".xls,.xlsx",

		removedfile: function(file){var _ref;return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;},
		init: function()
		{
			this.on("success", function(file, responseText)
			{
				console.log(responseText);
				$resimadi=responseText.replace('"', '');$resimadi=$resimadi.replace('"', '');
				$resimadi=$resimadi.replace('\\', '');res=$resimadi.split("|");

				$resimadi=res[0];
				$resimid=res[1];
				$ren=res[2];
				$rboy=res[3];
				if($resimadi=="urun-fiyat.xls" || $resimadi=="urun-fiyat.xlsx")
				{
					if($("#textModal").css('display') == 'none')
					{
						$("#urunfiyatcanvas").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Ürün Listesi Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#adimlink").attr("href", "/_y/s/s/urunler/excel-urun-fiyatguncelle.php?dosya="+$resimadi);
						$("#textModal").css("display", "block");
					}
				}
			});
			this.on("addedfile", function(file)
			{
				if($yuklene=="urunlistesifiyat")
				{

				}
			});
		}
	};
Dropzone.options.myawesomedropzoneurunfiyaten =
	{
		parallelUploads: 1,
		autoProcessQueue: true,
		addRemoveLinks: true,
		maxFiles: 1,
		maxFilesize: 150,
		dictDefaultMessage: "Listeyi yüklemek için dosyayı sürükleyip bırakın",
		dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
		dictFallbackText: "Dosyalarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
		dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
		dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
		dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
		dictCancelUpload: "İptal Et",
		dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
		dictRemoveFile: "Dosya Sil",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
		acceptedFiles: ".xls,.xlsx",

		removedfile: function(file){var _ref;return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;},
		init: function()
		{
			this.on("success", function(file, responseText)
			{
				console.log(responseText);
				$resimadi=responseText.replace('"', '');$resimadi=$resimadi.replace('"', '');
				$resimadi=$resimadi.replace('\\', '');res=$resimadi.split("|");

				$resimadi=res[0];
				$resimid=res[1];
				$ren=res[2];
				$rboy=res[3];
				if($resimadi=="urun-fiyat.xls" || $resimadi=="urun-fiyat.xlsx")
				{
					if($("#textModal").css('display') == 'none')
					{
						$("#urunfiyatcanvasen").click();
						$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Ürün Listesi Yükleme Başarılı");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
						$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
						this.removeAllFiles();
						$("#adimlink").attr("href", "/_y/s/s/urunler/excel-urun-fiyatguncelle-en.php?dosya="+$resimadi);
						$("#textModal").css("display", "block");
					}
				}
			});
			this.on("addedfile", function(file)
			{
				if($yuklene=="urunlistesifiyaten")
				{

				}
			});
		}
	};
$(document).on("click","body a",function()
{
	$link=$(this).attr("href");
	if($link=="#offcanvas-left")
	{
		$yuklene="resim";
	}
	else if($link=="#offcanvas-file")
	{
		$yuklene="dosya";
	}
	else if($link=="#offcanvas-video")
	{
		$yuklene="video";
	}
	else if($link=="#offcanvas-topluurun")
	{
		$yuklene="urunlistesi";
	}
	else if($link=="#offcanvas-toplufiyat")
	{
		$yuklene="urunlistesifiyat";
	}
	else if($link=="#offcanvas-toplufiyaten")
	{
		$yuklene="urunlistesifiyaten";
	}
});
$("div#resimgovde").sortable();