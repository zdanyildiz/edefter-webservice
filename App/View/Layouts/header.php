<?php
/**
 * @var Casper $casper
 * @var array $bannerInfo
 * @var array $companySettings
 * @var Config $config
 * @var int $languageID
 * @var string $favoriteLink
 * @var string $cartLink
 * @var string $memberLink
 * @var Menu $allMenu
 * @var array $siteSettings
 * @var Session $session
 * @var Database $db
 * @var string $languageCode
 */

// Banner Manager sınıfını include et
if (isset($config)) {
    $config->includeClass("BannerManager");
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/App/Core/BannerManager.php';
}

$visitorCart = $casper->getVisitor()["visitorCart"] ?? [];
$cartCount = (isset($visitorCart["totalCount"])) ? $visitorCart["totalCount"] : 0;

$siteConfig = $casper->getSiteConfig();
$generalSettings = $siteConfig["generalSettings"];
$siteType = $generalSettings["sitetip"];
$isMemberRegistration = $generalSettings["uyelik"];
//$siteSettings sadece header için
$siteHeaderSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "header";
});
$headerShowMenu = 0;
$headerShowSearch = 0;
$headerShowMemberIcon = 0;
$headerShowBasketIcon = 0;
$headerShowFavIcon = 0;
$headerShowSocialMedia = 0;
$headerShowEmail = 0;
$headerShowPhone = 0;
$headerShowWhatsapp = 0;
$headerShowLanguages = 0;
foreach ($siteHeaderSettings as $siteSetting) {
    if($siteSetting['element'] == "menu"){
        $headerShowMenu = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "search"){
        $headerShowSearch = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "member_icon"){
        $headerShowMemberIcon = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "fav_icon"){
        $headerShowFavIcon = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "basket_icon"){
        $headerShowBasketIcon = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "social_media"){
        $headerShowSocialMedia = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "email"){
        $headerShowEmail = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "phone"){
        $headerShowPhone = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "whatsapp"){
        $headerShowWhatsapp = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "language") {
        $headerShowLanguages = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
}
if($headerShowLanguages == 1){
    include_once MODEL . 'Language.php';
    $language = new Language($db,$languageCode,$languageID);
    $languages = $language->getLanguagesWithLink();
}

$logoSettings = $casper->getSiteConfig()["logoSettings"];
$logoImg = $logoSettings["resim_url"];
$logoAlt = $logoSettings["logoyazi"];

// firma bilgileri
//$companySettings = $casper->getSiteConfig()["companySettings"];
$companyPhone = $companySettings["ayarfirmatelefon"] ?? "";
$companyEmail = $companySettings["ayarfirmaeposta"] ?? "";
$companyGsm = $companySettings["ayarfirmagsm"] ?? "";
$companyCountryCode = $companySettings["ayarfirmaulkekod"] ?? "";

$socialMediaSettings = $siteConfig["socialMediaSettings"];

$companyFacebook = $socialMediaSettings["facebook"] ?? '';
$companyTwitter = $socialMediaSettings["twitter"] ?? '';
$companyInstagram = $socialMediaSettings["instagram"] ?? '';
$companyLinkedin = $socialMediaSettings["linkedin"] ?? '';
$companyYoutube = $socialMediaSettings["youtube"] ?? '';

/**
 * @var array $bannerInfo
 */
//echo "<pre>";print_r($session->getSession("category"));exit;
$bannerPageID = null; $bannerCategoryID = null;
if($session->getSession("page")!=[]){
    $bannerPageID = $session->getSession("page")['sayfaid'];
}
if($session->getSession("category")!=[]){
    $bannerCategoryID = $session->getSession("category")['category']['kategoriid'];
}
if($session->getSession("mainPage")!=[]){
    $bannerCategoryID = $session->getSession("mainPage")["homePageCategoryId"];
}


$bannerManager = BannerManager::getInstance();
$topBannerResult = $bannerManager->getTopBanners($bannerPageID, $bannerCategoryID);
$topBannersHtml = $topBannerResult['html'];
?>
<?php
if(!empty($topBannersHtml)){
?>
<?=$topBannersHtml?>
<?php }?>

<section id="top-contactAndSocial">
    <div class="top-contactAndSocialContainer">
        <div id="top-contacts">
            <?php
            // E-posta boş değilse
            if (!empty($companyEmail) && $headerShowEmail==1):?>
                <div class="contact-icon">
                    <a href="mailto:<?=$companyEmail?>" title="<?=_header_eposta_title?>"><svg xmlns="http://www.w3.org/2000/svg" height="40px" width="40px" id="Capa_1" viewBox="0 0 75.294 75.294" xml:space="preserve"><g><path d="M66.097,12.089h-56.9C4.126,12.089,0,16.215,0,21.286v32.722c0,5.071,4.126,9.197,9.197,9.197h56.9   c5.071,0,9.197-4.126,9.197-9.197V21.287C75.295,16.215,71.169,12.089,66.097,12.089z M61.603,18.089L37.647,33.523L13.691,18.089   H61.603z M66.097,57.206h-56.9C7.434,57.206,6,55.771,6,54.009V21.457l29.796,19.16c0.04,0.025,0.083,0.042,0.124,0.065   c0.043,0.024,0.087,0.047,0.131,0.069c0.231,0.119,0.469,0.215,0.712,0.278c0.025,0.007,0.05,0.01,0.075,0.016   c0.267,0.063,0.537,0.102,0.807,0.102c0.001,0,0.002,0,0.002,0c0.002,0,0.003,0,0.004,0c0.27,0,0.54-0.038,0.807-0.102   c0.025-0.006,0.05-0.009,0.075-0.016c0.243-0.063,0.48-0.159,0.712-0.278c0.044-0.022,0.088-0.045,0.131-0.069   c0.041-0.023,0.084-0.04,0.124-0.065l29.796-19.16v32.551C69.295,55.771,67.86,57.206,66.097,57.206z"/></g></svg><?=$companyEmail?></a>
                </div>
            <?php endif; ?>
            <?php
            // Telefon boş değilse
            if (!empty($companyPhone) && $headerShowPhone==1):
                $companyPhone = str_contains($companyPhone, "+") ? $companyPhone : "+".$companyCountryCode.$companyPhone;
                ?>
                <div class="contact-icon"><a href="tel:<?=$companyPhone?>" title="<?=_header_telefon_title?>"><svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)"  stroke="none"><path d="M6145 12794 c-216 -13 -391 -28 -530 -45 -995 -122 -1927 -467 -2760 -1022 -907 -604 -1648 -1433 -2146 -2402 -395 -769 -615 -1549 -690 -2450 -17 -193 -17 -757 0 -950 75 -901 295 -1681 690 -2450 610 -1187 1579 -2156 2766 -2766 769 -395 1549 -615 2450 -690 193 -17 757 -17 950 0 901 75 1681 295 2450 690 1187 610 2156 1579 2766 2766 395 769 615 1549 690 2450 17 193 17 757 0 950 -75 901 -295 1681 -690 2450 -610 1187 -1579 2156 -2766 2766 -753 387 -1531 610 -2390 684 -164 15 -666 27 -790 19z m739 -779 c1310 -112 2519 -671 3464 -1599 980 -963 1561 -2210 1673 -3591 15 -193 15 -657 0 -850 -110 -1350 -664 -2567 -1605 -3523 -965 -981 -2206 -1559 -3591 -1673 -193 -16 -657 -16 -850 0 -1386 114 -2628 692 -3591 1672 -943 961 -1493 2167 -1605 3524 -16 193 -16 657 0 850 115 1388 693 2628 1672 3591 878 862 1988 1408 3189 1568 416 55 832 66 1244 31z"/><path d="M5060 10738 c-54 -15 -679 -379 -716 -417 -83 -84 -102 -207 -50 -309 54 -105 1149 -1998 1175 -2032 58 -73 186 -116 276 -91 46 12 662 365 715 409 68 57 105 179 81 267 -6 22 -274 497 -596 1055 -474 822 -594 1023 -631 1057 -68 64 -164 87 -254 61z"/><path d="M3942 9867 c-214 -130 -321 -208 -435 -317 -334 -319 -423 -636 -366 -1295 82 -942 549 -2201 1249 -3365 657 -1092 1446 -1996 2175 -2492 375 -255 608 -343 910 -343 202 0 370 40 606 145 110 50 365 185 374 199 3 6 -1137 1992 -1198 2088 -6 10 -17 8 -47 -7 -69 -35 -183 -69 -272 -80 -327 -40 -714 148 -1073 522 -444 462 -796 1143 -905 1753 -81 448 -20 809 175 1038 33 39 144 132 166 139 8 3 -217 401 -590 1049 -332 574 -606 1047 -610 1051 -4 5 -75 -34 -159 -85z"/><path d="M8220 5330 c-55 -7 -46 -3 -413 -214 -164 -94 -314 -186 -333 -204 -72 -69 -102 -185 -72 -273 17 -50 1155 -2026 1197 -2078 71 -90 212 -117 326 -62 86 41 614 347 655 380 56 45 90 120 90 202 0 37 -5 81 -12 96 -34 80 -1175 2043 -1206 2075 -61 64 -141 91 -232 78z"/></g></svg><?=$companyPhone?></a></div>
            <?php endif;?>
            <?php
            // gsm boş değilse
            if (!empty($companyGsm) && $headerShowWhatsapp==1):
                $companyGsm = str_contains($companyGsm, "+") ? $companyGsm : "+".$companyCountryCode.$companyGsm;
                ?>
                <div class="contact-icon">
                    <a href="https://wa.me/<?=$companyGsm?>" title="<?=_header_whatsapp_title?>"><svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="340 -40 640.000000 640.000000" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)"  stroke="none"><path d="M6255 6844 c-540 -35 -1107 -229 -1555 -532 -473 -320 -848 -752 -1091 -1256 -133 -276 -216 -536 -273 -856 -43 -240 -52 -602 -22 -880 40 -374 177 -822 362 -1188 l53 -103 -123 -367 c-68 -202 -191 -570 -274 -818 -84 -249 -152 -459 -152 -469 0 -9 13 -22 29 -28 26 -10 29 -14 24 -45 -6 -32 -5 -34 18 -27 41 13 936 298 1314 420 198 63 368 115 378 115 9 0 52 -17 95 -39 366 -184 756 -294 1171 -332 164 -14 498 -7 659 16 954 132 1766 659 2266 1468 163 264 318 632 401 952 79 307 117 688 96 982 -54 781 -356 1473 -881 2017 -509 527 -1157 853 -1895 952 -108 14 -482 26 -600 18z m391 -684 c357 -29 650 -108 959 -259 419 -206 770 -514 1030 -906 200 -301 323 -625 371 -979 23 -168 23 -508 0 -680 -163 -1209 -1161 -2141 -2372 -2217 -427 -26 -824 44 -1212 214 -107 47 -284 143 -339 183 -17 13 -39 24 -49 24 -9 0 -222 -65 -472 -145 -250 -80 -456 -145 -457 -143 -2 2 62 197 141 433 79 237 144 442 144 458 0 16 -18 53 -44 90 -418 599 -554 1426 -351 2127 45 152 82 245 155 390 200 391 505 732 880 982 473 316 1064 472 1616 428z"/><path d="M5323 5236 c-23 -7 -56 -23 -75 -34 -51 -32 -199 -190 -245 -262 -147 -229 -180 -534 -92 -832 67 -225 149 -397 299 -629 190 -292 313 -450 510 -653 296 -305 545 -476 927 -635 282 -118 490 -185 607 -197 81 -8 258 20 362 58 144 52 309 168 373 262 64 96 130 313 138 457 l6 95 -31 36 c-22 24 -112 78 -294 176 -432 232 -487 254 -555 218 -17 -8 -81 -73 -141 -143 -178 -207 -215 -243 -245 -243 -38 0 -287 127 -403 205 -135 92 -223 166 -334 281 -132 137 -275 333 -355 486 l-18 36 72 79 c95 101 134 162 172 268 39 108 37 141 -20 290 -51 133 -92 243 -163 434 -58 157 -101 221 -161 240 -57 17 -287 22 -334 7z"/></g></svg><?=$companyGsm?></a></div>
            <?php endif;?>
            <?=$allMenu->getShowTopMenu()?>
            <?php
            if($headerShowLanguages == 1){
                echo '<select id="languageSelect" class="language-select">';
                foreach ($languages as $language){
                    $selectedLangeuage = ($languageID == $language['dilid']) ? 'selected' : '';
                    echo '<option value="'.$language['dilkisa'].'" data-link="'.$language['link'].'" '.$selectedLangeuage.'>'.$language['dilad'].'</option>';
                }
                echo '</select>';
            }
            ?>
        </div>
        <?php if($headerShowSocialMedia==1):?>
        <div id="top-socialMedia">
            <?php
            if (!empty($companyFacebook)): ?>
                <div class="social-icon"><a href="<?=$companyFacebook?>" title="<?=_header_facebook_title?>" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="0 0 1280.000000 1275.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1275.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M1280 12735 c-308 -46 -560 -186 -772 -430 -185 -211 -338 -501 -459 -868 l-49 -149 3 -5006 2 -5007 23 -97 c62 -271 169 -458 366 -645 52 -49 136 -117 188 -152 207 -140 529 -272 851 -350 l127 -31 4983 3 4982 2 100 23 c595 137 971 631 1154 1516 l21 99 0 4776 0 4776 -25 105 c-191 788 -547 1255 -1068 1400 -189 53 100 50 -5320 49 -3998 -1 -5039 -4 -5107 -14z m8855 -1376 c39 -22 60 -46 74 -88 8 -24 11 -211 11 -634 0 -586 -1 -602 -21 -643 -15 -31 -32 -48 -63 -63 -41 -20 -58 -21 -485 -21 l-443 0 -61 -32 c-94 -50 -176 -137 -225 -238 -68 -139 -76 -203 -77 -575 0 -311 1 -321 22 -361 14 -26 36 -48 60 -60 36 -18 74 -19 666 -22 l627 -3 0 -594 0 -595 -618 0 -618 0 -44 -22 c-34 -18 -51 -35 -70 -73 l-25 -49 -3 -2908 -2 -2908 -943 0 c-897 0 -944 1 -983 19 -25 12 -50 33 -65 57 l-24 39 -3 2923 -2 2922 -600 0 -600 0 0 524 c0 347 4 534 11 553 13 35 45 70 84 91 26 15 88 18 505 22 377 4 480 8 500 19 37 20 71 53 83 81 7 16 13 170 18 415 4 276 11 425 23 510 55 399 163 713 333 970 92 139 283 329 423 421 292 194 645 302 1090 333 63 5 408 8 765 7 583 -1 653 -3 680 -17z"/></g></svg><span>Facebook</span></a></div>
            <?php endif;?>
            <?php
            if (!empty($companyInstagram)):?>
                <div class="social-icon">
                    <a href="<?=$companyInstagram?>" title="<?=_header_instagram_title?>" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M3035 12789 c-144 -13 -390 -55 -540 -94 -1169 -301 -2089 -1221 -2390 -2390 -40 -157 -81 -397 -95 -560 -6 -78 -10 -1256 -10 -3346 0 -3508 -2 -3362 55 -3675 157 -855 646 -1617 1358 -2118 498 -350 1058 -549 1677 -596 214 -16 6632 -9 6750 8 576 82 1009 238 1444 519 193 125 323 230 509 413 320 312 548 637 722 1033 134 302 211 587 267 977 17 118 24 6536 8 6750 -45 585 -225 1118 -541 1595 -503 760 -1282 1276 -2173 1440 -313 57 -163 55 -3695 54 -1785 -1 -3291 -6 -3346 -10z m6705 -1099 c927 -114 1678 -775 1905 -1675 69 -274 65 -29 65 -3620 0 -2856 -2 -3258 -15 -3362 -50 -379 -194 -738 -423 -1047 -96 -130 -328 -362 -458 -458 -309 -229 -668 -373 -1047 -423 -104 -13 -506 -15 -3362 -15 -3591 0 -3346 -4 -3620 65 -903 228 -1571 990 -1675 1914 -8 68 -10 1065 -8 3386 l3 3290 28 138 c162 811 724 1453 1494 1707 132 43 345 91 463 103 91 10 6571 6 6650 -3z"/><path d="M9785 10656 c-278 -65 -491 -272 -559 -547 -23 -93 -21 -255 4 -353 110 -432 583 -666 995 -493 111 46 253 160 316 253 175 258 173 598 -6 849 -89 125 -211 215 -362 268 -75 26 -106 30 -208 33 -78 3 -141 -1 -180 -10z"/><path d="M6195 9574 c-786 -62 -1494 -384 -2039 -930 -504 -503 -813 -1135 -913 -1864 -24 -177 -24 -596 1 -775 71 -521 231 -950 511 -1370 436 -655 1087 -1123 1837 -1320 470 -123 982 -137 1457 -39 612 126 1160 422 1606 869 507 507 815 1133 912 1855 22 167 25 590 5 750 -94 744 -404 1383 -922 1900 -500 499 -1139 811 -1855 905 -114 16 -494 27 -600 19z m400 -975 c428 -36 853 -207 1195 -479 113 -91 299 -283 386 -400 224 -300 361 -628 421 -1005 24 -154 24 -487 0 -644 -152 -971 -897 -1716 -1868 -1868 -153 -24 -499 -24 -647 0 -732 118 -1341 565 -1662 1220 -332 676 -298 1470 91 2114 177 292 460 575 752 752 399 241 869 350 1332 310z"/></g></svg><span>Instagram</span></a></div>
            <?php endif;?>
            <?php
            if (!empty($companyTwitter)):
                ?>
                <div class="social-icon"><a href="<?=$companyTwitter?>" title="<?=_header_twitter_title?>" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="350 350 300 250" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M5195 2781 c-189 -54 -340 -197 -413 -389 -23 -59 -26 -82 -26 -201 l-1 -134 -77 6 c-185 15 -393 74 -583 167 -204 100 -360 218 -508 383 -38 43 -72 77 -77 77 -4 0 -17 -22 -29 -49 -70 -164 -69 -360 3 -516 32 -69 106 -164 163 -210 l48 -38 -52 6 c-29 3 -81 18 -115 32 -35 15 -74 29 -87 32 -24 6 -24 6 -17 -60 24 -236 195 -449 414 -517 31 -10 50 -20 43 -24 -6 -4 -61 -6 -121 -5 -60 1 -113 0 -116 -4 -10 -10 16 -73 56 -136 101 -155 257 -256 435 -279 l60 -8 -55 -36 c-209 -135 -472 -208 -707 -195 -83 5 -104 3 -114 -9 -16 -19 -20 -16 131 -93 228 -117 457 -177 715 -187 590 -25 1099 212 1447 671 219 290 357 695 358 1053 0 50 4 92 9 92 28 0 293 281 279 295 -2 3 -27 -4 -55 -15 -51 -19 -179 -53 -243 -64 l-35 -6 30 22 c69 49 161 155 197 228 21 41 36 76 34 78 -2 2 -27 -8 -55 -22 -76 -39 -146 -66 -240 -92 l-84 -23 -51 45 c-60 53 -166 107 -251 129 -80 20 -233 18 -310 -4z"></path></g></svg><span>Twitter</span></a></div>
            <?php endif;?>
            <?php
            if (!empty($companyLinkedin)):
                ?>
                <div class="social-icon"><a href="<?=$companyLinkedin?>" title="<?=_header_linkedin_title?>" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" height="40px" width="40px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M2200 12793 c-234 -23 -378 -53 -565 -114 -341 -112 -655 -309 -930 -584 -389 -390 -609 -831 -687 -1380 -19 -132 -19 -8498 0 -8630 78 -548 299 -993 686 -1381 388 -387 833 -608 1381 -686 132 -19 8498 -19 8630 0 549 78 990 298 1380 687 389 390 609 831 687 1380 19 132 19 8498 0 8630 -53 375 -171 695 -361 980 -104 158 -189 262 -319 392 -384 386 -802 600 -1347 691 -92 15 -429 17 -4310 18 -2316 0 -4226 -1 -4245 -3z m1017 -2167 c318 -65 568 -253 703 -531 124 -255 137 -564 33 -823 -86 -218 -285 -421 -507 -522 -298 -135 -731 -133 -1025 6 -121 57 -198 113 -291 210 -126 132 -200 264 -242 431 -19 74 -23 114 -23 258 1 145 4 182 24 255 46 170 124 305 250 431 155 155 357 256 571 288 41 6 86 13 100 15 50 9 337 -4 407 -18z m5758 -2650 c257 -38 443 -94 650 -196 561 -274 932 -757 1094 -1423 106 -435 113 -590 109 -2692 l-3 -1580 -962 -3 -962 -2 -4 1687 c-3 1851 0 1747 -62 1993 -104 407 -337 643 -697 706 -128 22 -370 14 -482 -16 -164 -44 -296 -122 -429 -253 -105 -104 -174 -197 -237 -319 -63 -121 -87 -214 -100 -382 -6 -80 -10 -774 -10 -1773 l0 -1643 -959 0 -959 0 2 2895 1 2895 957 0 958 0 2 -407 3 -408 53 77 c116 172 308 374 462 489 274 205 597 328 965 368 121 13 486 5 610 -13z m-5075 -3001 l0 -2895 -962 2 -963 3 -3 2880 c-1 1584 0 2886 3 2893 3 9 206 12 965 12 l960 0 0 -2895z"/></g></svg><span>Linkedin</span></a></div>
            <?php endif;?>
            <?php
            if (!empty($companyYoutube)):
                ?>
                <div class="social-icon"><a href="<?=$companyYoutube?>" title="<?=_header_youtube_title?>" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M6120 12794 c-710 -48 -1205 -142 -1790 -342 -674 -231 -1331 -585 -1885 -1017 -302 -236 -722 -639 -952 -915 -851 -1021 -1339 -2202 -1469 -3555 -22 -224 -25 -826 -6 -1045 50 -570 150 -1054 320 -1560 398 -1179 1112 -2203 2067 -2965 920 -733 2023 -1197 3200 -1344 311 -39 425 -45 800 -45 372 0 552 11 840 50 1210 163 2339 663 3295 1459 195 162 608 574 767 765 851 1022 1339 2202 1469 3555 22 224 25 826 6 1045 -39 446 -108 834 -217 1225 -250 898 -677 1711 -1284 2440 -158 190 -572 604 -761 762 -1019 849 -2214 1343 -3550 1469 -154 14 -724 26 -850 18z m717 -1279 c787 -72 1483 -295 2158 -688 857 -500 1570 -1266 2005 -2152 275 -559 429 -1085 506 -1728 25 -202 30 -755 10 -973 -62 -673 -223 -1253 -511 -1839 -497 -1010 -1329 -1843 -2330 -2335 -559 -275 -1085 -429 -1728 -506 -202 -25 -755 -30 -973 -10 -799 73 -1491 294 -2169 689 -1158 676 -2032 1823 -2366 3102 -117 452 -163 812 -162 1290 0 532 67 993 214 1482 316 1049 947 1969 1803 2632 359 278 757 508 1166 674 457 186 967 312 1440 356 63 6 131 13 150 14 111 11 642 5 787 -8z"/><path d="M4980 8648 c-60 -31 -99 -92 -141 -220 l-34 -103 2 -640 c1 -352 7 -1234 13 -1960 11 -1226 13 -1326 30 -1400 67 -289 179 -342 506 -237 12 4 774 441 1694 972 1567 904 1677 969 1740 1032 92 91 134 174 128 255 -6 81 -41 146 -123 228 -67 67 -141 111 -1760 1051 -1186 689 -1709 988 -1755 1002 -151 48 -234 53 -300 20z"/></g></svg><span>Youtube</span></a></div>
            <?php endif;?>
        </div>
        <?php endif;?>
    </div>
</section>

<header class="header-container">
    <div class="header">
        <div class="logo-container">
            <a href="<?=$config->http.$config->hostDomain?>">
                <img src="<?=imgRoot."?imagePath=".$logoImg?>" alt="<?=$logoAlt?>">
            </a>
        </div>
        <?php if($headerShowMenu==1): ?>
            <nav id="mainMenu" class="nav-container">
                <input type="checkbox" id="mobileMainmenuViewer" role="button">
                <label for="mobileMainmenuViewer" class="mobileMainmenuViewerLabel">
                    <div></div><div></div><div></div>
                </label>
                <?=$allMenu->getShowMainMenu()?>
            </nav>
        <?php endif;?>
        <?php if($headerShowSearch==1): ?>
        <div class="product-search-container">
            <form action="/" method="get">
                <div class="close-search btn"><a href="#close"><svg class="svg-icon svg-icon-close" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 512 512" xml:space="preserve"><path d="M512,25.6L486.4,0L256,230.4L25.6,0L0,25.6l230.4,230.4L0,486.4L25.6,512l230.4-230.4L486.4,512l25.6-25.6 L281.6,256L512,25.6z"/></svg></a></div>
                <input type="hidden" name="languageID" value="<?=$languageID?>">
                <input type="text" name="q" placeholder="<?=_header_arama_title?>" />
                <button type="submit"><?=_header_ara_buton?></button>
            </form>
        </div>
        <?php endif;?>
        <?php if($siteType==1 || $isMemberRegistration==1): ?>
        <div class="shop-menu-container">
            <div class="svg-icon-container search"><a href="#search" title="<?=_header_arama_title?>"><svg class="svg-icon svg-icon-search" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 512 512" xml:space="preserve"><path d="M204.8,25.6v25.6c42.5,0,80.7,17.2,108.6,45c27.8,27.9,45,66.1,45,108.6c0,42.5-17.2,80.7-45,108.6   c-27.9,27.8-66.1,45-108.6,45c-42.5,0-80.7-17.2-108.6-45c-27.8-27.9-45-66.1-45-108.6c0-42.5,17.2-80.7,45-108.6   c27.9-27.8,66.1-45,108.6-45V25.6V0C91.7,0,0,91.7,0,204.8c0,113.1,91.7,204.8,204.8,204.8c113.1,0,204.8-91.7,204.8-204.8   C409.6,91.7,317.9,0,204.8,0V25.6z"/><path d="M504.5,468.3L350.9,314.7c-10-10-26.2-10-36.2,0c-10,10-10,26.2,0,36.2l153.6,153.6c10,10,26.2,10,36.2,0   C514.5,494.5,514.5,478.3,504.5,468.3"/></svg><span><?=_header_ara_buton?></span></a></div>
            <?php if($headerShowMemberIcon==1): ?>
            <div class="svg-icon-container member"><a href="<?=$memberLink?>" title="<?=_header_uyegiris_title?>"><svg  class="svg-icon svg-icon-member" width="40px" height="40px" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve"><path d="M491.1,425.8c0,26.5-8.5,47.8-24.7,63.1s-37.5,23-64,23H110.5c-27.3,0-48.6-7.7-64.9-23   c-16.2-15.4-24.7-36.7-24.7-63.1c0-11.9,0-23,0.9-34.1c0.9-11.1,2.6-23,4.3-36.7s5.1-24.7,8.5-35.8c3.4-11.1,8.5-22.2,14.5-32.4   c6-10.2,12.8-19.6,20.5-27.3c7.7-7.7,17.1-13.7,28.2-17.9s23.9-6.8,37.5-6.8c1.7,0,6.8,2.6,13.7,6.8s15.4,10.2,24.7,16.2   c9.4,6,21.3,11.1,35.8,16.2c14.5,5.1,29.9,6.8,44.4,6.8c14.5,0,29.9-2.6,44.4-6.8c14.5-5.1,26.5-10.2,35.8-16.2   c9.4-6,17.9-11.1,24.7-16.2c6.8-5.1,11.9-6.8,13.7-6.8c13.7,0,25.6,2.6,37.5,6.8s20.5,10.2,28.2,17.9c7.7,7.7,14.5,16.2,20.5,27.3   c6,11.1,11.1,21.3,14.5,32.4c3.4,11.1,6.8,23,8.5,35.8c1.7,12.8,4.3,24.7,4.3,36.7S491.1,413.9,491.1,425.8z M384.4,128   c0,35-12.8,65.7-37.5,90.5c-24.7,24.7-55.5,37.5-90.5,37.5s-65.7-12.8-90.5-37.5S128.4,163,128.4,128s12.8-65.7,37.5-90.5   S221.4,0,256.4,0s65.7,12.8,90.5,37.5C371.6,62.3,384.4,93,384.4,128z"/></svg><span><?=_header_uyegiris_title?></span></a></div>
            <?php endif;?>
            <?php if($headerShowFavIcon==1): ?>
            <div class="svg-icon-container favorites"><a href="<?=$favoriteLink?>" title="<?=_header_favori_title?>"><svg class="svg-icon svg-icon-favorites" width="40px" height="40px" x="0px" y="0px" viewBox="0 0 512 512"  xml:space="preserve"><path d="M490.4,231.9C447.6,314.1,333.1,426,268.4,485.6c-6.9,6.3-17.5,6.3-24.5,0C178.9,426,64.4,314.1,21.6,231.9   c-94-181,143-301.6,234.4-120.7C347.4-69.8,584.3,50.9,490.4,231.9z"/></svg><span><?=_header_favori_title?></span></a></div>
            <?php endif;?>
            <?php if($headerShowBasketIcon==1): ?>
            <div class="svg-icon-container basket"><a href="<?=$cartLink?>" title="<?=_header_sepetim_yazi?>"><label class="basket-count"><?=$cartCount?></label><svg class="svg-icon svg-icon-basket" width="40px" height="40px" x="0px" y="0px" viewBox="0 0 512 512"  xml:space="preserve"><path xmlns="http://www.w3.org/2000/svg" id="XMLID_10_" d="M480.3,224.8c8.4,0,16.8,2.8,22.3,9.3c5.6,6.5,9.3,14,9.3,22.3c0,8.4-2.8,16.8-9.3,22.3   c-6.5,5.6-14,9.3-22.3,9.3h-3.7l-28.9,165.7c-0.9,7.4-4.7,14-11.2,18.6s-13,7.4-20.5,7.4H95.9c-7.4,0-14.9-2.8-20.5-7.4   c-5.6-4.7-9.3-11.2-11.2-18.6L35.4,288.1h-3.7c-8.4,0-16.8-2.8-22.3-9.3S0,264.8,0,256.5s2.8-16.8,9.3-22.3   c6.5-5.6,14-9.3,22.3-9.3C31.7,224.8,480.3,224.8,480.3,224.8z M121,424c4.7,0,8.4-1.9,11.2-5.6c2.8-3.7,4.7-7.4,3.7-11.2   L128.5,303c0-4.7-1.9-8.4-5.6-11.2s-7.4-4.7-11.2-3.7c-4.7,0-8.4,1.9-11.2,5.6c-2.8,3.7-4.7,7.4-3.7,11.2l8.4,104.3   c0,3.7,1.9,7.4,5.6,10.2c2.8,2.8,6.5,4.7,11.2,4.7C121.9,424,121,424,121,424z M224.3,408.2V304.9c0-4.7-1.9-8.4-4.7-11.2   c-2.8-2.8-6.5-4.7-11.2-4.7c-4.7,0-8.4,1.9-11.2,4.7s-4.7,6.5-4.7,11.2v104.3c0,4.7,1.9,8.4,4.7,11.2c2.8,2.8,6.5,4.7,11.2,4.7   c4.7,0,8.4-1.9,11.2-4.7C222.5,417.5,224.3,412.9,224.3,408.2z M320.2,408.2V304.9c0-4.7-1.9-8.4-4.7-11.2   c-2.8-2.8-6.5-4.7-11.2-4.7s-8.4,1.9-11.2,4.7c-2.8,2.8-4.7,6.5-4.7,11.2v104.3c0,4.7,1.9,8.4,4.7,11.2c2.8,2.8,6.5,4.7,11.2,4.7   s8.4-1.9,11.2-4.7C318.4,417.5,320.2,412.9,320.2,408.2z M407.7,410.1l8.4-104.3c0-4.7-0.9-8.4-3.7-11.2s-6.5-5.6-11.2-5.6   c-4.7,0-8.4,0.9-11.2,3.7s-5.6,6.5-5.6,11.2l-8.4,104.3c0,4.7,0.9,8.4,3.7,11.2s6.5,4.7,11.2,4.7h0.9c3.7,0,7.4-1.9,11.2-4.7   C405.9,417.5,407.7,413.8,407.7,410.1z M119.2,105.7L95.9,209H63.3L88.4,98.2c2.8-14.9,10.2-27,22.3-36.3c12.1-9.3,25.1-14,40-14   h41.9c0-4.7,1.9-8.4,4.7-11.2c2.8-2.8,6.5-4.7,11.2-4.7h95.9c4.7,0,8.4,1.9,11.2,4.7c2.8,2.8,4.7,6.5,4.7,11.2h41.9   c14.9,0,27.9,4.7,40,14s18.6,21.4,22.3,36.3l25.1,109.8H417l-23.3-103.3c-1.9-7.4-5.6-13-11.2-17.7c-5.6-4.7-12.1-7.4-19.5-7.4   h-41.9c0,4.7-1.9,8.4-4.7,11.2c-2.8,2.8-6.5,4.7-11.2,4.7h-97.7c-4.7,0-8.4-1.9-11.2-4.7c-2.8-2.8-4.7-6.5-4.7-11.2h-41.9   c-7.4,0-14,2.8-19.5,7.4C124.7,91.7,121,98.2,119.2,105.7z"/></svg><span><?=_header_sepetim_yazi?></span></a></div>
            <?php endif;?>
        </div>
        <?php endif;?>

    </div>
</header>