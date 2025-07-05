//melzeme var mı
$('input[type=radio][name=malzeme]').change(function()
{
    if (this.value == 0)
    {
        $("#malzemegrupsatir").hide();
        $('input[type=radio][name=malzemegrupid]').prop('checked', false);
        $("#malzemekutu").html("");
        if($('input[name=renk]:checked').val()==0)
        {
            $("#varyantkutu").empty();
            $tvaryantsatir=$varyantsatir.replace("[malzemeid]","0");
            $tvaryantsatir=$tvaryantsatir.replace("[malzemead]","-");
            $tvaryantsatir=$tvaryantsatir.replace("[renkid]","0");
            $tvaryantsatir=$tvaryantsatir.replace("[renkad]","-");
            $("#varyantkutu").append('<div id="urunfiyatsatirb0r0" class="urunfiyatsatir">'+ $tvaryantsatir +'</div>');
            $("#urunfiyatsatirb0r0 #urunsatisfiyat_varyant").val($sfiyat);
            $("#urunfiyatsatirb0r0 #urunindirimsizfiyat_varyant").val($ifiyat);
            $("#urunfiyatsatirb0r0 #urunbayifiyat_varyant").val($bfiyat);
            $("#urunfiyatsatirb0r0 #urunalisfiyat_varyant").val($afiyat);
            $("#urunfiyatsatirb0r0 #urunstok_varyant").val($stk);
            $("#urunfiyatsatirb0r0 #urunstokkodu_varyant").val($stkno);
        }
    }
    else
    {
        $("#malzemegrupsatir").show();
        //$("#urunfiyatvaryant").show();
    }
});
//beden var mı
$('input[type=radio][name=beden]').change(function()
{
    if (this.value == 0)
    {
        $("#bedengrupsatir").hide();
        $('input[type=radio][name=bedengrupid]').prop('checked', false);
        $("#bedenkutu").html("");
        if($('input[name=renk]:checked').val()==0)
        {
        	$("#varyantkutu").empty();
            $tvaryantsatir=$varyantsatir.replace("[bedenid]","0");
            $tvaryantsatir=$tvaryantsatir.replace("[bedenad]","-");
            $tvaryantsatir=$tvaryantsatir.replace("[renkid]","0");
            $tvaryantsatir=$tvaryantsatir.replace("[renkad]","-");
        	$("#varyantkutu").append('<div id="urunfiyatsatirb0r0" class="urunfiyatsatir">'+ $tvaryantsatir +'</div>');
            $("#urunfiyatsatirb0r0 #urunsatisfiyat_varyant").val($sfiyat);
            $("#urunfiyatsatirb0r0 #urunindirimsizfiyat_varyant").val($ifiyat);
            $("#urunfiyatsatirb0r0 #urunbayifiyat_varyant").val($bfiyat);
            $("#urunfiyatsatirb0r0 #urunalisfiyat_varyant").val($afiyat);
            $("#urunfiyatsatirb0r0 #urunstok_varyant").val($stk);
            $("#urunfiyatsatirb0r0 #urunstokkodu_varyant").val($stkno);
        }
    }
    else
    {
    	$("#bedengrupsatir").show();
        //$("#urunfiyatvaryant").show();
    }
});
//renk var mı
$('input[type=radio][name=renk]').change(function()
{
    if (this.value == 0)
    {
        $("#renkgrupsatir").hide();
        $('input[type=radio][name=renkgrupid]').prop('checked', false);
        $("#renkkutu").html("");
        if($('input[name=beden]:checked').val()==0)
        {
            
        }
    }
    else
    {
    	$("#renkgrupsatir").show();
        //$("#urunfiyatvaryant").show();
    }
});
//Malzeme grubu seç
$('input[type=radio][name=malzemegrupid]').change(function()
{
    //$urunmalzemegrup_ayir = urunmalzemegruplar.split(',');
    //$urunmalzemegrup_toplam = $urunmalzemegrup_ayir.length;
    $secilimalzemegrupid=$('input[name=malzemegrupid]:checked').val();
    //alert($secilimalzemegrupid);
    $seciligurubunmalzemeleri=$("#malzemegrup" + $secilimalzemegrupid).data("id");
    //alert($seciligurubunmalzemeleri);
    //Seçili Grubun malzemeleri yaz
    $malzemeayir=$seciligurubunmalzemeleri.split('*');
    $malzemekutuyaz="";
    for($i=0;$i<$malzemeayir.length;$i++)
    {
        $malzemedeger=$malzemeayir[$i];
        $malzemeid= $malzemedeger.split('|')[0];
        $malzemead= $malzemedeger.split('|')[1];
        $malzemekutuyaz=$malzemekutuyaz + '<div class="col-sm-4"><label for="malzemeler'+ $malzemeid +'" class="checkbox-inline checkbox-styled checkbox-primary"><input name="malzemeler" id="malzemeler'+ $malzemeid +'" type="checkbox" value="'+ $malzemeid +'" ><span>'+ $malzemead +'</span></label></div>';
    }
    $("#malzemekutu").html($malzemekutuyaz);
});
//beden grubu seç
$('input[type=radio][name=bedengrupid]').change(function()
{
	//$urunbedengrup_ayir = urunbedengruplar.split(',');
	//$urunbedengrup_toplam = $urunbedengrup_ayir.length;
	$secilibedengrupid=$('input[name=bedengrupid]:checked').val();
	//alert($secilibedengrupid);
	$seciligurubunbedenleri=$("#bedengrup" + $secilibedengrupid).data("id");
	//alert($seciligurubunbedenleri);
	//Seçili Grubun bedenleri yaz
	$bedenayir=$seciligurubunbedenleri.split('*');
	$bedenkutuyaz="";
	for($i=0;$i<$bedenayir.length;$i++)
	{
		$bedendeger=$bedenayir[$i];
		$bedenid= $bedendeger.split('|')[0];
		$bedenad= $bedendeger.split('|')[1];
		$bedenkutuyaz=$bedenkutuyaz + '<div class="col-sm-3"><label for="bedenler'+ $bedenid +'" class="checkbox-inline checkbox-styled checkbox-primary"><input name="bedenler" id="bedenler'+ $bedenid +'" type="checkbox" value="'+ $bedenid +'" ><span>'+ $bedenad +'</span></label></div>';
	}
	$("#bedenkutu").html($bedenkutuyaz);
});
//renk seç
$('input[type=radio][name=renkgrupid]').change(function()
{
	//$urunrenkgrup_ayir = urunrenkgruplar.split(',');
	//$urunrenkgrup_toplam = $urunrenkgrup_ayir.length;
	$secilirenkgrupid=$('input[name=renkgrupid]:checked').val();
	//alert($secilirenkgrupid);
	$seciligurubunrenkleri=$("#renkgrup" + $secilirenkgrupid).data("id");
	//alert($seciligurubunrenkleri);
	//Seçili Grubun renkleri yaz
	$renkayir=$seciligurubunrenkleri.split('*');
	$renkkutuyaz="";
	for($i=0;$i<$renkayir.length;$i++)
	{
		$renkdeger=$renkayir[$i];
		$renkid= $renkdeger.split('|')[0];
		$renkad= $renkdeger.split('|')[1];
		$renkkutuyaz=$renkkutuyaz + '<div class="col-sm-2"><label for="renkler'+ $renkid +'" class="checkbox-inline checkbox-styled checkbox-primary"><input name="renkler" id="renkler'+ $renkid +'" type="checkbox" value="'+ $renkid +'" ><span>'+ $renkad +'</span></label></div>';
	}
	$("#renkkutu").html($renkkutuyaz);
});
