<!-- Fiyat üst özellikleri -->
<div class="row">
    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">
            <h4>ÜRÜN FİYAT ÖZELLİKLERİ</h4><p></p>
            <p>Fiyat, Bayi Fiyat, KDV oranı, İndirim</p>
            <p>Ürün Alış Fiyatını (Sadece siz görebilirsiniz)</p>
        </article>
    </div>
    <div class="col-lg-offset-1 col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <select id="urunparabirim" name="urunparabirim" class="form-control">
                                <?php
                                $parabirim_d=0; $parabirim_v=""; $parabirim_s="";
                                $parabirim_s="
									SELECT 
										parabirimid,parabirimad 
									FROM 
										urunparabirim 
									Where 
										parabirimsil='0'
								";
                                $parabirim_v=$db->select($parabirim_s);
                                if($parabirim_v) $parabirim_d=1;
                                unset($parabirim_s);
                                if($parabirim_d==1)
                                {
                                    foreach($parabirim_v as $parabirim_t)
                                    {
                                        $l_parabirimid = $parabirim_t["parabirimid"];
                                        $l_parabirimad = $parabirim_t["parabirimad"];
                                        ?>
                                        <option value="<?=$l_parabirimid?>" <?php if(S($l_parabirimid)==S($f_urunparabirim))echo "selected"; ?> >
                                            <?=$l_parabirimad?>
                                        </option>
                                        <?php
                                    }
                                    unset($parabirim_t,$parabirim_v);
                                }
                                unset($parabirim_v);
                                ?>
                            </select>
                            <label for="parabirimid">Genel Para Birimi Seçin</label>
                        </div>
                    </div>
                    <div class="col-sm-6" style="margin-top:20px">
                        <label class="radio-inline radio-styled">
                            <input type="radio" name="uruneskifiyatgoster" id="uruneskifiyatgosterhayir" value="0" <?php if(S($f_uruneskifiyatgoster)==0)echo 'checked';?>><span style="text-decoration:line-through">Eski Fiyat Gösterme</span>
                        </label>
                        <label class="radio-inline radio-styled">
                            <input
                                    type="radio"
                                    name="uruneskifiyatgoster"
                                    id="uruneskifiyatgosterevet"
                                    value="1"
                                <?php if(S($f_uruneskifiyatgoster)==1)echo 'checked';?>>
                            <span>Eski Fiyat Göster
									<sup> 120 TL</sup>
								</span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                    type="text"
                                    name="uruntaksit"
                                    id="uruntaksit"
                                    class="form-control"
                                    placeholder="9"
                                    value="<?=$f_uruntaksit?>"
                                    data-rule-number="true"
                                    required=""
                                    aria-required="true"
                                    aria-invalid="false">
                            <label for="uruntaksit">Ürün Taksit</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                    type="text"
                                    name="urunkdv"
                                    id="urunkdv"
                                    class="form-control"
                                    placeholder="0.18"
                                    value="<?=$f_urunkdv?>"
                                    data-rule-number="true"
                                    required=""
                                    aria-required="true"
                                    aria-invalid="false">
                            <label for="urunindirimsizfiyat" required aria-required="true">KDV Oranı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="text" name="urunindirimorani" id="urunindirimorani" class="form-control" placeholder="0.15" value="<?=$f_urunindirimorani?>"  data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                            <label for="uruntaksit">Ürün İndirim %10 için 0.10</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                    type="text"
                                    name="urunsatisadet"
                                    id="urunsatisadet"
                                    class="form-control"
                                    placeholder="99"
                                    value="<?=$f_urunsatisadet?>"
                                    data-rule-digits="true"
                                    required=""
                                    aria-required="true"
                                    aria-invalid="false">
                            <label for="urunsatisadet" required aria-required="true">adet satılmıştır</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                type="text"
                                name="urunminimummiktar"
                                id="urunminimummiktar"
                                class="form-control"
                                placeholder="1"
                                value="<?=$f_urunminimummiktar?>"
                                data-rule-number="true"
                                required=""
                                aria-required="true"
                                aria-invalid="false">
                            <label for="urunminimummiktar">Ürün Minimum Satış Miktarı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                type="text"
                                name="urunmaksimummiktar"
                                id="urunmaksimummiktar"
                                class="form-control"
                                placeholder="5"
                                value="<?=$f_urunmaksimummiktar?>"
                                data-rule-number="true"
                                required=""
                                aria-required="true"
                                aria-invalid="false">
                            <label for="urunmaksimummiktar" required aria-required="true">Ürün Maksimum Satış Miktarı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="text"
                                   name="urunkatsayi"
                                   id="urunkatsayi"
                                   class="form-control"
                                   placeholder="1"
                                   value="<?=$f_urunkatsayi?>"
                                   data-rule-number="true" required=""
                                   aria-required="true" aria-invalid="false">
                            <label for="uruntaksit">Ürün Artış Kat Sayısı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <select id="urunmiktarbirimid" name="urunmiktarbirimid" class="form-control">
                                <option value="0">Miktar Birim Seçin</option>
                                <?php
                                $miktarBirim_s="
                                        SELECT 
                                            urunmiktarbirimid,urunmiktarbirimadi 
                                        FROM 
                                            urunmiktarbirim
                                        Where 
                                            urunmiktarbirimsil='0' 
                                    ";
                                if($db->select($miktarBirim_s))
                                {
                                    $miktarBirim_v=$db->select($miktarBirim_s);unset($miktarBirim_s);
                                    if($miktarBirim_v)
                                    {
                                        foreach($miktarBirim_v as $miktarBirim_t)
                                        {
                                            $l_urunmiktarbirimid = $miktarBirim_t["urunmiktarbirimid"];
                                            $l_murunmiktarbirimad   = $miktarBirim_t["urunmiktarbirimadi"];
                                            ?>
                                            <option value="<?=$l_urunmiktarbirimid?>" <?php if(S($l_urunmiktarbirimid)==S($f_urunmiktarbirimid))echo "selected"; ?> >
                                                <?=$l_murunmiktarbirimad?>
                                            </option>
                                            <?php
                                        }unset($miktarBirim_t,$l_urunmiktarbirimid,$l_murunmiktarbirimad);
                                    }unset($miktarBirim_v);
                                }
                                else{
                                    //hatalogisle("Ürün Ekle-Miktar Birim Getir",$db->error);
                                }
                                ?>
                            </select>
                            <label for="urunmiktarbirimid">Miktar Birim Seçin</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <em class="text-caption">Ürün Fiyat/Stok/Taksit</em>
    </div>
</div>
<!-- FİYAT STOK -->
<div class="row">
    <div class="card">
        <div class="card-body" id="varyantkutu">
            <a class="btn btn-floating-action ink-reaction btn-success" style="position:absolute;top:-10px;left:-20px;z-index:3"
               id="varyantsatirekle"
               data-id="m<?=$f_urunmalzemeid?>b<?=$f_urunbedenid?>r<?=$f_urunrenkid?>"
               title="Ekle">
                <i class="fa fa-plus"></i>
            </a>
            <?php
            $urunvaryant_s="
				SELECT 
					urunozellikleri.urunpinid,urunpin.urunmalzemead as urunpinad,
				    urunozellikleri.urunbedengrupid,urunbedengrupad,
					urunozellikleri.urunrenkgrupid,urunrenkgrupad,
					urunozellikleri.urunmalzemegrupid,urunmalzemegrupad,
					urunozellikleri.urunbedenid,urunbedenad,
					urunozellikleri.urunrenkid,urunrenkad,
					urunozellikleri.urunmalzemeid,urunmalzeme.urunmalzemead,
					urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,urunalisfiyat,urunstok,urunstokkodu,urunozellikid
				FROM 
					urunozellikleri
						LEFT JOIN urunbedengrup ON 
							urunbedengrup.urunbedengrupid=urunozellikleri.urunbedengrupid
						LEFT JOIN urunbeden ON 
							urunbeden.urunbedenid=urunozellikleri.urunbedenid
						LEFT JOIN urunrenkgrup ON 
							urunrenkgrup.urunrenkgrupid=urunozellikleri.urunrenkgrupid
						LEFT JOIN urunrenk ON 
							urunrenk.urunrenkid=urunozellikleri.urunrenkid
						LEFT JOIN urunmalzemegrup ON 
							urunmalzemegrup.urunmalzemegrupid=urunozellikleri.urunmalzemegrupid
						LEFT JOIN urunmalzeme ON 
							urunmalzeme.urunmalzemeid=urunozellikleri.urunmalzemeid	
				        LEFT JOIN urunmalzeme as urunpin ON 
							urunpin.urunmalzemeid=urunozellikleri.urunpinid	
				WHERE 
					urunozellikleri.sayfaid='".$f_sayfaid."'
						
			";
            if($db->select($urunvaryant_s))
            {
                $urunvaryant_v=$db->select($urunvaryant_s);unset($urunvaryant_s);
                if($urunvaryant_v)
                {
                    $v=0;
                    $varyantsayisi=count($urunvaryant_v); echo(  "Varyant Sayısı:  $varyantsayisi");
                    foreach ($urunvaryant_v as $urunvaryant_t)
                    {
                        $v++;
                        $f_urunindirimsizfiyat 	=$urunvaryant_t["urunindirimsizfiyat"];
                        $f_urunsatisfiyat		=$urunvaryant_t["urunsatisfiyat"];
                        $f_urunbayifiyat 		=$urunvaryant_t["urunbayifiyat"];
                        $f_urunalisfiyat 		=$urunvaryant_t["urunalisfiyat"];
                        $f_urunstok 			=$urunvaryant_t["urunstok"];
                        $f_urunstokkodu			=$urunvaryant_t["urunstokkodu"];

                        $f_urunbedengrupid		=$urunvaryant_t["urunbedengrupid"];
                        $f_urunrenkgrupid		=$urunvaryant_t["urunrenkgrupid"];
                        $f_urunmalzemegrupid	=$urunvaryant_t["urunmalzemegrupid"];
                        $f_urunbedenid			=$urunvaryant_t["urunbedenid"];
                        $f_urunrenkid			=$urunvaryant_t["urunrenkid"];
                        $f_urunmalzemeid		=$urunvaryant_t["urunmalzemeid"];
                        $f_urunbedenad			=$urunvaryant_t["urunbedenad"];
                        $f_urunrenkad			=$urunvaryant_t["urunrenkad"];
                        $f_urunmalzemead		=$urunvaryant_t["urunmalzemead"];
                        $f_urunozellikid		=$urunvaryant_t["urunozellikid"];
                        $f_urunpinid		    =$urunvaryant_t["urunpinid"];
                        $f_urunpinad		    =$urunvaryant_t["urunpinad"];
                        ?>
                        <div id="k<?=$f_urunozellikid?>" class="urunfiyatsatir border-black" style="padding: 5px;position: relative;border-color: darkgray">
                        <!-- span class="btn btn-default btn-xs " style="position:absolute;top:-10px;left:-15px;z-index: 2"
                              id="#"
                              data-id="m<?=$f_urunmalzemeid?>b<?=$f_urunbedenid?>r<?=$f_urunrenkid?>"
                        >
                            1
                        </span -->
                            <div class="col-sm-3">
                                <div class="form-group malzemediv" data-id="<?= $f_urunmalzemeid ?>" data-malzemeid="<?= $f_urunmalzemeid?>">
                                    <input type="hidden" name="urunmalzemelerid[]" id="urunmalzemelerid" value="<?= $f_urunmalzemeid ?>">
                                    <input type="text" name="urunmalzemead" autocomplete="off" data-id="0" id="m<?=$f_urunozellikid?>" class="form-control text-danger malzemead" value="<?= !empty($f_urunmalzemead) ? str_replace('"',"''",$f_urunmalzemead):'';?>" aria-invalid="false"><label for="urunmalzemead">Malzeme</label>
                                    <ul id="q<?=$f_urunozellikid?>"  style="position: absolute; width: 200%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="hidden" name="urunbedenlerid[]" id="urunbedenlerid" value="<?=$f_urunbedenid?>">
                                    <input type="text" name="urunbedenad" autocomplete="off" id="b<?=$f_urunozellikid?>" data-id="0" class="form-control text-danger olcuad" value="<?= !empty($f_urunbedenad) ? str_replace('"',"''",$f_urunbedenad):'';?>" aria-invalid="false"><label for="urunolcuad">Ölçü</label>
                                    <ul id="sb<?=$f_urunozellikid?>"  style="position: absolute; width: 200%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="hidden" name="urunrenklerid[]" id="urunrenklerid" value="<?=$f_urunrenkid?>">
                                    <input type="text" name="urunrenkad" autocomplete="off" data-id="0"  id="r<?=$f_urunozellikid?>" class="form-control text-danger renkad" value="<?=!empty($f_urunrenkad) ? str_replace('"',"''",$f_urunrenkad) :''?>"  aria-invalid="false"><label for="urunrenkad">Renk</label>
                                    <ul id="sr<?=$f_urunozellikid?>"  style="position: absolute; width: 200%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="hidden" name="urunpinlerid[]" id="urunpinlerid" value="<?=$f_urunpinid?>">
                                    <input type="text" name="urunpinad" autocomplete="off" data-id="0"  id="p<?=$f_urunozellikid?>" class="form-control text-danger pinad" value="<?= !empty($f_urunpinad) ? str_replace('"',"''",$f_urunpinad) : '';?>"  aria-invalid="false"><label for="urunpinad">Pin</label>
                                    <ul id="sp<?=$f_urunozellikid?>"  style="position: absolute; width: 100%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input
                                            type="text"
                                            name="urunstokkodu_varyant[]"
                                            id="urunstokkodu_varyant"
                                            class="form-control"
                                            autocomplete="off"
                                            placeholder="STK-AA123"
                                            value="<?=$f_urunstokkodu?>"
                                            required=""
                                            aria-required="true"
                                            aria-invalid="false">
                                    <label for="urunstokkodu_varyant" required aria-required="true">Stok Kodu</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="text" name="urunsatisfiyat_varyant[]" id="urunsatisfiyat_varyant" autocomplete="off" class="form-control text-danger" placeholder="99.99" value="<?=$f_urunsatisfiyat?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                    <label for="urunsatisfiyat_varyant" class="text-danger">Satış Fiyat</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="text" name="urunindirimsizfiyat_varyant[]" id="urunindirimsizfiyat_varyant" autocomplete="off" class="form-control" placeholder="79.99" value="<?=$f_urunindirimsizfiyat?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                    <label for="urunindirimsizfiyat_varyant">İnd.Siz Fiyat</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="text" name="urunbayifiyat_varyant[]" id="urunbayifiyat_varyant" class="form-control" autocomplete="off" placeholder="79.99" value="<?=$f_urunbayifiyat?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                    <label for="urunbayifiyat_varyant">Bayi Fiyat</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="text" name="urunalisfiyat_varyant[]" id="urunalisfiyat_varyant" class="form-control" autocomplete="off" placeholder="49.99" value="<?=$f_urunalisfiyat?>" data-rule-number="true">
                                    <label for="urunalisfiyat_varyant">Alış Fiyat</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input
                                            type="text"
                                            name="urunstok_varyant[]"
                                            id="urunstok_varyant"
                                            class="form-control"
                                            placeholder="99"
                                            value="<?=$f_urunstok?>"
                                            data-rule-digits="true"
                                            required=""
                                            autocomplete="off"
                                            aria-required="true"
                                            aria-invalid="false">
                                    <label for="urunstok_varyant" required aria-required="true">Stok</label>
                                </div>
                            </div>
                        <?php if($v>1){?>
                            <a class="btn btn-floating-action ink-reaction btn-danger kutusil" style="position:absolute;top:-10px;right:-20px;z-index: 2"
                               id="varyantsatirsil"
                               data-sil="s<?=$f_urunozellikid?>"
                               data-id="m<?=$f_urunozellikid?>"
                               title="sil">
                                <i class="fa fa-times"></i>
                            </a>
                        <?php }?>
                        </div>
                        <?php
                    }unset($urunvaryant_t);
                }
                else
                {
                    ?>
                    <div id="k0" class="urunfiyatsatir border-black" style="padding: 5px;position: relative;border-color: darkgray">
                        <span class="btn btn-default btn-xs " style="position:absolute;top:-10px;left:-15px;z-index: 2"
                              id="#"
                              data-id="m<?=$f_urunmalzemeid?>b<?=$f_urunbedenid?>r<?=$f_urunrenkid?>"
                        >
                        </span>
                        <div class="col-sm-3">
                            <div class="form-group malzemediv" data-id="<?= $f_urunmalzemeid ?>" data-malzemeid="<?= $f_urunmalzemeid?>">
                                <input type="hidden" name="urunmalzemelerid[]" id="urunmalzemelerid" value="0">
                                <input type="text" name="urunmalzemead" autocomplete="off" data-id="0" id="m0" class="form-control text-danger malzemead" value="<?=$f_urunmalzemead?>" aria-invalid="false"><label for="urunmalzemead">Malzeme</label>
                                <ul id="q0"  style="position: absolute; width: 100%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="hidden" name="urunbedenlerid[]" id="urunbedenlerid" value="0">
                                <input type="text" name="urunbedenad" autocomplete="off" id="b0" data-id="0" class="form-control text-danger olcuad" value="<?=$f_urunbedenad?>" aria-invalid="false"><label for="urunolcuad">Ölçü</label>
                                <ul id="sb0"  style="position: absolute; width: 100%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="hidden" name="urunrenklerid[]" id="urunrenklerid" value="0">
                                <input type="text" name="urunrenkad" autocomplete="off" data-id="0"  id="r0" class="form-control text-danger renkad" value="<?=$f_urunrenkad?>"  aria-invalid="false"><label for="urunrenkad">Renk</label>
                                <ul id="sr0"  style="position: absolute; width: 100%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="hidden" name="urunpinlerid[]" id="urunpinlerid" value="0">
                                <input type="text" name="urunpinad" autocomplete="off" data-id="0"  id="p0" class="form-control text-danger pinad" value="<?=$f_urunpinad?>"  aria-invalid="false"><label for="urunpinad">Pin</label>
                                <ul id="sp0"  style="position: absolute; width: 100%; z-index: 2; background-color: rgb(255, 255, 255);"></ul>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input
                                        type="text"
                                        name="urunstokkodu_varyant[]"
                                        id="urunstokkodu_varyant"
                                        class="form-control"
                                        placeholder="STK-AA123"
                                        value="<?=$f_urunstokkodu?>"
                                        required=""
                                        aria-required="true"
                                        aria-invalid="false">
                                <label for="urunstokkodu_varyant" required aria-required="true">Stok Kodu</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" name="urunsatisfiyat_varyant[]" id="urunsatisfiyat_varyant" class="form-control text-danger" placeholder="99.99" value="<?=$f_urunsatisfiyat?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                <label for="urunsatisfiyat_varyant" class="text-danger">Satış Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" name="urunindirimsizfiyat_varyant[]" id="urunindirimsizfiyat_varyant" class="form-control" placeholder="79.99" value="<?=$f_urunindirimsizfiyat?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                <label for="urunindirimsizfiyat_varyant">İnd.Siz Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" name="urunbayifiyat_varyant[]" id="urunbayifiyat_varyant" class="form-control" placeholder="79.99" value="<?=$f_urunbayifiyat?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                <label for="urunbayifiyat_varyant">Bayi Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" name="urunalisfiyat_varyant[]" id="urunalisfiyat_varyant" class="form-control" placeholder="49.99" value="<?=$f_urunalisfiyat?>" data-rule-number="true">
                                <label for="urunalisfiyat_varyant">Alış Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input
                                        type="text"
                                        name="urunstok_varyant[]"
                                        id="urunstok_varyant"
                                        class="form-control"
                                        placeholder="99"
                                        value="<?=$f_urunstok?>"
                                        data-rule-digits="true"
                                        required=""
                                        aria-required="true"
                                        aria-invalid="false">
                                <label for="urunstok_varyant" required aria-required="true">Stok</label>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                unset($urunvaryant_v);
            }
            ?>
        </div>
    </div>
</div>

<style>
    .olcuad_sonuc,.renkad_sonuc,.malzemead_sonuc,.pinad_sonuc{display:none;position:absolute;z-index:2;width:265px;cursor:pointer}
    @media(max-width:1024px){.olcuad_sonuc,.renkad_sonuc,.malzemead_sonuc,.pinad_sonuc{width:auto}}
</style>
