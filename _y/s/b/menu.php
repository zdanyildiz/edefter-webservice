<?php
/**
 * @var int $adminAuth
 * @var int $sessionLifetime
 * @var int $adminAuth
 * @var Database $db
 * @var array $checkGeneralSettings
*/
$siteType = 0;
$isMemberRegistration =1;
if(!empty($checkGeneralSettings)){
    $siteType = $checkGeneralSettings[0]["sitetip"];
    $isMemberRegistration = $checkGeneralSettings[0]["uyelik"];
    //print_r($checkGeneralSettings);
}

include_once MODEL."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();
?>

<div id="menubar">
    <div class="menubar-fixed-panel">
        <div>
            <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <div class="expanded">
            <a href="/_y/">
                <span class="text-lg text-bold text-primary">Admin</span>
            </a>
        </div>
    </div>
    <div class="menubar-scroll-panel">
        <ul id="main-menu" class="gui-controls">
            <?php if(count($languages)>1):?>
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-flag"></i></div>
                    <span class="title">ÖNCELİKLİ DİL AYARLAMA</span>
                </a>
                <ul>
                <?php foreach ($languages as $language): ?>
                    <li>
                        <a href="/App/Controller/Admin/AdminLanguageController.php?action=setLanguage&languageID=<?=$language['languageID']?>&referrer=<?=$_SERVER['REQUEST_URI']?>" id="lang<?=$language['languageCode']?>">
                            Çalışma Dili: <?=$language['languageName']?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </li>
            <?php endif;?>
            <!-- Anasayfa -->
            <li>
                <a href="/_y/" id="home">
                    <div class="gui-icon"><i class="md md-home"></i></div>
                    <span class="title">ANA EKRAN</span>
                </a>
            </li>
            <!-- /Anasayfa -->

            <!-- Ürünler -->
            <?php if($siteType==1):?>
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-stars"></i></div>
                    <span class="title">ÜRÜNLER</span>
                </a>
                <ul>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Ürünler</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/urunler/AddProduct.php" id="addProductphp"><span class="title">Ürün Ekle</span></a></li>
                            <li><a href="/_y/s/s/urunler/ProductList.php" id="productListphp"><span class="title">Ürün Liste</span></a></li>
                            <li><a href="/_y/s/s/urunler/DownloadProductList.php" id="downloadProductListphp"><span class="title">Toplu İşlemler</span></a></li>
                            <li><a href="/_y/s/s/urunler/ProductTransfer.php" id="productTransferphp"><span class="title">Ürün Aktar</span></a></li>
                            <!-- li><a href="/_y/s/s/urunler/silinenurunliste.php" id="silinenurunlistephp"><span class="title">Silinen Ürün Liste</span></a></li>
                            <li><a href="/_y/s/s/urunler/uruniliski.php" id="uruniliskiphp"><span class="title">İlişkili Ürünler</span></a></li>
                            <li><a href="/_y/s/s/urunler/excel-urun-yukle.php" id="topluurunyuklephp"><span class="title">Toplu Ürün Yükle</span></a></li>
                            <li><a href="/_y/s/s/urunler/excel-urun-fiyatguncelle.php" id="topluurunguncellephp"><span class="title">Toplu Fiyat Güncelle</span></a></li>
                            <li><a href="/_y/s/s/urunler/excel-urun-fiyatguncelle-en.php" id="topluurunguncelleenphp"><span class="title">Yurtdışı Toplu Fiyat Güncelle</span></a></li -->

                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Ürün Kategorileri</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/urunler/AddProductCategory.php" id="addProductCategoryphp"><span class="title">Ürün Kategori Ekle</span></a></li>
                            <li><a href="/_y/s/s/urunler/ProductCategoryList.php" id="productCategoryListphp"><span class="title">Ürün Kategori Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Ürün Grupları</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/gruplar/AddProductGroup.php" id="addProductGroupphp" ><span class="title">Ürün Grubu Ekle</span></a></li>
                            <li><a href="/_y/s/s/gruplar/ProductGroupList.php" id="productGroupListphp" ><span class="title">Ürün Grubu Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Varyant - Seçenekler</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/varyasyonlar/VariantGroupList.php" id="variantGroupListphp" ><span class="title">Varyant Grupları</span></a></li>
                            <li><a href="/_y/s/s/varyasyonlar/AddVariantGroup.php" id="addVariantGroupphp" ><span class="title">Varyant Grubu Ekle</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Markalar</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/markalar/AddBrand.php" id="addBrandphp" ><span class="title">Marka Ekle</span></a></li>
                            <li><a href="/_y/s/s/markalar/BrandList.php" id="brandListphp" ><span class="title">Marka Liste</span></a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php endif;?>
            <!-- /Ürünler -->

            <!-- SİPARİŞLER -->
            <?php if($siteType==1):?>
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-shopping-cart"></i></div>
                    <span class="title">SİPARİŞLER</span>
                </a>
                <ul>
                    <li><a href="/_y/s/s/siparisler/CreateOrder.php" id="navCreateOrder"><span class="title">Sipariş Oluştur</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=100" id="navAllOrders"><span class="title">Sipariş Tümü</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=99" id="navOrdersByCreditCard"><span class="title">Sipariş Kredi K.</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=98" id="navOrderByEft"><span class="title">Sipariş Banka H.</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=97" id="navOrdersByPaymentAtTheDoor"><span class="title">Sipariş kapıda Ö.</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=2" id="navPreparedOrders"><span class="title">Sipariş Hazırlanıyor</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=9" id="navSupplyExpectedOrders"><span class="title">Tedarik Beklenenler</span></a></li>

                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=0" id="navOrdersReadyToShip"><span class="title">Kargoya Hazır</span></a></li><li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=3" id="navOrdersShipped"><span class="title">Kargoya Verilenler</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=4" id="navDeliveredorders"><span class="title">Teslim Edilenler</span></a></li>

                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=10" id="navReturnedOrders"><span class="title">İade Edilenler</span></a></li>
                    <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=11" id="navCanceledOrders"><span class="title">İptal Edilenler</span></a></li>

                    <!-- li><a href="/_y/s/s/iptaliadedegisim/degisim.php" id="degisimphp"><span class="title">Değişim Talebi <label class="bildirim"></label></span></a></li>
                    <li><a href="/_y/s/s/iptaliadedegisim/iade.php" id="iadephp"><span class="title">İade Talebi <label class="bildirim"></label></span></a></li>
                    <li><a href="/_y/s/s/iptaliadedegisim/iptal.php" id="iptalphp"><span class="title">İptal Talebi <label class="bildirim"></label></span></a></li -->
                </ul>
            </li>
            <?php endif;?>
            <!-- /SİPARİŞLER -->
            <!-- Üyeler -->
            <?php if($siteType==1 || $isMemberRegistration==1):?>
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="fa fa-user"></i></div>
                    <span class="title">ÜYELER</span>
                </a>
                <ul>
                    <li><a href="/_y/s/s/uyeler/AddMember.php" id="addMemberphp"><span class="title">Üye Ekle</span></a></li>
                    <li><a href="/_y/s/s/uyeler/MemberList.php" id="memberListphp"><span class="title">Üye Liste</span></a></li>
                    <li><a href="/_y/" ><span class="title">Üye Sepet</span></a></li>
                </ul>
            </li>
            <?php endif;?>
            <!-- /ÜYELER -->
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="fa fa-mail-reply"></i></div>
                    <span class="title">FORMLAR</span>
                </a>
                <ul>
                    <li><a href="/_y/s/s/formiletisim/ContactFormList.php" id="contactFormListphp" ><span class="title">İletişim Form Mesajları</span></a></li>
                    <li><a href="/_y/s/s/formiletisim/NewsletterFormList.php" id="newsletterFormListphp"><span class="title">E-Bülten Kayıtları </span></a></li>
                    <!--li><a href="/_y/s/s/uyeler/sorusor.php" id="sorusorphp"><span class="title">Üye Mesajları </span></a></li -->
                </ul>
            </li>
            <!-- Kampanyalar -->
            <?php if($siteType==1):?>
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="fa fa-gift"></i></div>
                    <span class="title">KAMPANYALAR</span>
                </a>
                <ul>
                    <li><a href="/_y/s/s/kampanyalar/AddCampaign.php" id="addCampaignphp">Kampanya Ekle</a></li>
                    <li><a href="/_y/s/s/kampanyalar/CampaignList.php" id="campaignListphp">Kampanyalar</a></li>
                    <?php
                    /**
                     * @todo paket_indirim düzenle yapılacak, sadece indirim oranı girilecek
                     * kampanyalı ürünler sayfası yapılacak, kampanya seçilecek, kategori, marka, tedarikçi ya da ürünler seçilebilecek
                     * */
                    ?>
                </ul>
            </li>
            <?php endif;?>
            <!-- /Kampanyalar -->

            <!-- Site İçerik -->
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-laptop-chromebook"></i></div>
                    <span class="title">SİTE İÇERİĞİ</span>
                </a>
                <ul>
                    <!-- Diller -->
                    <li class="gui-folder">
                        <a>
                            <span class="title">Diller</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/diller/AddLanguage.php" id="addLanguagephp"><span class="title">Dil Ekle</span></a></li>
                            <li><a href="/_y/s/s/diller/LanguageList.php" id="languageListphp"><span class="title">Dilller</span></a></li>
                            <li><a href="/_y/s/s/diller/AddLanguageConstant.php" id="addLanguageConstantphp"><span class="title">Dil Sabitleri</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Menü Yerlesimi</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/menuler/Menus.php?menuLocation=0" id="menusTopphp"><span class="title">Tepe Menü</span></a></li>
                            <li><a href="/_y/s/s/menuler/Menus.php?menuLocation=1" id="menusMainphp" ><span class="title">Ana Menü</span></a></li>
                            <!-- li><a href="/_y/s/s/menuler/Menus.php?menuLocation=2" id="menusLeftphp"><span class="title">Sol Menü</span></a></li>
                            <li><a href="/_y/s/s/menuler/Menus.php?menuLocation=3" id="menusRightphp"><span class="title">Sağ Menü</span></a></li -->
                            <li><a href="/_y/s/s/menuler/Menus.php?menuLocation=4" id="menusBottomphp" ><span class="title">Alt Menü</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Kategoriler</span>
                        </a>

                        <ul>
                            <li><a href="/_y/s/s/kategoriler/AddCategory.php" id="addCategoryphp"><span class="title">Kategori Ekle</span></a></li>
                            <li><a href="/_y/s/s/kategoriler/CategoryList.php" id="categoryListphp" ><span class="title">Kategori Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Sayfalar</span>
                        </a>

                        <ul>
                            <li><a href="/_y/s/s/sayfalar/AddPage.php" id="addPagephp"><span class="title">Sayfa Ekle</span></a></li>
                            <li><a href="/_y/s/s/sayfalar/PageList.php" id="pageListphp"><span class="title">Aktif Sayfalar</span></a></li>
                        </ul>

                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Slayt / Banner Yönetimi</span>
                        </a>
                        <ul>
                            <li>
                                <a href="/_y/s/s/banners/AddBanner.php" id="addBannerphp"><span class="title">Banner Ekle</span></a>
                            </li>
                            <li>
                                <a href="/_y/s/s/banners/ListBanner.php" id="listBannerphp"><span class="title">Banner Liste</span></a>
                            </li>
                            <!--
                            <li class="gui-folder">
                                <a>
                                    <span class="title">Kategoriler</span>
                                </a>
                                <ul>
                                    <li><a href="/_y/s/s/tasarim/AddSlide.php" id="addSlidephp"><span class="title">Slayt Düzenle</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddTopBanner.php" id="addTopBannerphp"><span class="title">Tepe Banner</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddMiddleBanner.php" id="addMiddleBannerphp"><span class="title">Orta Banner</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddBottomBanner.php" id="addBottomBannerphp"><span class="title">En Alt Banner</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddCarousel.php" id="addCarouselphp"><span class="title">Carousel Slayt</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddHeaderBanner.php" id="addHeaderBannerphp"><span class="title">Header Banner</span></a></li>
                                </ul>
                            </li>
                            <li class="gui-folder">
                                <a>
                                    <span class="title">Sayfalar</span>
                                </a>
                                <ul>
                                    <li><a href="/_y/s/s/tasarim/AddSlideForPage.php" id="addSlideForPagephp"><span class="title">Slayt Düzenle</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddTopBannerForPage.php" id="addTopBannerForPagephp"><span class="title">Tepe Banner</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddMiddleBannerForPage.php" id="addMiddleBannerForPagephp"><span class="title">Orta Banner</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddBottomBannerForPage.php" id="addBottomBannerForPagephp"><span class="title">En Alt Banner</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddCarouselForPage.php" id="addCarouselForPagephp"><span class="title">Carousel Slayt</span></a></li>
                                    <li><a href="/_y/s/s/tasarim/AddHeaderBannerForPage.php" id="addHeaderBannerForPagephp"><span class="title">Header Banner</span></a></li>
                                </ul>
                            </li>
                            <li><a href="/_y/s/s/tasarim/AddWelcomeBanner.php" id="addWelcomeBannerphp"><span class="title">Karşılama Popup </span></a></li>
                            -->
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Galeriler</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/galeriler/AddGallery.php" id="addGalleryphp"><span class="title">Galeri Oluştur</span></a></li>
                            <li><a href="/_y/s/s/galeriler/GalleryList.php" id="galleryListphp"><span class="title">Galeriler</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Videolar</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/videolar/AddVideo.php" id="addVideophp"><span class="title">Video Ekle</span></a></li>
                            <li><a href="/_y/s/s/videolar/VideoList.php" id="videoListphp"><span class="title">Videolar</span></a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <!-- /Site İçerik -->

            <!-- E-TİCARET AYARLARI -->
            <?php if($siteType==1):?>
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-store"></i></div>
                    <span class="title">E-TİCARET AYARLARI</span>
                </a>
                <ul>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Tedarikçiler</span>
                        </a>

                        <ul>
                            <li><a href="/_y/s/s/tedarikciler/AddSupplier.php" id="addSupplierphp" ><span class="title">Tedarikçi Ekle</span></a></li>
                            <li><a href="/_y/s/s/tedarikciler/SupplierList.php" id="supplierListphp" ><span class="title">Tedarikçi Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Fiyat Ayarları</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/fiyatayar/PriceSettings.php" id="priceSettingsphp" ><span class="title">Fiyat Ayarları</span></a></li>
                            <li><a href="/_y/s/s/parabirimler/AddCurrency.php" id="addCurrencyphp" ><span class="title">Para Birimi Ekle</span></a></li>
                            <li><a href="/_y/s/s/parabirimler/CurrencyList.php" id="currencyListphp" ><span class="title">Para Birimi Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Ödeme Sağlayıcısı Ayarları</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/bankaeft/AddBankAccount.php" id="addBankAccountphp"><span class="title">Banka Hesabı Ekle</span></a></li>
                            <li><a href="/_y/s/s/bankaeft/BankAccountsList.php" id="bankAccountsListphp"><span class="title">Banka Hesapları</span></a></li>
                            <li><a href="/_y/s/s/odemeyontemi/AddPaymentGateway.php" id="addPaymentGatewayphp"><span class="title">Ödeme Sağlayıcısı Ekle</span></a></li>
                            <li><a href="/_y/s/s/odemeyontemi/PaymentGatewayList.php" id="paymentGatewayListphp"><span class="title">Ödeme Sağlayıcısı Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Kargo Ayarları</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/kargolar/kargoekle.php"  id="kargoeklephp"><span class="title">Kargo Ekle</span></a></li>
                            <li><a href="/_y/s/s/kargolar/kargoliste.php"  id="kargolistephp"><span class="title">Kargo Liste</span></a></li>
                            <li><a href="/_y/s/s/kargoucret/kargoucret.php"  id="kargoucretphp"><span class="title">Kargo Ücret</span></a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php endif;?>
            <!-- /E-TİCARET AYARLARI -->

            <!-- Genel Ayarlar -->

            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-settings"></i></div>
                    <span class="title">GENEL AYARLAR</span>
                </a>
                <ul>
                    <?php if($adminAuth<=1){?>
                    <!-- Genel Ayarlar -->
                    <li>
                        <a href="/_y/s/s/genelayarlar/AddGeneralSettings.php" id="addGeneralSettingsphp">
                            <span class="title">Genel Site Ayarları</span>
                        </a>

                    </li>
                    <li>
                        <a href="/_y/s/s/firmabilgileri/AddCompanySettings.php" id="addCompanySettingsphp">
                            <span class="title">Firma Bilgileriniz</span>
                        </a>
                    </li>
                    <li>
                        <a href="/_y/s/s/firmabilgileri/CompanySettingsList.php" id="companySettingsListphp">
                            <span class="title">Şube Bilgileriniz</span>
                        </a>
                    </li>
                    <li>
                        <a href="/_y/s/s/ekkodlar/AddSocialMedia.php" id="addSocialMediaphp" >
                            <span class="title">Sosyal Medya Hesaplarınız</span>
                        </a>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Analitik ve Dönüşüm Kodları</span>
                        </a>
                        <ul>
                            <li>
                                <a href="/_y/s/s/ekkodlar/PlatformTracking.php" id="platformTrackingphp" >
                                    <span class="title">🚀 Platform Tracking Yönetimi</span>
                                </a>
                            </li>
                            <li>
                                <a href="/_y/s/s/ekkodlar/AddTagManager.php" id="addTagManagerphp" >
                                    <span class="title">Google Tag Manager (Eski)</span>
                                </a>
                            </li>
                            <li>
                                <a href="/_y/s/s/ekkodlar/AdConversionCode.php" id="adConversionCodephp" >
                                    <span class="title">Reklam Dönüşüm Kodu (Eski)</span>
                                </a>
                            </li>
                            <li>
                                <a href="/_y/s/s/ekkodlar/SalesConversionCode.php" id="salesConversionCodephp" >
                                    <span class="title">Satış Dönüşüm Kodu (Eski)</span>
                                </a>
                            </li>
                            <li>
                                <a href="/_y/s/s/ekkodlar/CartConversionCode.php" id="cartConversionCodephp" >
                                    <span class="title">Sepet Dönüşüm Kodu (Eski)</span>
                                </a>
                            </li>
                            <li>
                                <a href="/_y/s/s/ekkodlar/AnalysisCode.php" id="analysisCodephp" >
                                    <span class="title">Ziyaretçi Analiz Kodu (Eski)</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">SMTP Ayarları</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/genelayarlar/AddSMTPSettings.php" id="addSMTPSettingsphp"><span class="title">SMTP Ayarları</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Sayfa Ayarları</span>
                        </a>

                        <ul>
                            <li><a href="/_y/s/s/ayarlar/AddPageType.php" id="addPageTypephp"><span class="title">Sayfa Tipi Ekle</span></a></li>
                            <li><a href="/_y/s/s/ayarlar/PageTypeList.php" id="pageTypeListphp"><span class="title">Sayfa Tip Listesi</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Yöneticiler</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/yoneticiler/AddAdmin.php" id="addAdminphp"><span class="title">Yönetici Ekle</span></a></li>
                            <li><a href="/_y/s/s/yoneticiler/AdminList.php" id="adminListphp"><span class="title">Yöneticiler</span></a></li>
                        </ul>
                    </li>
                    <?php if($adminAuth==0):?>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Loglar</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/Logs/LogReader.php" id="logReaderphp"><span class="title">Site Logları</span></a></li>
                            <li><a href="/_y/s/s/Logs/SystemLogReader.php" id="systemLogReaderphp"><span class="title">Sistem Logları</span></a></li>
                        </ul>
                    </li>
                    <?php endif;?>
                    <!-- Genel Ayarlar -- >


                        <!-- Firma -- >


                        <!-- Eklneti -- >
                        <li class="gui-folder">
                            <a>
                                <span class="title">Ek Kodlar</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="/_y/s/s/ekkodlar/yorumeklenti.php" id="yorumeklentiphp">
                                        <span class="title">Yorum Eklentisi</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/_y/s/s/ekkodlar/yorumeklentiliste.php" id="yorumeklentilistephp">
                                        <span class="title">Yorum Ekl. Liste</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Yönetici -- >
                    <?php } ?>
                    <!-- Kullanıcı Ayarları -->
                </ul>
            </li>

            <!-- /Genel Ayarlar -->

            <!-- İşlemler -- >

            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-info"></i></div>
                    <span class="title">İŞLEMLER</span>
                </a>
                <ul>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Bayiler</span>
                        </a>
                        <ul>
                            <li><a href="/_y/s/s/bayiler/bayigrupekle.php" id="bayigrupeklephp"><span class="title">Bayi Grup Ekle</span></a></li>
                            <li><a href="/_y/s/s/bayiler/bayigrupliste.php" id="bayigruplistephp"><span class="title">Bayi Grup Liste</span></a></li>
                            <li><a href="/_y/s/s/bayiler/bayiekle.php" id="bayieklephp"><span class="title">Bayi Ekle</span></a></li>
                            <li><a href="/_y/s/s/bayiler/bayiliste.php" id="bayilistephp"><span class="title">Bayi Liste</span></a></li>
                        </ul>
                    </li>
                    <li class="gui-folder">
                        <a>
                            <span class="title">Cari İşlemler</span>
                        </a>
                        <ul>
                            <li><a href="/_y/" ><span class="title">Cari Hareketler</span></a></li>
                            <li><a href="/_y/" ><span class="title">Bakiyeler</span></a></li>
                            <li><a href="/_y/" ><span class="title">Ödeme Bildirimi</span></a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <!-- İşlemler -->

            <!-- Tasarım -->
            <li class="gui-folder">
                <a>
                    <div class="gui-icon"><i class="md md-palette"></i></div>
                    <span class="title">SİTENİZİ ÖZELLEŞTİRİN</span>
                </a>
                <ul>                    <li><a href="/_y/s/s/tasarim/AddLogo.php" id="addLogophp"><span class="title">Logo Ekle</span></a></li>
                    <li><a href="/_y/s/s/tasarim/AddFavicon.php" id="addFaviconphp"><span class="title">Favicon Ekle</span></a></li>
                    <li>
                        <a href="/_y/s/s/tasarim/Design.php" id="designphp">
                            <span class="title">Site Görünümü</span>
                        </a>
                    </li>
                    <li>
                        <a href="/_y/s/s/tasarim/Theme.php" id="themephp">
                            <span class="title">🎨 Gelişmiş Tema Düzenleyici</span>
                        </a>
                    </li>
                    <!-- li>
                        <a href="/_y/s/s/tasarim/HomePageDesign.php" id="homePageDesignphp">
                            <span class="title">Ana Sayfa Görünümü</span>
                        </a>
                    </li>
                    <li>
                        <a href="/_y/s/s/tasarim/HomePageProducts.php" id="homePageProductsphp">
                            <span class="title">Ana Sayfa Ürün Grupları</span>
                        </a>
                    </li -->
                    <li>
                        <a href="/_y/s/s/tasarim/SiteSettings.php" id="siteSettingsphp">
                            <span class="title">Site Ayarları</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
        <div class="menubar-foot-panel">
            <small class="no-linebreak hidden-folded">
                <span class="opacity-75">Copyright &copy; <?=Date("Y")?></span> <strong>Global Pozitif Tek.</strong>
            </small>
        </div>
    </div>
</div>

<!-- oturuma devam edip etmeyeceğimizi soran ve evet hayır içeren modal oluşturalım  -->
<div class="modal fade" id="sessionWarning" tabindex="-1" role="dialog" aria-labelledby="sessionWarningLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-bold" id="sessionWarningLabel">Oturum Süreniz Dolmak Üzere</h4>
            </div>
            <div class="modal-body">
                <p>Oturum süreniz dolmak üzere. Oturuma devam etmek ister misiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hayır</button>
                <button type="button" class="btn btn-primary" id="renewSession">Evet</button>
            </div>
        </div>
    </div>
</div>
<?php if($adminAuth==0){?>
<!-- Asistan Simgesi ve Sohbet Penceresi -->
<div id="assistant-icon" style="position: fixed; bottom: 70px; right: 20px; background-color: #fff; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
    <img src="/_y/m/r/Logo/assistant-logo.png" alt="Assistant Icon" style="width: 30px; height: 30px;">
</div>
<div id="assistant-chat" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 300px; height: 400px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <div style="padding: 10px; background-color: #007bff; color: white; border-top-left-radius: 5px; border-top-right-radius: 5px;">
        Pozitif Asistan
        <span id="assistant-close" style="float: right; cursor: pointer;">&times;</span>
    </div>
    <div id="chat-messages" style="padding: 10px; height: 300px; overflow-y: auto; border-bottom: 1px solid #ccc;"></div>
    <div style="padding: 10px;">
        <input type="text" id="user-input" style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 5px;" placeholder="Mesajınızı yazın...">
        <div id="waiting-animation" class="waiting-animation" style="display: none;">...</div>
    </div>
</div>

<style>
    .waiting-animation {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-top: 10px;
    }
    .waiting-animation:after {
        content: " ";
        display: block;
        width: 16px;
        height: 16px;
        margin: 2px;
        border-radius: 50%;
        border: 3px solid #2471aa;
        border-color: #2471aa transparent #2471aa transparent;
        animation: waiting-animation 1.2s linear infinite;
    }
    @keyframes waiting-animation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    #menubar{
        overflow-y: auto;
        background-color: #fff;
    }
    #menubar .menubar-foot-panel{
        display: none;
    }
    #menubar .menubar-scroll-panel{
        background-color: #fff;
    }
</style>
<?php } ?>
<script>

    var sessionLifeTime = <?=$sessionLifetime?>;
    var sessionTimerContainer = document.getElementById('sessionTimer');
    var warningThreshold = 300;

    // Sayfa ilk yüklendiğinde localStorage'ı başlat
    localStorage.setItem('sessionLifeTime', sessionLifeTime);


    function sessionTimer() {
        // Her saniye oturum süresini düşür
        var sessionTimer = setInterval(function () {
            sessionLifeTime--; // Kendi timer'ı azalt
            localStorage.setItem('sessionLifeTime', sessionLifeTime); // localStorage'ı da güncelle

            if (sessionLifeTime <= 0) {
                clearInterval(sessionTimer);
                window.location.href = "/_y/s/guvenlik/kilit.php?refUrl=<?=urlencode($_SERVER['REQUEST_URI'])?>";
            } else if (sessionLifeTime === warningThreshold) {
                $('#sessionWarning').modal('show');
                document.getElementById('renewSession').addEventListener('click', function () {
                    renewSessionAjax();
                    $('#sessionWarning').modal('hide');
                });
            }
            sessionTimerContainer.innerHTML = sessionLifeTime;
        }, 1000);

        // Her 30 saniyede bir localStorage ile karşılaştır
        setInterval(function () {
            var storedSessionLifeTime = parseInt(localStorage.getItem('sessionLifeTime'), 10);

            // Değerleri karşılaştır ve büyük olanı kullan
            if (storedSessionLifeTime > sessionLifeTime) {
                sessionLifeTime = storedSessionLifeTime;
            } else {
                localStorage.setItem('sessionLifeTime', sessionLifeTime);
            }
        }, 30000);
    }

    function renewSessionAjax() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/_y/s/guvenlik/reNewSession.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = xhr.responseText; // Sunucudan gelen yanıtı al
                //console.log("Yeni oturum süresi alındı: " + response);
                sessionLifeTime = parseInt(response, 10);

                // localStorage'ı güncelle
                localStorage.setItem('sessionLifeTime', sessionLifeTime);
            }
        };
        xhr.send();
    }

    // Oturum değişikliklerini dinle (diğer sekmelerden gelen)
    window.addEventListener('storage', function (event) {
        if (event.key === 'sessionLifeTime') {
            var newSessionLifeTime = parseInt(event.newValue, 10);
            //console.log("LocalStorage güncellendi, yeni değer: " + newSessionLifeTime);

            if (newSessionLifeTime > sessionLifeTime) {
                sessionLifeTime = newSessionLifeTime; // Büyük olanı kullan
                sessionTimerContainer.innerHTML = sessionLifeTime;
            }
        }
    });

    // Oturum zamanlayıcısını başlat
    sessionTimer();



    <?php if($adminAuth==0){?>
    document.addEventListener('DOMContentLoaded', function () {
        let threadId = null;
        let checkMessagesInterval = null;
        let lastMessageId = null; // Son gösterilen mesajın ID'sini saklayacağız

        // Sayfa yüklendiğinde mesajları ve threadId'yi yükle
        loadMessages();
        loadThreadId();

        $('#assistant-icon').click(function() {
            $('#assistant-chat').toggle();
        });

        $('#assistant-close').click(function() {
            $('#assistant-chat').hide();

            if (checkMessagesInterval) {
                clearInterval(checkMessagesInterval);
            }

            localStorage.removeItem('chatMessages');
            localStorage.removeItem('threadId');

            threadId = null;
            checkMessagesInterval = null;
            lastMessageId = null;
            $('#chat-messages').html('');
        });

        $('#user-input').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                var userInput = $('#user-input').val();
                if (userInput.trim() !== '') {
                    $('#chat-messages').append('<div><strong>Ben:</strong> ' + userInput + '</div>');
                    saveMessages(); // Mesajları kaydet
                    $('#user-input').val('');

                    $('#user-input').prop('disabled', true); // input'u pasif hale getir
                    $('#assistant-icon').prop('disabled', true); // button'u pasif hale getir
                    //user-input placeholder değiştirelim
                    $('#user-input').attr('placeholder', 'Asistan düşünüyor...');
                    $('#waiting-animation').show(); // Bekleme animasyonunu göster

                    if (!threadId) {
                        // Yeni bir thread oluştur ve mesaj gönder
                        askAssistant(userInput);
                    } else {
                        // Mevcut thread'e mesaj gönder
                        sendMessage(userInput);
                    }
                }
            }
        });

        function askAssistant(prompt) {
            console.log("sohbet başlat");
            $.ajax({
                url: '/App/Controller/Admin/AdminAIController.php',
                method: 'POST',
                data: {
                    action: 'askAssistant',
                    prompt: prompt
                },
                success: function(response) {

                    $('#user-input').prop('disabled', false); // input'u aktif hale getir
                    $('#assistant-icon').prop('disabled', false); // button'u aktif hale getir
                    //user-input placeholder eski haline getirelim
                    $('#user-input').attr('placeholder', 'Mesajınızı yazın...');
                    $('#waiting-animation').hide(); // Bekleme animasyonunu gizle

                    console.log(response);
                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        $('#chat-messages').append('<div><strong>Asistan:</strong> ' + response.answer + '</div>');
                        saveMessages(); // Mesajları kaydet
                        threadId = response.threadId;
                        saveThreadId(); // ThreadId'yi kaydet
                        lastMessageId = response.messageId; // Gelen mesajın ID'sini sakla

                    } else {
                        $('#chat-messages').append('<div><strong>Asistan:</strong> Bir hata oluştu.</div>');
                        saveMessages(); // Mesajları kaydet
                    }
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                },
                error: function() {
                    $('#user-input').prop('disabled', false); // input'u aktif hale getir
                    $('#assistant-icon').prop('disabled', false); // button'u aktif hale getir
                    //user-input placeholder eski haline getirelim
                    $('#user-input').attr('placeholder', 'Mesajınızı yazın...');
                    $('#waiting-animation').hide(); // Bekleme animasyonunu gizle
                    $('#chat-messages').append('<div><strong>Asistan:</strong> Bir hata oluştu.</div>');
                    saveMessages(); // Mesajları kaydet
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                }
            });
        }

        function sendMessage(prompt) {
            console.log("mesaj gönder");
            $.ajax({
                url: '/App/Controller/Admin/AdminAIController.php',
                method: 'POST',
                data: {
                    action: 'sendMessage',
                    threadId: threadId,
                    prompt: prompt
                },
                success: function(response) {
                    console.log("Mesaj Gönder success")
                    $('#user-input').prop('disabled', false); // input'u aktif hale getir
                    $('#assistant-icon').prop('disabled', false); // button'u aktif hale getir
                    //user-input placeholder eski haline getirelim
                    $('#user-input').attr('placeholder', 'Mesajınızı yazın...');
                    $('#waiting-animation').hide(); // Bekleme animasyonunu gizle

                    response = JSON.parse(response);
                    if (response.status === 'success') {
                        console.log("Mesaj Gönder status success")
                        if(response.messageId == lastMessageId){
                            return;
                        }
                        $('#chat-messages').append('<div><strong>Asistan:</strong> ' + response.answer + '</div>');
                        saveMessages(); // Mesajları kaydet
                        lastMessageId = response.messageId; // Gelen mesajın ID'sini sakla
                        conole.log("lastMessageId:" + lastMessageId);
                    } else {
                        console.log("Mesaj Gönder status error")
                        $('#chat-messages').append('<div><strong>Asistan:</strong> Bir hata oluştu.</div>');
                        saveMessages(); // Mesajları kaydet
                    }
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

                    if (!checkMessagesInterval) {
                        checkMessagesInterval = setInterval(checkForNewMessages, 5000);
                    }
                },
                error: function() {
                    console.log("Mesaj Gönder Error")
                    $('#user-input').prop('disabled', false); // input'u aktif hale getir
                    $('#assistant-icon').prop('disabled', false); // button'u aktif hale getir
                    //user-input placeholder eski haline getirelim
                    $('#user-input').attr('placeholder', 'Mesajınızı yazın...');
                    $('#waiting-animation').hide(); // Bekleme animasyonunu gizle
                    $('#chat-messages').append('<div><strong>Asistan:</strong> Bir hata oluştu.</div>');
                    saveMessages(); // Mesajları kaydet
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

                    if (!checkMessagesInterval) {
                        checkMessagesInterval = setInterval(checkForNewMessages, 5000);
                    }
                }
            });
        }

        function checkForNewMessages() {
            console.log("yeni mesaj kontrol için thread id kontrol ediliyor")
            if (threadId) {
                console.log("yeni mesaj kontrol ediliyor")
                $.ajax({
                    url: '/App/Controller/Admin/AdminAIController.php',
                    method: 'POST',
                    data: {
                        action: 'getMessages',
                        threadId: threadId
                    },
                    success: function(response) {
                        console.log("yeni mesaj success")
                        console.log(response);
                        response = JSON.parse(response);

                        if (response.status === 'success') {
                            let newMessages = false;

                            response.messages.forEach(function(message) {
                                if (message.role === 'assistant' && message.id !== lastMessageId) {
                                    console.log("yeni mesaj var")
                                    $('#chat-messages').append('<div><strong>Asistan:</strong> ' + message.content[0].text.value + '</div>');
                                    saveMessages(); // Mesajları kaydet
                                    lastMessageId = message.id; // Son gösterilen mesajın ID'sini güncelle
                                    newMessages = true;
                                }
                                else{
                                    console.log("yeni mesaj yok")
                                }
                                //ilk gelen mesaj (son id)'ı kontrol etmemiz yeterli çıkış yapalım
                                return;
                            });

                            if (!newMessages && checkMessagesInterval) {
                                console.log("yeni mesaj timer sil")
                                clearInterval(checkMessagesInterval); // Yeni mesaj yoksa intervali durdur
                            }
                            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                        }
                    },
                    error: function() {
                        console.log('Yeni mesajları getirirken hata oluştu');
                    }
                });
            }
        }

        function saveMessages() {
            const messages = $('#chat-messages').html();
            localStorage.setItem('chatMessages', messages);
        }

        function loadMessages() {
            const messages = localStorage.getItem('chatMessages');
            if (messages) {
                $('#chat-messages').html(messages);
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
            }
        }

        function saveThreadId() {
            localStorage.setItem('threadId', threadId);
        }

        function loadThreadId() {
            const savedThreadId = localStorage.getItem('threadId');
            if (savedThreadId) {
                threadId = savedThreadId;
            }
        }

    });
    <?php } ?>
</script>
