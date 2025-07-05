<?php
/**
 * @var string $productLink
 * @var string $productSeoTitle
 * @var string $productSeoDescription
 * @var string $productSeoKeywords
 * @var string $productUpdateDate
 * @var string $productPriceLastDate
 */

//$productUpdateDate ilk 10 karakteri alınır
$productUpdateDate = substr($productUpdateDate, 0, 10);
?>
<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12"><h4>SEO ÖZELLİKLERİ</h4></div>
    <!-- ÜRÜN FİYAT TARİH BİTİR -->

    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">
            <h4>ÜRÜN GOOGLE ÖZELLİKLERİ</h4><p></p>
            <p>Bu ürünle ilgili "fiyat geçerlilik tarihi"ni girin.<br>Google Merchant Center ürünleri listelerken geçerlilik tarihini göz önüne almaktadır.</p>
        </article>
    </div>
    <div class="col-lg-offset-1 col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <div class="input-daterange input-group" id="demo-date-range">
                        <div class="input-group-content">
                            <input type="text" class="form-control" name="productUpdateDate" id="productUpdateDate" required aria-required="true" value="<?=$productUpdateDate?>" readonly />
                            <label>Ürün güncelleme tarihi</label>
                        </div>
                        <span class="input-group-addon">ile</span>
                        <div class="input-group-content">
                            <input type="text" class="form-control datepicker" name="productPriceLastDate" id="productPriceLastDate" required aria-required="true" value="<?=$productPriceLastDate?>" />
                            <label>Fiyat Geçerlilik Son Tarih</label>
                            <div class="form-control-line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <em class="text-caption">Google ürün aramaları için gerekli</em>
    </div>

    <!-- SEO -->

    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">
            <h4>Google Arama SEO Seçenekleri</h4><p></p>
            <p>Bu alanda mümkün olduğunca sistemin oluşturduğu girdileri kullanın</p>
            <p>SEO Uzmanlığınız varsa alanlara müdahale edebilirsiniz</p>
            <p>Ürün özelliklerinizle ilgili anahtar kelimeleri girmeyi unutmayın.</p>

            <div class="btn-group" id="seoInfoButtonContainer" data-toggle="buttons">
                <label
                        class="btn btn-primary-bright btn-md"
                        data-toggle="modal"
                        data-target="#seoInfoModal"
                        title="SEO Bilgilendirme">
                    <i class="fa fa-info fa-fw"></i>
                    SEO Bilgilendirme
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
                            name="productLink"
                            id="productLink"
                            class="form-control"
                            placeholder="/"
                            value="<?=$productLink?>"
                            data-rule-minlength="5"
                            maxlength="100"
                            aria-invalid="false"
                            required aria-required="true">
                    <label for="productLink">Sayfa Link</label>
                </div>
                <div class="form-group">
                    <input
                            type="text"
                            name="productSeoTitle"
                            id="productSeoTitle"
                            class="form-control"
                            placeholder="Adidas Terrex Swift Solo Erkek Siyah Spor Ayakkabı - D67031"
                            value="<?=$productSeoTitle?>"
                            data-rule-minlength="5"
                            maxlength="65"
                            aria-invalid="false"
                            required aria-required="true">
                    <label for="productSeoTitle">SEO Başlık</label>
                </div>
                <div class="form-group">
                    <textarea
                            id="productSeoDescription"
                            name="productSeoDescription"
                            placeholder="Adidas TERREX Swift Solo D67031 Outdoor Siyah Fitness Erkek Spor Ayakkabı orjinal ürün, ücretsiz kargo ve peşin ödeme indirimi ve kredi kartı taksit seçenekleri ile en uygun fiyata burada"
                            class="form-control"
                            rows="3"
                            data-rule-minlength="25"
                            maxlength="200"
                            aria-invalid="false"
                            required aria-required="true"><?=$productSeoDescription?></textarea>
                    <label for="productSeoDescription">SEO Açıklama</label>
                </div>
                <div class="form-group">
                    <textarea
                            id="productSeoKeywords"
                            name="productSeoKeywords"
                            class="form-control"
                            placeholder="adidas ayakkabı,adidas spor ayakkabı,adidas terrex swift solo,adidas siyah spor ayakkabı,erkek siyah spor ayakkabı"
                            rows="2"
                            data-rule-minlength="6"
                            maxlength="255"
                            aria-invalid="false"
                            required aria-required="true"><?=$productSeoKeywords?></textarea>
                    <label for="productSeoKeywords">SEO Kelimeler</label>
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
                <div class="form-group">
                    <button id="createSeo" type="button" class="btn btn-primary-bright btn-sm">AI Seo Oluşturucu</button>
                </div>

            </div>
        </div>
        <em class="text-caption">Ürün İçeriği/Açıklama</em>
    </div>

</div>
<!-- Seo Bilgilendirme Modalı -->
<div class="modal fade" id="seoInfoModal" tabindex="-1" role="dialog" aria-labelledby="seoInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title" id="seoInfoModalLabel">SEO Bilgilendirme</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <p><strong># SEO Nedir?</strong><br>
                SEO, web sitenizin arama motorlarında daha görünür hale gelmesini sağlamak için yapılan çalışmalardır. Amaç, arama sonuçlarında daha üst sıralarda yer almak ve organik trafiği artırmaktır.
                </p>
                <p><strong># SEO Başlığı Nasıl Olmalı?</strong><br>
                SEO başlığı, sayfanızın arama motoru sonuçlarında görünen başlıktır. Kullanıcıların dikkatini çeker ve sayfanın içeriği hakkında bilgi verir. Başlığınız 50-60 karakter arasında olmalı ve anahtar kelimeleri içermelidir.
                </p>
                <p><strong># SEO Açıklaması Nasıl Olmalı?</strong><br>
                Meta açıklama, sayfanızın arama sonuçlarında görünen kısa özetidir. Bu açıklama, kullanıcıları sayfanıza tıklamaya teşvik eder. 150-160 karakter arasında olmalı, sayfanın içeriğini doğru bir şekilde özetlemeli ve anahtar kelimeleri içermelidir.

                </p>
                <p><strong># Anahtar Kelime Seçimi Nasıl Olmalı?</strong><br>
                Anahtar kelimeler, kullanıcıların arama motorlarında kullanabileceği terimlerdir. Ürün veya hizmetinizle ilgili en önemli ve popüler kelimeleri seçin. Anahtar kelimeleri doğal bir şekilde içeriğe dahil edin ve aşırıya kaçmaktan kaçının.

                </p>
                <p><strong># SEO İçerikleri Nasıl Olmalı?</strong><br>
                İçeriğiniz, hem kullanıcılar hem de arama motorları için değerli olmalıdır. Bilgilendirici, özgün ve kaliteli içerikler oluşturun. Anahtar kelimeleri mantıklı bir şekilde kullanın ve okuyucularınıza fayda sağlayan bilgiler sunun.

                </p>
                <p><strong># URL Yapısı Nasıl Olmalı?</strong><br>
                URL yapınız, kısa, açıklayıcı ve anahtar kelimeleri içermelidir. Örneğin, "www.siteadi.com/urun-adi" gibi basit ve anlaşılır URL'ler tercih edilmelidir. URL'lerde gereksiz karakterlerden kaçının.

                </p>
                <p><strong># Görsel Optimizasyonu Nasıl Olmalı?</strong><br>
                Görseller, sayfa yüklenme hızını ve kullanıcı deneyimini etkiler. Görsellerinizi optimize ederek hızlı yüklenmelerini sağlayın. Ayrıca, görsel alt metinlerinde (alt text) anahtar kelimeleri kullanarak arama motorlarının görsellerinizi anlamasını kolaylaştırın.

                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>