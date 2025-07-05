<div class="row">
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<h4>ÜRÜN ÖDEME ÖZELLİKLERİ</h4><p></p>
			<p>Bu ürünle ilgili ödeme yöntemlerini seçin</p>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
				<div class="col-sm-3">
					<div class="form-group">
						<div class="checkbox checkbox-styled">
							<label>
								<input name="urunkredikarti" id="urunkredikarti" type="checkbox" value="1" <?php if(S($f_urunkredikarti)==1)echo 'checked';?>>
								<span>Kredi Kartı</span>
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<div class="checkbox checkbox-styled">
							<label>
								<input name="urunhavaleodeme" id="urunhavaleodeme" type="checkbox" value="1" <?php if(S($f_urunhavaleodeme)==1)echo 'checked';?>>
								<span>Havale ile Ödeme</span>
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<div class="checkbox checkbox-styled">
							<label>
								<input name="urunkapidaodeme" id="urunkapidaodeme" type="checkbox" value="1" <?php if(S($f_urunkapidaodeme)==1)echo 'checked';?>>
								<span>Kapıda Ödeme</span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<em class="text-caption">Ödeme Yöntemleri Seçin</em>
	</div>
</div>