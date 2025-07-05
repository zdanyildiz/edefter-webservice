<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12">
        <h4>Ürün İçeriği</h4>
    </div>
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<p>
				Ürün adı girin! (örn: Adidas Pace VS Erkek Beyaz Spor Ayakkabı)
			</p>
			<p>Ürün içeriğini/özelliklerini girin</p>

            <!-- Bilgilendirme Modalı için Button-->
            <div class="btn-group" id="contentHelpButtonContainer" data-toggle="buttons">
                <label
                        class="btn btn-primary-bright btn-md"
                        data-toggle="modal"
                        data-target="#productContentHelpModal"
                        title="Ürün İçeriği ve Görselleri Hakkında Bilgi">
                    <i class="fa fa-info fa-fw"></i>
                    Bilgilendirme
                </label>
            </div>
            <div class="btn-group" id="contentCreateButtonContainer" data-toggle="buttons" style="margin-top:30px">
                <label
                        class="btn btn-primary btn-md"
                        data-toggle="modal"
                        data-target="#productContentCreateModal"
                        title="Yapay Zeka ile Ürün İçeriği Üretin">
                    <i class="fa fa-connectdevelop fa-fw"></i>
                    AI İçerik Üretici
                </label>
            </div>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
				<div class="form-group">
					<input 
						type="text" 
						name="productName"
						id="productName"
						class="form-control" 
						placeholder="Örn:ÜRÜN BAŞLIĞINI GİRİN" 
						value="<?=$productName?>"
						required aria-required="true">
					<label for="productNam">Ürün Adı</label>
                    <input type="hidden" class="hidden" name="productOrder" id="productOrder" value="<?=$productOrder?>">
				</div>
                <div class="form-group">
                    <textarea name="productDescription" id="productDescription" class="form-control" rows="1" placeholder="Ücretsiz Kargo - Gününde Gönderim - Faturalı - Üreticiden gönderi"><?=$productDescription?></textarea>
                    <label for="productDescription">Ürün Alt Başlık</label>
                </div>
                <div class="form-group">
                    <textarea name="productShortDesc" id="productShortDesc" class="form-control" rows="1" placeholder="100% yerli malı stoklarla sınırlı"><?=$productShortDesc?></textarea>
                    <label for="productShortDesc">Ürün Alt Başlık 2</label>
                </div>
				<div class="form-group no-padding">
					<textarea 
						id="productContent"
						name="productContent"
						rows="40" 
						style="height: 500px"><?=$productContent?></textarea>
				</div>
                <div class="btn-group" id="contentImageButtonContainer" data-toggle="buttons">
                    <label
                            class="btn btn-primary-bright btn-md"
                            href="#offcanvas-imageUpload"
                            id="uploadImageByLeftCanvas"
                            data-target="productContent"
                            data-uploadtarget="Product"
                            data-toggle="offcanvas"
                            title="Yeni Resim Yükle">
                        <i class="fa fa-plus fa-fw"></i>
                        İçeriğe Resim Yükle
                    </label>

                    <label
                            class="btn btn-default-light btn-md"
                            href="#offcanvas-imageSearch"
                            id="selectImageByRightCanvas"
                            data-target="productContent"
                            data-toggle="offcanvas"
                            title="Listeden Resim Seç">
                        <i class="fa fa-file-image-o fa-fw"></i>
                        İçeriğe Resim Seç
                    </label>
                </div>
			</div>
		</div>
		<em class="text-caption">Ürün İçeriği/Açıklama</em>
	</div>
</div>
<!-- ürün başlık, içerik ve görsellerin seo ve kullanıcı deneyimine etkisini açıklayan yardım modalını ekleyelim -->
<div class="modal fade" id="productContentHelpModal" tabindex="-1" role="dialog" aria-labelledby="productContentHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="productContentHelpModalLabel">Ürün İçeriği ve Görselleri Hakkında Bilgi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong># Ürün Başlığı Nasıl Olmalı?</strong><br>
                Ürün başlığı, ürününüzün ne olduğunu açıkça belirtmelidir. Anahtar kelimeleri içermeli ve 50-60 karakter arasında olmalıdır. Başlığınız, arama motorlarında ürününüzü kolayca bulunabilir hale getirir ve kullanıcıların dikkatini çeker.
                **Etkisi:** Doğru başlıklar, arama motoru sonuçlarında daha üst sıralarda yer almanızı sağlar ve kullanıcıların ürünü tıklama olasılığını artırır.

                </p>
                <p><strong># Ürün İçeriği Nasıl Olmalı?</strong><br>
                 Ürün içeriği, kullanıcıların ürün hakkında detaylı bilgi almasını sağlar. Açıklamalar, özgün ve bilgilendirici olmalı, ürünün özelliklerini ve faydalarını net bir şekilde belirtmelidir. Anahtar kelimeleri doğal bir şekilde kullanarak SEO'ya uygun hale getirin.
                **Etkisi:** Kaliteli ve bilgilendirici içerikler, kullanıcıların satın alma kararını etkiler ve arama motorlarında daha iyi sıralamalara sahip olmanızı sağlar.

                </p>
                <p><strong># Ürün Görselleri Nasıl Olmalı?</strong><br>
                Ürün görselleri, yüksek çözünürlüklü ve profesyonel olmalıdır. Ürünün farklı açılardan fotoğraflarını ekleyin ve görsellerin alt metinlerinde (alt text) anahtar kelimeleri kullanın. Görsellerin boyutlarını optimize ederek hızlı yüklenmelerini sağlayın.
                **Etkisi:** Kaliteli görseller, kullanıcıların ürünü daha iyi anlamasını ve satın alma olasılığını artırır. Ayrıca, optimize edilmiş görseller, sayfa yüklenme hızını artırarak SEO'ya katkıda bulunur.

                </p>
                <p><strong># Ürün Başlığı, İçeriği ve Görsellerinin Aramaya ve Kullanıcı Deneyimine Etkisi</strong><br>
                Doğru başlıklar, optimize edilmiş içerikler ve görseller, arama motorlarının sitenizi daha iyi anlamasını sağlar ve ürün sayfalarınızın arama sonuçlarında üst sıralarda yer almasına yardımcı olur. Bu, organik trafiğinizi artırır.

                </p>
                <p><strong>Kullanıcı Deneyimine Etkisi (Impact on User Experience)</strong><br>
                Açık, bilgilendirici ve kaliteli içerikler, kullanıcıların site içinde daha fazla zaman geçirmesini sağlar. Kullanıcı dostu başlıklar ve çekici görseller, kullanıcıların ürüne olan ilgisini artırır ve satın alma kararını olumlu yönde etkiler.
                </p>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="productContentCreateModal" tabindex="-1" role="dialog" aria-labelledby="productContentCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button id="btn-popup-alert-close" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="productContentCreateModalLabel">Yapay Zeka ile Ürün İçeriği Üretin</h4>
            </div>
            <div class="modal-body">

                <p><strong>Ürün içeriği üretmek için, ürünün özelliklerini ve amacını kısa ve net bir şekilde belirtin. Ürünü kimlerin kullanabileceğini, hangi özelliklerinin öne çıktığını ve kullanıcıya nasıl fayda sağlayacağını ifade eden birkaç cümle yazın.</strong></p>
                <div class="card-body">
                    <strong>Örnek Metin</strong><br>
                    <p><strong>Örnek 1:</strong> "Yüksek kaliteli pamuk kumaştan üretilmiş rahat bir erkek tişört. Günlük kullanım için idealdir, yumuşak dokusu ve dayanıklılığı ile öne çıkar."</p>

                    <p><strong>Örnek 2:</strong> "Su geçirmez özellikli, nefes alabilir kadın mont. Soğuk hava koşullarında bile sıcak tutan, hafif ve şık bir tasarıma sahip."</p>

                    <p><strong>Örnek 3:</strong> "Çocuklar için özel tasarlanmış, renkli ve ergonomik su matarası. BPA içermeyen yapısı ve kolay taşınabilir özelliği ile çocuklar için güvenli bir seçenek."</p>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea class="form-control" id="productInf" name="productInf" rows="3" placeholder="Ürün cümlenizi yazın" style="
                            background-color:#efefef;
                            width:96%;
                            padding: 10px 1% 10px 1%;
                            margin:10px 0 0 0;
                            border:solid 1px #eee"
                        ></textarea>
                        <label for="productInf">Ürün Cümlesi</label>
                    </div>
                </div>
            </div>
            <br>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="productContentCreateButton">Ürün İçeriği Üret</button>
            </div>
        </div>
    </div>
</div>