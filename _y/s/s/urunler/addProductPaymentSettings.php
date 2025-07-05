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
								<input name="productCreditCard" id="productCreditCard" type="checkbox" value="1" <?php if($productCreditCard==1)echo 'checked';?>>
								<span>Kredi Kartı</span>
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<div class="checkbox checkbox-styled">
							<label>
								<input name="productBankTransfer" id="productBankTransfer" type="checkbox" value="1" <?php if($productBankTransfer==1)echo 'checked';?>>
								<span>Havale ile Ödeme</span>
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<div class="checkbox checkbox-styled">
							<label>
								<input name="productCashOnDelivery" id="productCashOnDelivery" type="checkbox" value="1" <?php if($productCashOnDelivery==1)echo 'checked';?>>
								<span>Kapıda Ödeme</span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<em class="text-caption">Ürün İçin Ödeme Yöntemleri Seçin</em>
	</div>
</div>