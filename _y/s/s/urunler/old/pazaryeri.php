<!-- PAZARYERİ -->
<div class="tab-pane" id="tab_pazaryeri">
    <div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
        <div class="col-lg-12"><h4>PAZAR YERLERİ</h4></div>
        <div class="col-lg-3 col-md-4">
            <p>Bu ürünü pazar yerlerinizde yayınlayabilirsiniz</p><p>Ürünü eklemek/güncellemek istediğiniz pazar yerlerini seçin</p>
        </div>
        <div class="col-lg-offset-1 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <?php foreach($_SESSION["pazaryeri"] as $pazaryeri)
                        {
                            $pazaryeriad=$pazaryeri["pazaryeriad"];
                            $pazaryeriid=$pazaryeri["pazaryeriid"];

                            ?>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="checkbox checkbox-styled">
                                        <label>
                                            <input name="pazaryeri[]" id="pazaryeri_<?=$pazaryeriad?>" type="checkbox" value="<?=$pazaryeriad?>" <?php if(q("from")==$pazaryeriad)echo 'checked';?>>
                                            <span><?=$pazaryeriad?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>