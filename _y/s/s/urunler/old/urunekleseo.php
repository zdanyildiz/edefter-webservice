<div class="row">
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<h4>Google Arama SEO Seçenekleri</h4><p></p>
			<p>
				SEO Başlığı Kullanıcılar google'da nasıl arar?(örn: Adidas Pace VS Erkek Beyaz Spor Ayakkabı) (En fazla 65 karakter)
			</p>
			<p>SEO Açıklaması; ürünü basitçe tanımlayın ve avantajlarını ortaya çıkarın (En fazla 200 karakter)</p>
			<p>SEO Kelimeler; ürünü varyasyonlarını girin (marka ürün,renk ürün,cinsiyet ürün,marka cinsiyet...) (En fazla 255 karakter)</p>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
				<div class="form-group">
					<input 
						type="text" 
						name="seobaslik" 
						id="seobaslik" 
						class="form-control" 
						placeholder="Adidas Terrex Swift Solo Erkek Siyah Spor Ayakkabı - D67031" 
						value="<?=$f_seobaslik?>" 
						data-rule-minlength="5"
						maxlength="65"
						aria-invalid="false"
						required aria-required="true">
					<label for="seobaslik">SEO Başlık</label>
				</div>
				<div class="form-group">
					<textarea 
						id="seoaciklama" 
						name="seoaciklama" 
						placeholder="Adidas TERREX Swift Solo D67031 Outdoor Siyah Fitness Erkek Spor Ayakkabı orjinal ürün, ücretsiz kargo ve peşin ödeme indirimi ve kredi kartı taksit seçenekleri ile en uygun fiyata burada"
						class="form-control"  
						rows="3"
						data-rule-minlength="25"
						maxlength="255"
						aria-invalid="false"
						required aria-required="true"><?=$f_seoaciklama?></textarea>
						<label for="seoaciklama">SEO Açıklama</label>
				</div>
				<div class="form-group">
					<textarea 
						id="seokelime" 
						name="seokelime"
						class="form-control" 
						placeholder="adidas ayakkabı,adidas spor ayakkabı,adidas terrex swift solo,adidas siyah spor ayakkabı,erkek siyah spor ayakkabı" 
						rows="2"
						data-rule-minlength="6"
						maxlength="255"
						aria-invalid="false"
						required aria-required="true"><?=$f_seokelime?></textarea>
						<label for="seokelime">SEO Kelimeler</label>
				</div>
				<div class="form-group">
					<div class="bootstrap-tagsinput" style="display:inline-block;position: relative;">
						<?php
						$kelimeler_s="
							Select 
								kelime 
							From 
								seo 
									inner join sayfa on 
										sayfa.benzersizid=seo.benzersizid 
										inner join sayfalistekategori on 
											sayfalistekategori.sayfaid=sayfa.sayfaid 
											inner join kategori on 
												kategori.kategoriid=sayfalistekategori.kategoriid
							Where sayfalistekategori.kategoriid='".$f_kategoriid."'

						";
						$seokelimeler="";
						if($db->select($kelimeler_s))
						{
							$kelimeler_v=$db->select($kelimeler_s);
							if($kelimeler_v)
							{															
								foreach ($kelimeler_v as $kelimeler_t)
								{
									$kelimesatir=$kelimeler_t["kelime"];
									$kelimesatir=ltrim($kelimesatir,",");
									$kelimesatir=rtrim($kelimesatir,",");
									$kelimesatir=str_replace(",,", ",", $kelimesatir);
									if(!BosMu($kelimesatir))
									{
										$seotumkelimeler = explode(",", $kelimesatir);
										foreach($seotumkelimeler as $kelime)
										{
											if(strpos($seokelimeler, $kelime)===false)
											{
												echo '<span 
													class="tag label label-info" 
													data-key="'.$kelime.'" style="white-space:normal;font-size:85%">'.$kelime.'
												</span>';
											}
										}
										$seokelimeler=$seokelimeler.",".$kelimesatir;
									}
								}
							}
						}

						/*if(!BosMu($seokelimeler))
						{
							$seokelimeler=ltrim($seokelimeler,",");
							$seokelimeler=rtrim($seokelimeler,",");
							$seokelimeler=str_replace(",,", ",", $seokelimeler);
							$seotumkelimeler = explode(",", $seokelimeler);
							foreach($seotumkelimeler as $kelime)
							{
								echo '<span 
										class="tag label label-info" 
										data-key="'.$kelime.'" style="white-space:normal;font-size:85%">'.$kelime.'
									</span>';
							}
						}*/
						?>
					</div>
					<label>Diğer ürün Kelimeler</label>
				</div>
			</div>
		</div>
		<em class="text-caption">Ürün İçeriği/Açıklama</em>
	</div>
</div>