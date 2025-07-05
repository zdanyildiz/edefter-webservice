<div class="row">
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<h4>ÜRÜN ÖZELLİKLERİ</h4><p></p>
			<p>Bu alan ürün sayfasında kısa açıklama bölümünde görünür. Dilerseniz ürün grubuna bir metin ekleyerek bu gruba seçtiğiniz tüm ürünlerde aynı açıklamanın görünmesini sağlayabilirsiniz.</p>
			<p>Örneğin ücretsiz montaj.</p>
			<p>Kargo Teslimat Süresi (Gün)</p>
			<p>Sabit Kargo Ücreti (Ürün Fiyatına Eklenir)</p>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
				<div class="form-group">
					<textarea name="urunaciklama" id="urunaciklama" class="form-control" rows="2" placeholder="Alacağınız bu ürün %100 orjinal olup, orjinal kutusunda ve faturasıyla anlaşmalı kargo firmamız tarafından size teslim edilecektir."><?=$f_urunaciklama?></textarea>
					<label for="urunaciklama">Ürün Kısa Açıklama</label>
				</div>
				<div class="form-group">
					<textarea name="urunhediye" id="urunhediye" class="form-control" rows="2" placeholder="Bu ürünü satın alan müşterilere sağlanacak avantajları yazabilirsiniz"><?=$f_urunhediye?></textarea>
					<label for="urunhediye">Ürün Hediye/Promosyon açıklaması</label>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<input type="text" name="urundesi" id="urundesi" class="form-control" placeholder="3" value="<?=$f_urundesi?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
						<label for="urundesi">Ürün Desi</label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<input type="text" name="urunkargosuresi" id="urunkargosuresi" class="form-control" placeholder="3" value="<?=$f_urunkargosuresi?>" data-rule-digits="true" required="" aria-required="true" aria-invalid="false">
						<label for="urunkargosuresi">Kargo Süresi (Gün)</label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<input type="text" name="urunsabitkargoucreti" id="urunsabitkargoucreti" class="form-control" placeholder="4.50" value="<?=$f_urunsabitkargoucreti?>"data-rule-number="true" required="" aria-required="true" aria-invalid="false">
						<label for="urunsabitkargoucreti">Sabit Kargo Ücreti</label>
					</div>
				</div>
			</div>
		</div>
		<em class="text-caption">Ürün İçeriği/Açıklama</em>
	</div>
</div>
