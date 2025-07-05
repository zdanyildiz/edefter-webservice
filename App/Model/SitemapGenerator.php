<?php
class SitemapGenerator {
    private $seos;

    public function __construct($seos) {
        $this->seos = $seos;
    }


    public function generateSitemap() {
        $sitemap = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml"></urlset>');

        foreach ($this->seos as $seo) {
            $url = $sitemap->addChild('url');
            $url->addChild('loc', htmlspecialchars($seo->link));
            $url->addChild('lastmod', date(DATE_ATOM));
            $url->addChild('changefreq', 'daily');
            $url->addChild('priority', '0.5');

            // Dil bilgisini ekleyin
            $link = $url->addChild('xhtml:link', '', 'http://www.w3.org/1999/xhtml');
            $link->addAttribute('rel', 'alternate');
            $link->addAttribute('hreflang', $seo->dil); // Seo modelinden dil bilgisini kullanın
            $link->addAttribute('href', htmlspecialchars($seo->link));
        }

        return $sitemap->asXML();
    }

    public function generateImageSitemap() {
        $sitemap = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml"></urlset>');

        foreach ($this->seos as $seo) {

            if(!empty($seo->resim)){
                $url = $sitemap->addChild('url');
                $url->addChild('loc', htmlspecialchars($seo->link));

                // Dil bilgisini ekleyin
                $link = $url->addChild('xhtml:link', '', 'http://www.w3.org/1999/xhtml');
                $link->addAttribute('rel', 'alternate');
                $link->addAttribute('hreflang', $seo->dil); // Buraya dil kodunu ekleyin
                $link->addAttribute('href', htmlspecialchars($seo->link));

                // Resimleri virgülle ayır
                $images = explode(',', $seo->resim);

                // Her bir resim için bir <image:image> elementi oluştur
                foreach ($images as $image) {
                    if(!empty($image)){
                        $imageElement = $url->addChild('image:image', '', 'http://www.google.com/schemas/sitemap-image/1.1');
                        $imageElement->addChild('image:loc', htmlspecialchars(trim($image)), 'http://www.google.com/schemas/sitemap-image/1.1');
                    }

                }
            }
        }

        return $sitemap->asXML();
    }

    public function titleCase($string) {
        if ($string === null) {
            $string = '';
        }
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        return $string;
    }

    /*public function generateMerchantCenterSitemap($products,$euro,$languageCode) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $urlset = $doc->createElement('rss');
        $urlset->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0  version="2.0"');
        $doc->appendChild($urlset);


        if(!empty($languageCode)){
            $products = array_filter($products, function($product) use ($languageCode) {
                return $product['languageCode'] == $languageCode;
            });
        }

        $stockCodes = [];


        foreach ($products as $product) {
            if ($product['productPrice'] == 0 || empty($product['productPrice']) || $product['productPrice'] == "0.00" || empty($product['productStockCode'])) {
                continue;
            }

            if(in_array($product['productStockCode'], $stockCodes)){
                continue;
            }

            $item = $doc->createElement('item');
            $urlset->appendChild($item);

            $productLink = ($languageCode =="TR") ? "https://www.makinaelemanlari.com".$product['productLink'] : "https://en.makinaelemanlari.com".$product['productLink'];

            $productStockCode = $doc->createElement('g:id', $product['productStockCode']);
            $item->appendChild($productStockCode);
            //$this->addCData($doc, $item, 'g:id', $product['productStockCode']);

            //$this->addCData($doc, $item, 'g:title', $this->titleCase($product['productName']));
            $title = $doc->createElement('g:title', $this->titleCase($product['productName']));
            $item->appendChild($title);

            //$this->addCData($doc, $item, 'g:link', $productLink);
            $link = $doc->createElement('g:link', $productLink);
            $item->appendChild($link);

            //$this->addCData($doc, $item, 'g:image_link', "https://www.makinaelemanlari.com".$product['productImage']);
            $imageLink = $doc->createElement('g:image_link', "https://www.makinaelemanlari.com".$product['productImage']);
            $item->appendChild($imageLink);

            $price = number_format($product['productPrice']*$euro, 2, '.', '');
            $priceUnit = ($languageCode=="EN") ? "EUR" : "TRY";

            $price = $doc->createElement('g:price', $price." ".$priceUnit);
            $item->appendChild($price);
            //$this->addCData($doc, $item, 'g:price', $price." ".$priceUnit);

            //$this->addCData($doc, $item, 'g:availability', 'in stock');
            $availability = $doc->createElement('g:availability', 'in stock');
            $item->appendChild($availability);

            //$this->addCData($doc, $item, 'g:brand', $product['productBrand']);
            $brand = $doc->createElement('g:brand', $product['productBrand']);
            $item->appendChild($brand);

            //$this->addCData($doc, $item, 'g:condition', ($languageCode=="TR") ? "Yeni" : "New");
            $condition = $doc->createElement('g:condition', ($languageCode=="TR") ? "Yeni" : "New");
            $item->appendChild($condition);

            $productType = $product['productCategory'];
                //($languageCode == "TR") ? "Endüstriyel Ekipman" : "Industrial Equipment";

            //$this->addCData($doc, $item, 'g:product_type', $productType);
            $productType = $doc->createElement('g:product_type', $productType);
            $item->appendChild($productType);


            $productCategory = ($languageCode=="TR") ? "İş ve Endüstri > Endüstriyel Ekipman" : "Business and Industry > Industrial Equipment";
            //$this->addCData($doc, $item, 'g:google_product_category', $productCategory." > " .$product['productCategory']);
            $googleProductCategory = $doc->createElement('g:google_product_category', $productCategory." > " .$product['productCategory']);
            $item->appendChild($googleProductCategory);

            $productSku = $doc->createElement('g:sku', $product['productStockCode']);
            $item->appendChild($productSku);
            //$this->addCData($doc, $item, 'g:sku', $product['productStockCode']);

            $shipping = $doc->createElement('g:shipping');
            $item->appendChild($shipping);
            //$this->addCData($doc, $shipping, 'g:country', 'TR');
            //$this->addCData($doc, $shipping, 'g:service', 'Standard');
            //$this->addCData($doc, $shipping, 'g:price', '0.00 TRY');
            //cdata olmadan kargo özelliklerini ekleyelim
            $country = $doc->createElement('g:country', 'TR');
            $shipping->appendChild($country);
            $service = $doc->createElement('g:service', 'Standard');
            $shipping->appendChild($service);
            $price = $doc->createElement('g:price', '0.00 TRY');
            $shipping->appendChild($price);

            $stockCodes[] = $product['productStockCode'];
        }

        return $doc->saveXML();
    }*/

    public function generateMerchantCenterSitemap($siteDomain,$products,$currentRates,$currencyCode,$languageCode) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $rss = $doc->createElement('rss');
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $rss->setAttribute('version', '2.0');
        $doc->appendChild($rss);

        $channel = $doc->createElement('channel');
        $rss->appendChild($channel);

        $title = $doc->createElement('title', DOMAIN.' - Online Mağza Ürünleri');
        $channel->appendChild($title);

        $link = $doc->createElement('link', 'https://'.DOMAIN);
        $channel->appendChild($link);

        $description = $doc->createElement('description', 'Tüm ürünler');
        $channel->appendChild($description);

        foreach ($products as $product) {
            if ($product['productPrice'] == 0 || empty($product['productPrice']) || $product['productPrice'] == "0.00" || empty($product['productStockCode'])) {
                continue;
            }

            $item = $doc->createElement('item');
            $channel->appendChild($item);

            $productLink = $siteDomain.$product['productLink'];

            $stockCode = $product['productStockCode'];
            $productStockCode = $doc->createElement('g:id', $stockCode);
            $item->appendChild($productStockCode);

            //$title = $doc->createElement('g:title', $this->titleCase($product['productName']));
            //$item->appendChild($title);
            $this->addCData($doc, $item, 'g:title', $this->titleCase($product['productName']));

            //$link = $doc->createElement('g:link', $productLink);
            //$item->appendChild($link);
            $this->addCData($doc, $item, 'g:link', $productLink."?q=".$product['productStockCode']);

            //$imageLink = $doc->createElement('g:image_link', $siteDomain.$product['productImage']);
            //$item->appendChild($imageLink);

            $productImages =explode(",",$product['productImages']) ?? [];

            if(!empty($productImages)){
                $i=0;
                foreach ($productImages as $productImage) {
                    $i++;
                    if($i==1){
                        $imageLink = $doc->createElement('g:image_link', $siteDomain.imgRoot.$productImage);
                        $item->appendChild($imageLink);
                    }
                    else{
                        $additionalImageLink = $doc->createElement('g:additional_image_link', $siteDomain.imgRoot.$productImage);
                        $item->appendChild($additionalImageLink);

                    }
                }
            }


            $condition = $doc->createElement('g:condition', ($languageCode=="TR") ? "Yeni" : "New");
            $item->appendChild($condition);

            $availability = $doc->createElement('g:availability', 'in stock');
            $item->appendChild($availability);

            $price = number_format($product['productPrice']*$currentRates, 2, '.', '');
            $priceUnit = $currencyCode;

            $price = $doc->createElement('g:price', $price." ".$priceUnit);
            $item->appendChild($price);

            $shipping = $doc->createElement('g:shipping');
            $item->appendChild($shipping);

            $country = $doc->createElement('g:country', 'TR');
            $shipping->appendChild($country);

            $service = $doc->createElement('g:service', 'Standard');
            $shipping->appendChild($service);

            $price = $doc->createElement('g:price', '0.00 '.$priceUnit);
            $shipping->appendChild($price);

            $sku = $doc->createElement('g:sku', $stockCode); // Örnek bir GTIN değeri
            $item->appendChild($sku);

            $mpn = $doc->createElement('g:mpn', $stockCode); // Örnek bir GTIN değeri
            $item->appendChild($mpn);

            $brand = $doc->createElement('g:brand', $product['productBrand']);
            $item->appendChild($brand);

            $productType = $product['productCategory'];
            $productType = $doc->createElement('g:product_type', $productType);
            $item->appendChild($productType);

            $googleCategory = !empty($product['merchant_category_name']) ? $product['merchant_category_name'] : $product['productCategory'];
            $googleProductCategory = $doc->createElement('g:google_product_category', $googleCategory);
            $item->appendChild($googleProductCategory);
        }

        return $doc->saveXML();
    }
    public function addCData($doc, $element, $name, $value) {
        $node = $doc->createElement($name);
        $cdata = $doc->createCDATASection($value);
        $node->appendChild($cdata);
        $element->appendChild($node);
    }

    public function generateProductList($siteDomain,$products){
        //ürün id, ürün kategorisi, ürün adı, ürün açıklaması, ürün resimleri, ürün linki , ürün stok kodu, ürün fiyatı
        $doc = new DOMDocument('1.0', 'UTF-8');
        $rss = $doc->createElement('rss');
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $rss->setAttribute('version', '2.0');
        $doc->appendChild($rss);

        $channel = $doc->createElement('channel');
        $rss->appendChild($channel);

        $title = $doc->createElement('title', DOMAIN.' - Online Mağza Ürünleri');
        $channel->appendChild($title);

        $link = $doc->createElement('link', $siteDomain);
        $channel->appendChild($link);

        $description = $doc->createElement('description', 'Tüm ürünler');
        $channel->appendChild($description);

        foreach ($products as $product) {
            if ($product['productPrice'] == 0 || empty($product['productPrice']) || $product['productPrice'] == "0.00" || empty($product['productStockCode'])) {
                continue;
            }

            $item = $doc->createElement('item');
            $channel->appendChild($item);

            $productLink = $siteDomain . $product['productLink'];

            $stockCode = $product['productStockCode'];
            $productStockCode = $doc->createElement('id', $stockCode);
            $item->appendChild($productStockCode);

            $this->addCData($doc, $item, 'title', $this->titleCase($product['productName']));

            $this->addCData($doc, $item, 'link', $productLink . "?q=" . $product['productStockCode']);

            $productImages = explode(",", $product['productImages']) ?? [];

            if (!empty($productImages)) {
                $i = 0;
                foreach ($productImages as $productImage) {
                    $i++;
                    if ($i == 1) {
                        $imageLink = $doc->createElement('image_link', $siteDomain . imgRoot . $productImage);
                        $item->appendChild($imageLink);
                    } else {
                        $additionalImageLink = $doc->createElement('additional_image_link', $siteDomain . imgRoot . $productImage);
                        $item->appendChild($additionalImageLink);

                    }
                }
            }

            $price = number_format($product['productPrice'], 2, '.', '');
            $priceUnit = $product['currencyCode'];

            $price = $doc->createElement('price', $price . " " . $priceUnit);
            $item->appendChild($price);

            //cargo bilgisi gerekli değil

            $sku = $doc->createElement('stockCode', $stockCode);
            $item->appendChild($sku);


            $productType = $product['productCategory'];
            $productType = $doc->createElement('category', $productType);
            $item->appendChild($productType);

            //variantroperties'i de alalım json geliyor
            $variantProperties = json_decode($product['productVariants'],true);
            //$variantProperties =Array
            //(
            //    [0] => Array
            //        (
            //            [variantID] => 8247
            //            [variantName] => DÖNÜŞLÜ KONSOL GÖVDESİ (ORTA BOY)
            //            [variantCurrencyID] => 3
            //            [variantCurrencyCode] => EUR
            //            [variantCurrencySymbol] => €
            //            [variantSellingPrice] => 9.66
            //            [variantPriceWithoutDiscount] => 12.08
            //            [variantSellerPrice] => 0.00
            //            [variantDiscountRate] => 20
            //            [variantQuantity] => 999
            //            [variantStockCode] => 210.IDT.200
            //            [variantMinQuantity] => 1.0000
            //            [variantCoefficient] => 1.0000
            //            [variantProperties] => Array
            //                (
            //                    [0] => Array
            //                        (
            //                            [attribute] => Array
            //                                (
            //                                    [name] => Malzeme
            //                                    [value] => Güçlendirilmiş Polyamid (PA)
            //                                )
            //
            //                        )
            //
            //                    [1] => Array
            //                        (
            //                            [attribute] => Array
            //                                (
            //                                    [name] => Renk
            //                                    [value] => Siyah
            //                                )
            //
            //                        )
            //
            //                )
            //
            //        )
            //
            //)

            $variantsItem = $doc->createElement('variants');
            $item->appendChild($variantsItem);

            if(!empty($variantProperties)){
                foreach ($variantProperties as $variantProperty){

                    if($variantProperty['variantStockCode'] != $stockCode){
                        continue;
                    }
                    $variantItem = $doc->createElement('variantItem');
                    $variantsItem->appendChild($variantItem);

                    /*$variantStockCode = $doc->createElement('variantId', $variantProperty['variantStockCode']);
                    $variantItem->appendChild($variantStockCode);*/

                    /*$this->addCData($doc, $variantItem, 'variantTitle', $this->titleCase($variantProperty['variantName']));*/

                    /*$price = number_format($variantProperty['variantSellingPrice'], 2, '.', '');
                    $priceUnit = $variantProperty['variantCurrencyCode'];*/

                    /*$price = $doc->createElement('variantPrice', $price . " " . $priceUnit);
                    $variantItem->appendChild($price);*/


                    if(!empty($variantProperty['variantProperties'])){
                        foreach ($variantProperty['variantProperties'] as $variantPropertyItem){

                            $variantPropertyElement = $doc->createElement('variantProperty');
                            $variantItem->appendChild($variantPropertyElement);

                            $attribute = $doc->createElement('attribute', $variantPropertyItem['attribute']['name']);
                            $variantPropertyElement->appendChild($attribute);

                            $value = $doc->createElement('value', $variantPropertyItem['attribute']['value']);
                            $variantPropertyElement->appendChild($value);
                        }
                    }
                }
            }

        }

        return $doc->saveXML();

    }

}