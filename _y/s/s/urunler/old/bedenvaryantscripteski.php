/*var row ='<div class="col-sm-1"><div class="form-group"><input type="text" name="urunsatisfiyat_varyant[]" id="urunsatisfiyat_varyant" class="form-control text-danger" placeholder="99.99" value="0.00" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunsatisfiyat_varyant" class="text-danger">Satış Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunindirimsizfiyat_varyant[]" id="urunindirimsizfiyat_varyant" class="form-control" placeholder="79.99" value="0.00" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunindirimsizfiyat_varyant">İnd.Siz Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunbayifiyat_varyant[]" id="urunbayifiyat_varyant" class="form-control" placeholder="79.99" value="0.00" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunbayifiyat_varyant[]">Bayi Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunalisfiyat_varyant[]" id="urunalisfiyat_varyant" class="form-control" placeholder="49.99" value="0.00" data-rule-number="true"><label for="urunalisfiyat">Alış Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunstok_varyant[]" id="urunstok_varyant" class="form-control" placeholder="99" value="100" data-rule-digits="true" required="" aria-required="true" aria-invalid="false"><label for="urunstok" required aria-required="true">Stok</label></div></div><div class="col-sm-2"><div class="form-group"><input type="text" name="urunstokkodu_varyant[]" id="urunstokkodu_varyant" class="form-control" placeholder="URN:STK-AA123" value="" required="" aria-required="true" aria-invalid="false" value="x"><label for="urunstokkodu_varyant" required aria-required="true">Ürün Stok Kodu</label></div></div>';
function bedentikla()
{
    $secilibedenidler = []; $secilibedenadlar = []; $bedentoplam=0;
    $('input[type="checkbox"][name=bedenler]:checked').each(function()
    {
       $secilibedenidler.push($(this).val());
       $secilibedenadlar.push($('label[for=bedenler'+ $(this).val() +']').text());
    });
    $bedentoplam=$secilibedenidler.length;

    if($bedentoplam>0)
    {
        //**********
        $urunbedentut=[];
        $.each( $secilibedenidler, function( $i, $deger )
        {
            $bedenid= $deger;
            $bedenad= $secilibedenadlar[$i];


            $ii=$i-1;
            $divid="b"+$bedenid;
            $urunbedentut.push( $divid );

            if($i==0)
            {
                if ($("#urunfiyatsatir0").length>0)$("#urunfiyatsatir0").prop('id', 'urunfiyatsatir'+$divid);
            }

            if ($("#urunfiyatsatir"+$divid).length<=0)
            {
                $("#varyantkutu").append('<div id="urunfiyatsatir'+$divid+'" class="urunfiyatsatir">'+ $varyantsatir +'</div>');
            }
            if ($("#urunfiyatsatir"+$divid).length>0)
            {
                
                $("#urunfiyatsatir"+$divid+" #urunbedenlerid").val($bedenid);
                $("#urunfiyatsatir"+$divid+" #urunbedenad").val($bedenad);

                $renkid=$("#urunfiyatsatir"+$divid+" #urunrenklerid").val();
                $renkad=$("#urunfiyatsatir"+$divid+" #urunrenkad").val();
                $satisfiyat=$("#urunfiyatsatir"+$divid+" #urunsatisfiyat_varyant").val();
                $insatisfiyat=$("#urunfiyatsatir"+$divid+" #urunindirimsizfiyat_varyant").val();
                $bayifiyat=$("#urunfiyatsatir"+$divid+" #urunbayifiyat_varyant").val();
                $alisfiyat=$("#urunfiyatsatir"+$divid+" #urunalisfiyat_varyant").val();
                $stok=$("#urunfiyatsatir"+$divid+" #urunstok_varyant").val();
                $stokkodu=$("#urunfiyatsatir"+$divid+" #urunstokkodu_varyant").val();

                $("#urunfiyatsatir"+$divid+" #urunrenklerid").val($renkid);
                $("#urunfiyatsatir"+$divid+" #urunrenkad").val($renkad);
                /*if($satisfiyat!="0"&&$satisfiyat!=""&&$satisfiyat!="0.00")*/
                $("#urunfiyatsatir"+$divid+" #urunsatisfiyat_varyant").val($satisfiyat);
                /*if($insatisfiyat!="0"&&$insatisfiyat!=""&&$insatisfiyat!="0.00")*/
                $("#urunfiyatsatir"+$divid+" #urunindirimsizfiyat_varyant").val($insatisfiyat);
                /*if($bayifiyat!="0"&&$bayifiyat!=""&&$bayifiyat!="0.00")*/
                $("#urunfiyatsatir"+$divid+" #urunbayifiyat_varyant").val($bayifiyat);
                /*if($alisfiyat!="0"&&$alisfiyat!=""&&$alisfiyat!="0.00")*/
                $("#urunfiyatsatir"+$divid+" #urunalisfiyat_varyant").val($alisfiyat);
                /*if($stok!="")*/$("#urunfiyatsatir"+$divid+" #urunstok_varyant").val($stok);
                /*if($stokkodu!="")*/$("#urunfiyatsatir"+$divid+" #urunstokkodu_varyant").val($stokkodu);

                if($i>0)
                {
                    
                    if($renkid=="0"||$renkad=="-")
                    {
                        $renkid=$("#urunfiyatsatir"+$urunbedentut[$ii]+" #urunrenklerid").val();
                        $renkad=$("#urunfiyatsatir"+$urunbedentut[$ii]+" #urunrenkad").val();
                        $("#urunfiyatsatir"+$divid+" #urunrenklerid").val($renkid);
                        $("#urunfiyatsatir"+$divid+" #urunrenkad").val($renkad);
                    }
                    if($satisfiyat==""||$satisfiyat=="0")
                    {
                        $satisfiyat=$("#urunfiyatsatir"+ $urunbedentut[$ii] +" #urunsatisfiyat_varyant").val();
                        $("#urunfiyatsatir"+$divid+" #urunsatisfiyat_varyant").val($satisfiyat);
                    }
                    if($insatisfiyat==""||$insatisfiyat=="0")
                    {
                        $insatisfiyat=$("#urunfiyatsatir"+ $urunbedentut[$ii] +" #urunindirimsizfiyat_varyant").val();
                        $("#urunfiyatsatir"+$divid+" #urunindirimsizfiyat_varyant").val($insatisfiyat);
                    }
                    if($bayifiyat==""||$bayifiyat=="0")
                    {
                        $bayifiyat=$("#urunfiyatsatir"+ $urunbedentut[$ii] +" #urunbayifiyat_varyant").val();
                        $("#urunfiyatsatir"+$divid+" #urunbayifiyat_varyant").val($bayifiyat);
                    }
                    if($alisfiyat==""||$alisfiyat=="0")
                    {
                        $alisfiyat=$("#urunfiyatsatir"+ $urunbedentut[$ii] +" #urunalisfiyat_varyant").val();
                        $("#urunfiyatsatir"+$divid+" #urunalisfiyat_varyant").val($alisfiyat);
                    }
                    if($stok=="")
                    {
                        $stok=$("#urunfiyatsatir"+ $urunbedentut[$ii] +" #urunstok_varyant").val();
                        $("#urunfiyatsatir"+$divid+" #urunstok_varyant").val($stok);
                    }
                    if($stokkodu=="")
                    {
                        $stokkodu=$("#urunfiyatsatir"+ $urunbedentut[$ii] +" #urunstokkodu_varyant").val();
                        $("#urunfiyatsatir"+$divid+" #urunstokkodu_varyant").val($stokkodu+"-"+$bedenid);
                    }
                }
            }
            
            //alert($varyant);
            $renkvarmi=$('input[name=renk]:checked').val();
            /*if($renktoplam>0 && $renkvarmi==1)
            {
                $.each( $secilirenkidler, function( $ii, $deger2 )
                {
                    $varyant2="";
                    $renkid= $deger2;
                    $renkad= $secilirenkadlar[$ii];
                    $varyant2='<div class="col-sm-1"><div class="form-group"><input type="hidden" name="urunrenklerid[]" value="'+ $renkid +'"><input type="text" name="urunrenkad" id="urunrenkad" class="form-control text-danger" value="'+ $renkad +'" readonly><label for="urunrenkad">-</label></div></div>';
                    $satir=$satir+'<div class="row"><div class="col-sm-2"> </div>'+$varyant+$varyant2+row+"</div>";
                });
            }
            else
            {
                $satir=$satir+'<div class="row"><div class="col-sm-2"> </div>'+$varyant+row+"</div>";
            }*/
        });
        //*********
        //$("#varyanticerik").html($satir);
    }
}
function renktikla()
{
    $renktoplam=0;
    if($('input[type="checkbox"][name=renkler]'))
    {
        $secilirenkidler = []; $secilirenkadlar = [];
        $('input[type="checkbox"][name=renkler]:checked').each(function()
        {
           $secilirenkidler.push($(this).val());
           $secilirenkadlar.push($('label[for=renkler'+ $(this).val() +']').text());
        });
        $renktoplam=$secilirenkidler.length;
        if($renktoplam==1)
        {
            $('[id="urunrenkad"]').val($secilirenkadlar[0]);
            $('[id="urunrenklerid"]').val($secilirenkidler[0]);
        }
        else if($renktoplam>1)
        {
            //alert($( ".urunfiyatsatir" ).length);
            
            
            $.each( $secilirenkidler, function( $ii, $deger2 )
            {
                $renkvar=0;
                $renkid = $deger2;
                $renkad = $secilirenkadlar[$ii];
                    $( ".urunfiyatsatir" ).each(function()
                    {
                        $divid=$(this).attr('id');alert($divid);
                        $renkvarid=$("#urunfiyatsatir"+$divid+" #urunrenklerid").val();
                        if($renkvarid==$renkid)
                        {
                            $renkvar==1;break;
                        }
                    });
                if($renkvar==0)
                {
                    $secilibedenidler = []; $secilibedenadlar = []; $bedentoplam=0;
                    $('input[type="checkbox"][name=bedenler]:checked').each(function()
                    {
                       $secilibedenidler.push($(this).val());
                       $secilibedenadlar.push($('label[for=bedenler'+ $(this).val() +']').text());
                    });
                    $bedentoplam=$secilibedenidler.length;
                    $varyantsatir='<div class="col-sm-2">-</div><div class="col-sm-1"><div class="form-group"><input type="hidden" name="urunbedenlerid[]" id="urunbedenlerid" value="0"><input type="text" name="urunbedenad" id="urunbedenad" class="form-control text-danger" value="-" readonly="" aria-invalid="false"><label for="urunbedenlerid">Beden</label></div></div><div class="col-sm-2"><div class="form-group"><input type="hidden" name="urunrenklerid[]" id="urunrenklerid" value="'+$renkid+'"><input type="text" name="urunrenkad" id="urunrenkad" class="form-control text-danger" value="'+$renkad+'" readonly="" aria-invalid="false"><label for="urunrenkad">Renk</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunsatisfiyat_varyant[]" id="urunsatisfiyat_varyant" class="form-control text-danger" placeholder="99.99" value="0" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunsatisfiyat" class="text-danger">Satış Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunindirimsizfiyat_varyant[]" id="urunindirimsizfiyat_varyant" class="form-control" placeholder="79.99" value="0" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunindirimsizfiyat">İnd.Siz Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunbayifiyat_varyant[]" id="urunbayifiyat_varyant" class="form-control" placeholder="79.99" value="0" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunindirimsizfiyat">Bayi Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunalisfiyat_varyant[]" id="urunalisfiyat_varyant" class="form-control" placeholder="49.99" value="0" data-rule-number="true"><label for="urunalisfiyat">Alış Fiyat</label></div></div><div class="col-sm-1"><div class="form-group"><input type="text" name="urunstok_varyant[]" id="urunstok_varyant" class="form-control" placeholder="99" value="" data-rule-digits="true" required="" aria-required="true" aria-invalid="false"><label for="urunstok" required aria-required="true">Stok</label></div></div><div class="col-sm-2"><div class="form-group"><input type="text" name="urunstokkodu_varyant[]" id="urunstokkodu_varyant" class="form-control" placeholder="STK-AA123" value="" required="" aria-required="true" aria-invalid="false"><label for="urunstokkodu" required aria-required="true">Stok Kodu</label></div></div>';
                    if($bedentoplam>0)
                    {
                        $urunbedentut=[];
                        $.each( $secilibedenidler, function( $i, $deger )
                        {
                            $bedenid= $deger;
                            $bedenad= $secilibedenadlar[$i];


                            $ii=$i-1;
                            //$divid="b"+$bedenid;
                            $urunbedentut.push( $divid );

                            /*if($i==0)
                            {
                                if ($("#urunfiyatsatir0").length>0)$("#urunfiyatsatir0").prop('id', 'urunfiyatsatir'+$divid);
                            }*/

                            if ($("#urunfiyatsatir"+$divid).length<=0)
                            {
                                $("#varyantkutu").append('<div id="urunfiyatsatir'+$divid+'" class="urunfiyatsatir">'+ $varyantsatir +'</div>');
                            }
                        });
                    }
                }
            });
            
            
        }
    }
}
function varyantkontrolbeden()
{
	$secilibedenidler = []; $secilibedenadlar = []; $bedentoplam=0;
    $('input[type="checkbox"][name=bedenler]:checked').each(function()
    {
       $secilibedenidler.push($(this).val());
       $secilibedenadlar.push($('label[for=bedenler'+ $(this).val() +']').text());
    });
    $bedentoplam=$secilibedenidler.length;
    //alert(secilibedenler);

    $renktoplam=0;
    if($('input[type="checkbox"][name=renkler]'))
    {
    	$secilirenkidler = []; $secilirenkadlar = [];
	    $('input[type="checkbox"][name=renkler]:checked').each(function()
	    {
	       $secilirenkidler.push($(this).val());
	       $secilirenkadlar.push($('label[for=renkler'+ $(this).val() +']').text());
	    });
	    $renktoplam=$secilirenkidler.length;
	    //alert($secilirenkadlar);
    }
    if($bedentoplam>0)
    {
    	//**********
    	$satir="";
    	$.each( $secilibedenidler, function( $i, $deger )
		{

			$varyant="";$varyant2="";
			$bedenid= $deger;
			$bedenad= $secilibedenadlar[$i];
			//alert($bedenid +" * "+ $bedenad);
			$varyant='<div class="col-sm-1"><div class="form-group"><input type="hidden" name="urunbedenlerid[]" value="'+ $bedenid +'"><input type="text" name="urunbedenad" id="urunbedenad" class="form-control text-danger" value="'+ $bedenad +'" readonly><label for="urunbedenid">-</label></div></div>';
			$renkvarmi=$('input[name=renk]:checked').val();
			if($renktoplam>0 && $renkvarmi==1)
			{
				$.each( $secilirenkidler, function( $ii, $deger2 )
				{
					$varyant2="";
					$renkid= $deger2;
					$renkad= $secilirenkadlar[$ii];
					$varyant2='<div class="col-sm-1"><div class="form-group"><input type="hidden" name="urunrenklerid[]" value="'+ $renkid +'"><input type="text" name="urunrenkad" id="urunrenkad" class="form-control text-danger" value="'+ $renkad +'" readonly><label for="urunrenkad">-</label></div></div>';
					$satir=$satir+'<div class="row"><div class="col-sm-2"> </div>'+$varyant+$varyant2+row+"</div>";
				});
			}
			else
			{
				$satir=$satir+'<div class="row"><div class="col-sm-2"> </div>'+$varyant+row+"</div>";
			}
		});
    	//*********
    	$("#varyanticerik").html($satir);
    }
}
function varyantkontrolrenk()
{
	$secilirenkidler = []; $secilirenkadlar = []; $renktoplam=0;
    $('input[type="checkbox"][name=renkler]:checked').each(function()
    {
       $secilirenkidler.push($(this).val());
       $secilirenkadlar.push($('label[for=renkler'+ $(this).val() +']').text());
    });
    $renktoplam=$secilirenkidler.length;
    //alert(secilirenkler);

    $bedentoplam=0;
    if($('input[type="checkbox"][name=bedenler]'))
    {
    	$secilibedenidler = []; $secilibedenadlar = [];
	    $('input[type="checkbox"][name=bedenler]:checked').each(function()
	    {
	       $secilibedenidler.push($(this).val());
	       $secilibedenadlar.push($('label[for=bedenler'+ $(this).val() +']').text());
	    });
	    $bedentoplam=$secilibedenidler.length;
	    //alert($secilirenkadlar);
    }
    if($renktoplam>0)
    {
    	//**********
    	$satir="";
    	$.each( $secilirenkidler, function( $i, $deger )
		{

			$varyant="";$varyant2="";
			$renkid= $deger;
			$renkad= $secilirenkadlar[$i];
			//alert($renkid +" * "+ $renkad);
			$varyant='<div class="col-sm-1"><div class="form-group"><input type="hidden" name="urunrenklerid[]" value="'+ $renkid +'"><input type="text" name="urunrenkad" id="urunrenkad" class="form-control text-danger" value="'+ $renkad +'" readonly><label for="urunbedenid">-</label></div></div>';
			$bedenvarmi=$('input[name=beden]:checked').val();
			if($bedentoplam>0 && $bedenvarmi==1)
			{
				$.each( $secilibedenidler, function( $ii, $deger2 )
				{
					$varyant2="";
					$bedenid= $deger2;
					$bedenad= $secilibedenadlar[$ii];
					$varyant2='<div class="col-sm-1"><div class="form-group"><input type="hidden" name="urunbedenlerid[]" value="'+ $bedenid +'"><input type="text" name="urunbedenad" id="urunbedenad" class="form-control text-danger" value="'+ $bedenad +'" readonly><label for="urunbedenad">-</label></div></div>';
					$satir=$satir+'<div class="row"><div class="col-sm-2"> </div>'+$varyant+$varyant2+row+"</div>";
				});
			}
			else
			{
				$satir=$satir+'<div class="row"><div class="col-sm-2"> </div>'+$varyant+row+"</div>";
			}
		});
    	//*********
    	$("#varyanticerik").html($satir);
    }
}*/