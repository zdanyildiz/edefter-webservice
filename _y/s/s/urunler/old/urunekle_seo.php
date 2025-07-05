<div class="row">
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<h4>Google Arama SEO Seçenekleri</h4><p></p>
            <!-- <p>
                SEO Başlığı; Otomatik Oluşturulur Kullanıcılar google'da nasıl arar?(örn: Adidas Pace VS Erkek Beyaz Spor Ayakkabı) (En fazla 65 karakter)
			</p>-->
            <!-- <p>SEO Açıklaması; Otomatik Oluşturulur ürünü basitçe tanımlayın ve avantajlarını ortaya çıkarın (En fazla 200 karakter)</p> -->
            <p>SEO Link; Sayfa bağlantısıdır. otomatik Oluşturulur.</p>
            <p>SEO Kelimeler; ürünü varyasyonlarını girin (marka ürün,renk ürün,cinsiyet ürün,marka cinsiyet...) (En fazla 255 karakter)</p>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
                <div class="form-group">
                    <input
                            type="text"
                            name="sayfalink"
                            id="sayfalink"
                            class="form-control"
                            placeholder="/"
                            value="<?=$productLink?>"
                            data-rule-minlength="5"
                            maxlength="100"
                            aria-invalid="false"
                            required aria-required="true">
                    <label for="sayfalink">Sayfa Link</label>
                </div>
                <div class="form-group hidden">
					<input 
						type="text" 
						name="seobaslik" 
						id="seobaslik" 
						class="form-control" 
						placeholder="Adidas Terrex Swift Solo Erkek Siyah Spor Ayakkabı - D67031" 
						value="<?=$productSeoTitle?>" 
						data-rule-minlength="5"
						maxlength="65"
						aria-invalid="false"
						required aria-required="true">
					<label for="seobaslik">SEO Başlık</label>
				</div>
				<div class="form-group hidden">
					<textarea 
						id="seoaciklama" 
						name="seoaciklama" 
						placeholder="Adidas TERREX Swift Solo D67031 Outdoor Siyah Fitness Erkek Spor Ayakkabı orjinal ürün, ücretsiz kargo ve peşin ödeme indirimi ve kredi kartı taksit seçenekleri ile en uygun fiyata burada"
						class="form-control"  
						rows="3"
						data-rule-minlength="25"
						maxlength="200"
						aria-invalid="false"
						required aria-required="true"><?=$productSeoDescription?></textarea>
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
						required aria-required="true"><?=$productSeoKeywords?></textarea>
						<label for="seokelime">SEO Kelimeler</label>
				</div>
				<div class="form-group">
					<div class="bootstrap-tagsinput" style="display:inline-block;position: relative;">
						<?php

                        if(!empty($seoKeywords))
                        {
                            foreach($seoKeywords as $keywords)
                            {
                                echo '<span 
                                    class="tag label label-info" 
                                    data-key="'.$keywords.'" style="white-space:normal;font-size:85%">'.$keywords.'
                                </span>';
                            }
                        }
						?>
					</div>
					<label>Diğer ürün Kelimeler</label>
				</div>
			</div>
		</div>
		<em class="text-caption">Ürün İçeriği/Açıklama</em>
	</div>
</div>