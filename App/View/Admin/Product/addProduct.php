<?php
/**
 * @var AdminSession $adminSession
 * @var Database $db
 * @var Router $router
 */

$requestData = $router->requestData;
$seoTitle = $router->seoTitle;

$productList = $adminSession->getSession("productList");
$totalProducts = $productList["totalProducts"];
$totalPages = $productList["totalPages"];
$productList = $productList["products"];
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?=$seoTitle?></h3>
    </div>
    <div class="card-body">
        <div class="card-pay">
            <div class="tabs-menu-boxed">
                <ul class="nav panel-tabs">
                    <li><a href="#category" class="active" data-bs-toggle="tab"> Kategori</a></li>
                    <li><a href="#content" data-bs-toggle="tab" class="">  İçerik</a></li>
                    <li><a href="#media" data-bs-toggle="tab" class="">  Medya</a></li>
                    <li><a href="#price-settings" data-bs-toggle="tab" class="">  Fiyat</a></li>
                    <li><a href="#variant" data-bs-toggle="tab" class="">  Varyant</a></li>
                    <li><a href="#features" data-bs-toggle="tab" class="">  Ek Özellik</a></li>
                    <li><a href="#showcase" data-bs-toggle="tab" class="">  Vitrin</a></li>
                    <li><a href="#payment" data-bs-toggle="tab" class="">  Ödeme</a></li>
                    <li><a href="#cargo" data-bs-toggle="tab" class="">  Kargo</a></li>
                    <li><a href="#seo" data-bs-toggle="tab" class="">  SEO</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active show" id="category">
                    <div class="card">
                        <div class="card-header">
                            <h4>Kategori - Marka - Tedarikçi - Ürün Model</h4>
                        </div>
                        <div class="card-body col-md-12" style="display: flex; flex-direction: row; align-items: flex-start;">
                            <div class="col-md-4 bg-danger-transparent card-body">
                                <article class="margin-bottom-xxl">
                                    <p>
                                        Gireceğiniz ürün bir Kategoriye ait olmalıdır. (örn: Ürünler/Cep Telefonu)
                                    </p>
                                    <p>
                                        Bir Marka seçmelisiniz. Lütfen Marka seçin! (örn: Samsung, Apple)
                                    </p>
                                    <p>
                                        Gruba tanımladığınız bilgiler (kdv, açıklama, parabirimi) otomatik alınır ve girişleri kolaylaştırır. (ayakkabılar, usb bellekler) <a href="/_y/s/s/gruplar/grupekle.php" class="primary">Buradan grup tanımlayabilirsiniz</a>
                                    </p>
                                    <p>Yayın alanından Pasif konumunu seçerseniz ürün kaydedilir fakat siz aktif edene kadar görüntülenmez.</p>
                                </article>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row" id="kategoridivler">
                                            <input type="hidden" id="kategoriid" name="kategoriid" value="">
                                            <!-- KATEGORİ -->
                                            <div id="kategoridiv0" class="col-sm-6 form-group floating-label">
                                                <select>
                                                    <option value="0">Önce Ürün Kategorisi Ekleyin </option>';
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- TEDARİKÇİ -->
                                            <div class="col-sm-4">
                                                <select id="tedarikciid" name="tedarikciid" class="form-control">

                                                </select>
                                                <label for="urungrupid">Tedarikçi Seçin</label>
                                            </div>
                                            <!-- MARKA -->
                                            <div class="col-sm-4">
                                                <select id="markaid" name="markaid" class="form-control">

                                                </select>
                                                <label for="markaid">Marka Seçin</label>
                                            </div>
                                            <!-- GRUP -->
                                            <div class="col-sm-4">
                                                <select id="urungrupid" name="urungrupid" class="form-control">
                                                    <option value="0">Ürün grubu Seçin</option>
                                                </select>
                                                <label for="urungrupid">Ürün Grubu Seçin</label>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-sm-2"><div class="form-group">Yayınlansın mı</div></div>
                                            <div class="col-sm-4">
                                                <div style="padding-top: 10px">
                                                    <label class="radio-inline radio-styled">
                                                        <input type="radio" name="sayfaaktif" value="1" ><span>Aktif</span>
                                                    </label>
                                                    <label class="radio-inline radio-styled">
                                                        <input type="radio" name="sayfaaktif" value="0" ><span>Pasif</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="urunmodel" id="urunmodel" required aria-required="true" value="" />
                                                    <label for="urunmodel">Ürün Model</label><i id="modelkopyala" class="md-content-copy"></i>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <em class="text-caption">Temel özellikleri seçin</em>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane" id="content">
                    <div class="card-header">
                        <h4>Ürün İçeriği</h4>
                    </div>
                    <div class="card-body col-md-12" style="display: flex; flex-direction: row; align-items: flex-start;">
                        <div class="col-md-4 bg-danger-transparent card-body">
                            <article class="margin-bottom-xxl">
                                <p>
                                    Ürün adı girin! (örn: Adidas Pace VS Erkek Beyaz Spor Ayakkabı)
                                </p>
                                <br>
                                <p>Ürün içeriğini/özelliklerini girin</p>
                            </article>
                        </div>
                        <div class="col-lg-offset-1 col-md-8">

                            <div class="form-group">
                                <input
                                        type="text"
                                        name="sayfaad"
                                        id="sayfaad"
                                        class="form-control"
                                        placeholder="Örn:ÜRÜN BAŞLIĞINI GİRİN"
                                        value=""
                                        required aria-required="true">
                                <label for="sayfaad">Ürün Adı</label>
                            </div>
                            <div class="form-group no-padding">
                                <div id="summernote">
                                    <p>Hello Summernote</p>
                                </div>
                            </div>
                            <style>
                                .tab-content .btn i{color:#000}
                                label {
                                    color: #ccc;
                                    padding: 5px;
                                }
                            </style>

                            <em class="text-caption">Ürün İçeriği/Açıklama</em>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="media">
                    <div class="row">
                        <label class="col-md-3 form-label mb-4 text-primary bg-danger-transparent card-body">Görsel Yükle ['jpg', 'png', 'jpeg', 'webp'] :</label>
                        <div class="col-md-9">
                            <input id="productImages" type="file" name="productImages" accept=".jpg, .png, .webp, image/jpeg, image/png, image/webp" multiple>
                        </div>
                    </div>
                    <div class="row" style="margin: 25px 0"></div>
                    <div class="row">
                        <label class="col-md-3 form-label mb-4 text-primary bg-danger-transparent card-body">Dosya Yükle ['jpg', 'png', 'jpeg', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'] :</label>
                        <div class="col-md-9">
                            <input id="productFiles" type="file" name="productFiles" accept=".jpg, .png, .webp, .pdf, .doc, .xls, .xlsx, .ppt, .pptx, .txt, .zip, .rar, image/jpeg, image/png, image/webp" multiple>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="price-settings">

                </div>
                <div class="tab-pane" id="variant">

                </div>
                <div class="tab-pane" id="features">

                </div>
                <div class="tab-pane" id="showcase">

                </div>
                <div class="tab-pane" id="payment">

                </div>
                <div class="tab-pane" id="cargo">

                </div>
                <div class="tab-pane" id="seo">

                </div>
            </div>
        </div>
    </div>
</div>