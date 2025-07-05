<?php
class Page
{
    private Database $db;
    private $casper;
    private JSON $json;
    private $siteConfig;

    public function __construct($db,$session)
    {
        $this->db = $db;
        $this->casper = $session->getCasper();
        $this->json = $this->casper->getConfig()->Json;
        $this->siteConfig = $this->casper->getSiteConfig();
    }

    public function getAllPages()
    {
        $sql = "SELECT 
                sayfa.*,
                seo.link, seo.baslik, seo.aciklama,
                CONCAT(resimklasor.resimklasorad, '/', resim.resim) as resim_url,
                GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', ') as kategoriler 
                FROM sayfa
                LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                LEFT JOIN seo ON sayfa.benzersizid = seo.benzersizid
                GROUP BY sayfa.sayfaid;";

        return $this->db->select($sql);
    }

    public function getPageById($id,$uniqID="")
    {
        if($id=="" && $uniqID==""){
            return [];
        }

        $where = "";

        //id varsa uniqid öğrenip devam edelim
        if(is_numeric($id) && $id>0)
        {
            $result = $this->db->select("SELECT benzersizid FROM sayfa WHERE sayfaid = :sayfaid",["sayfaid" => $id]);
            if ($result && count($result) > 0) {

                $uniqID = $result[0]['benzersizid'];

            } else {
                Log::write("Sayfa bulunamadı: $id","info");
                return [];
            }
        }

        if($uniqID!=""){
            $where = "WHERE sayfa.benzersizid = :uniqID";
            $param = ['uniqID' => $uniqID];
        }
        else{
            return [];
        }

        $sql = "
            SELECT 
                sayfa.*,
                GROUP_CONCAT(CONCAT(resimklasor.resimklasorad, '/', resim.resim) SEPARATOR ', ') as resim_url,
                GROUP_CONCAT(DISTINCT kategori.kategoriad SEPARATOR ', ') as kategoriler,kategori.kategoriid 
            FROM sayfa
                LEFT JOIN sayfalisteresim ON sayfa.sayfaid = sayfalisteresim.sayfaid
                LEFT JOIN resim ON sayfalisteresim.resimid = resim.resimid
                LEFT JOIN resimklasor ON resim.resimklasorid = resimklasor.resimklasorid
                LEFT JOIN sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                LEFT JOIN kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
            $where
        ";

        $data = $this->json->readJson(["Page",$uniqID]);

        if(empty($data)){

            $data = $this->db->select($sql,$param);

            if($data && count($data)>0){
                $data = $data[0];
                $this->json->createJson(["Page",$uniqID],$data);
            }
            else{
                return [];
            }
        }

        if (isset($data['sayfaid'])&&$data['sayfaid']!="") {
            $pageType = $data['sayfatip'];
            $pageFiles = $this->getPageFiles($data['sayfaid']);
            $pageGallery = $this->getPageGallery($data['sayfaid']);
            $pageVideos = $this->getPageVideos($data['sayfaid']);

            $data['pageFiles'] = $pageFiles;
            $data['pageGallery'] = $pageGallery;
            $data['pageVideos'] = $pageVideos;            if($pageType == 1){
                $contactForm = $this->getContactForm();

                $data['sayfaicerik'] = str_replace("[iletisimform]",$contactForm,$data['sayfaicerik']);

                $socialMedia = $this->getContactSosialMediaStyle($data);
                $data['sayfaicerik'] = str_replace("[sosyalmedya]",$socialMedia,$data['sayfaicerik']);

                $data = $this->getContactStyle($data);
            }              elseif($pageType == 29){
                $appointmentForm = $this->getAppointmentForm();
                $data['sayfaicerik'] = str_replace("[randevuform]",$appointmentForm,$data['sayfaicerik']);
                $data = $this->getContactStyle($data);
            }
            elseif($pageType == 10||$pageType == 12||$pageType == 13||$pageType == 14||$pageType == 15||$pageType == 18||$pageType == 25){
                $data = $this->getFirmInf($data);
            }
        }
        else {
            $data['sayfaad']="404";
            $data['sayfaicerik'] =_body_sayfa_bulunamadi;
            $data['sayfatip'] = 404;
            $data['kategoriid'] = 0;
            //Log::write("Sayfa bulunamadı: $id - $uniqID","info");
        }
        return $data;
    }

    public function getPageUniqIDByID($id)
    {
        $sql = "
            SELECT 
                benzersizid as pageUniqID
            FROM 
                sayfa
            WHERE 
                sayfaid = :id
        ";
        return $this->db->select($sql, ['id' => $id]);
    }
    public function getContactForm()
    {
        $contactForm ='
            <form id="contactForm" action="/?/control/form/post/contactForm" method="post" >
                <h2>'._iletisim_baslik_yazi.'</h2>
                <input type="hidden" name="action" value="contactForm">
                <input type="hidden" name="websites" value="">
                <div class="form-group">
                    <label for="namesurname">'._iletisim_form_adsoyad_yazi.'</label>
                    <input type="text" name="namesurname" id="namesurname" placeholder="'._iletisim_form_adsoyad_yazi.'" required>
                </div>
                <div class="form-group">
                    <label for="email">'._iletisim_form_eposta_yazi.'</label>
                    <input type="email" name="email" id="email" placeholder="'._iletisim_form_eposta_yazi.'" required>
                </div>
                <div class="form-group">
                    <label for="phone">'._iletisim_form_telefon_yazi.'</label>
                    <input type="tel" name="phone" id="phone" placeholder="'._iletisim_form_telefon_yazi.'" required>
                </div>
                <div class="form-group">
                    <label for="message">'._iletisim_form_mesajiniz_yazi.'</label>
                    <textarea name="message" id="message" placeholder="'._iletisim_form_mesajiniz_yazi.'" required></textarea>
                </div>
                <input type="hidden" name="cf-turnstile-response" id="cf-token-contact-form">
                <button class="btn btn-primary" type="submit">'._iletisim_form_buton_yazi.'</button>
            </form>
        ';
        return $contactForm;
    }

    public function getContactStyle($data){
        $companyInfo = $this->siteConfig['companySettings'];
        //print_r($data);exit();
        $companyShortName = '<p>'.$companyInfo['ayarfirmakisaad'].'</p>';
        $companyName = '<h2>'.$companyInfo['ayarfirmaad'].'</h2>';
        $companyAddress = '<p> <svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" width="20px" version="1.1" id="Capa_1" viewBox="0 0 255.856 255.856" xml:space="preserve"><g>	<path style="fill:#000002;" d="M127.928,38.8c-30.75,0-55.768,25.017-55.768,55.767s25.018,55.767,55.768,55.767   s55.768-25.017,55.768-55.767S158.678,38.8,127.928,38.8z M127.928,135.333c-22.479,0-40.768-18.288-40.768-40.767   S105.449,53.8,127.928,53.8s40.768,18.288,40.768,40.767S150.408,135.333,127.928,135.333z"/><path style="fill:#000002;" d="M127.928,0C75.784,0,33.362,42.422,33.362,94.566c0,30.072,25.22,74.875,40.253,98.904   c9.891,15.809,20.52,30.855,29.928,42.365c15.101,18.474,20.506,20.02,24.386,20.02c3.938,0,9.041-1.547,24.095-20.031   c9.429-11.579,20.063-26.616,29.944-42.342c15.136-24.088,40.527-68.971,40.527-98.917C222.495,42.422,180.073,0,127.928,0z    M171.569,181.803c-19.396,31.483-37.203,52.757-43.73,58.188c-6.561-5.264-24.079-26.032-43.746-58.089   c-22.707-37.015-35.73-68.848-35.73-87.336C48.362,50.693,84.055,15,127.928,15c43.873,0,79.566,35.693,79.566,79.566   C207.495,112.948,194.4,144.744,171.569,181.803z"/></g></svg>'.$companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." <br>".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'].'</p>';
        $companyPhone = '<p><a href="tel:'.$companyInfo['ayarfirmatelefon'].'"><svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M6145 12794 c-216 -13 -391 -28 -530 -45 -995 -122 -1927 -467 -2760 -1022 -907 -604 -1648 -1433 -2146 -2402 -395 -769 -615 -1549 -690 -2450 -17 -193 -17 -757 0 -950 75 -901 295 -1681 690 -2450 610 -1187 1579 -2156 2766 -2766 769 -395 1549 -615 2450 -690 193 -17 757 -17 950 0 901 75 1681 295 2450 690 1187 610 2156 1579 2766 2766 395 769 615 1549 690 2450 17 193 17 757 0 950 -75 901 -295 1681 -690 2450 -610 1187 -1579 2156 -2766 2766 -753 387 -1531 610 -2390 684 -164 15 -666 27 -790 19z m739 -779 c1310 -112 2519 -671 3464 -1599 980 -963 1561 -2210 1673 -3591 15 -193 15 -657 0 -850 -110 -1350 -664 -2567 -1605 -3523 -965 -981 -2206 -1559 -3591 -1673 -193 -16 -657 -16 -850 0 -1386 114 -2628 692 -3591 1672 -943 961 -1493 2167 -1605 3524 -16 193 -16 657 0 850 115 1388 693 2628 1672 3591 878 862 1988 1408 3189 1568 416 55 832 66 1244 31z"></path><path d="M5060 10738 c-54 -15 -679 -379 -716 -417 -83 -84 -102 -207 -50 -309 54 -105 1149 -1998 1175 -2032 58 -73 186 -116 276 -91 46 12 662 365 715 409 68 57 105 179 81 267 -6 22 -274 497 -596 1055 -474 822 -594 1023 -631 1057 -68 64 -164 87 -254 61z"></path><path d="M3942 9867 c-214 -130 -321 -208 -435 -317 -334 -319 -423 -636 -366 -1295 82 -942 549 -2201 1249 -3365 657 -1092 1446 -1996 2175 -2492 375 -255 608 -343 910 -343 202 0 370 40 606 145 110 50 365 185 374 199 3 6 -1137 1992 -1198 2088 -6 10 -17 8 -47 -7 -69 -35 -183 -69 -272 -80 -327 -40 -714 148 -1073 522 -444 462 -796 1143 -905 1753 -81 448 -20 809 175 1038 33 39 144 132 166 139 8 3 -217 401 -590 1049 -332 574 -606 1047 -610 1051 -4 5 -75 -34 -159 -85z"></path><path d="M8220 5330 c-55 -7 -46 -3 -413 -214 -164 -94 -314 -186 -333 -204 -72 -69 -102 -185 -72 -273 17 -50 1155 -2026 1197 -2078 71 -90 212 -117 326 -62 86 41 614 347 655 380 56 45 90 120 90 202 0 37 -5 81 -12 96 -34 80 -1175 2043 -1206 2075 -61 64 -141 91 -232 78z"></path></g></svg>'.$companyInfo['ayarfirmatelefon'].'</a></p>';
        $companyEmail = '<p><a href="mailto:'.$companyInfo['ayarfirmaeposta'].'"><svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" fill="#000000" height="20px" width="20px" id="Capa_1" viewBox="0 0 75.294 75.294" xml:space="preserve"><g><path d="M66.097,12.089h-56.9C4.126,12.089,0,16.215,0,21.286v32.722c0,5.071,4.126,9.197,9.197,9.197h56.9   c5.071,0,9.197-4.126,9.197-9.197V21.287C75.295,16.215,71.169,12.089,66.097,12.089z M61.603,18.089L37.647,33.523L13.691,18.089   H61.603z M66.097,57.206h-56.9C7.434,57.206,6,55.771,6,54.009V21.457l29.796,19.16c0.04,0.025,0.083,0.042,0.124,0.065   c0.043,0.024,0.087,0.047,0.131,0.069c0.231,0.119,0.469,0.215,0.712,0.278c0.025,0.007,0.05,0.01,0.075,0.016   c0.267,0.063,0.537,0.102,0.807,0.102c0.001,0,0.002,0,0.002,0c0.002,0,0.003,0,0.004,0c0.27,0,0.54-0.038,0.807-0.102   c0.025-0.006,0.05-0.009,0.075-0.016c0.243-0.063,0.48-0.159,0.712-0.278c0.044-0.022,0.088-0.045,0.131-0.069   c0.041-0.023,0.084-0.04,0.124-0.065l29.796-19.16v32.551C69.295,55.771,67.86,57.206,66.097,57.206z"></path></g></svg>'.$companyInfo['ayarfirmaeposta'].'</a></p>';
        $companyWhatsapp = '<p><a href="https://wa.me/'.$companyInfo['ayarfirmagsm'].'"><svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="340 -40 640.000000 640.000000" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M6255 6844 c-540 -35 -1107 -229 -1555 -532 -473 -320 -848 -752 -1091 -1256 -133 -276 -216 -536 -273 -856 -43 -240 -52 -602 -22 -880 40 -374 177 -822 362 -1188 l53 -103 -123 -367 c-68 -202 -191 -570 -274 -818 -84 -249 -152 -459 -152 -469 0 -9 13 -22 29 -28 26 -10 29 -14 24 -45 -6 -32 -5 -34 18 -27 41 13 936 298 1314 420 198 63 368 115 378 115 9 0 52 -17 95 -39 366 -184 756 -294 1171 -332 164 -14 498 -7 659 16 954 132 1766 659 2266 1468 163 264 318 632 401 952 79 307 117 688 96 982 -54 781 -356 1473 -881 2017 -509 527 -1157 853 -1895 952 -108 14 -482 26 -600 18z m391 -684 c357 -29 650 -108 959 -259 419 -206 770 -514 1030 -906 200 -301 323 -625 371 -979 23 -168 23 -508 0 -680 -163 -1209 -1161 -2141 -2372 -2217 -427 -26 -824 44 -1212 214 -107 47 -284 143 -339 183 -17 13 -39 24 -49 24 -9 0 -222 -65 -472 -145 -250 -80 -456 -145 -457 -143 -2 2 62 197 141 433 79 237 144 442 144 458 0 16 -18 53 -44 90 -418 599 -554 1426 -351 2127 45 152 82 245 155 390 200 391 505 732 880 982 473 316 1064 472 1616 428z"></path><path d="M5323 5236 c-23 -7 -56 -23 -75 -34 -51 -32 -199 -190 -245 -262 -147 -229 -180 -534 -92 -832 67 -225 149 -397 299 -629 190 -292 313 -450 510 -653 296 -305 545 -476 927 -635 282 -118 490 -185 607 -197 81 -8 258 20 362 58 144 52 309 168 373 262 64 96 130 313 138 457 l6 95 -31 36 c-22 24 -112 78 -294 176 -432 232 -487 254 -555 218 -17 -8 -81 -73 -141 -143 -178 -207 -215 -243 -245 -243 -38 0 -287 127 -403 205 -135 92 -223 166 -334 281 -132 137 -275 333 -355 486 l-18 36 72 79 c95 101 134 162 172 268 39 108 37 141 -20 290 -51 133 -92 243 -163 434 -58 157 -101 221 -161 240 -57 17 -287 22 -334 7z"></path></g></svg>'.$companyInfo['ayarfirmagsm'].'</a></p>';
        $companyLocationMap = $companyInfo['ayarfirmaharita'];

        $pageContent = $data['sayfaicerik'];
        $pageContent = str_replace("[firmaad]",$companyName,$pageContent);
        $pageContent = str_replace("[adres]",$companyAddress,$pageContent);
        $pageContent = str_replace("[telefon]",$companyPhone,$pageContent);
        $pageContent = str_replace("[mail]",$companyEmail,$pageContent);
        $pageContent = str_replace("[whatsapp]",$companyWhatsapp,$pageContent);
        $pageContent = str_replace("[harita]",$companyLocationMap,$pageContent);

        $pageContent = str_replace("[firmaharita]",$companyLocationMap,$pageContent);
        $pageContent = str_replace("[firmahahalle]",$companyInfo['ayarfirmamahalle'],$pageContent);
        $pageContent = str_replace("[firmasemt]",$companyInfo['ayarfirmasemt'],$pageContent);
        $pageContent = str_replace("[firmapostakod]",$companyInfo['ayarfirmapostakod'],$pageContent);
        $pageContent = str_replace("[firmailce]",$companyInfo['ayarfirmailce'],$pageContent);
        $pageContent = str_replace("[firmasehir]",$companyInfo['ayarfirmasehir'],$pageContent);
        $pageContent = str_replace("[firmaulke]",$companyInfo['ayarfirmaulke'],$pageContent);

        $data['sayfaicerik'] = $pageContent;
        return $data;
    }

    public function getContactSosialMediaStyle($data)
    {
        $socialMediaSettings = $this->siteConfig['socialMediaSettings'];

        $facebook = $socialMediaSettings['facebook'] ?? "";
        if(!empty($facebook)){
            $facebook = '<a href="'.$facebook.'" target="_blank">
                <svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 1280.000000 1275.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1275.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M1280 12735 c-308 -46 -560 -186 -772 -430 -185 -211 -338 -501 -459 -868 l-49 -149 3 -5006 2 -5007 23 -97 c62 -271 169 -458 366 -645 52 -49 136 -117 188 -152 207 -140 529 -272 851 -350 l127 -31 4983 3 4982 2 100 23 c595 137 971 631 1154 1516 l21 99 0 4776 0 4776 -25 105 c-191 788 -547 1255 -1068 1400 -189 53 100 50 -5320 49 -3998 -1 -5039 -4 -5107 -14z m8855 -1376 c39 -22 60 -46 74 -88 8 -24 11 -211 11 -634 0 -586 -1 -602 -21 -643 -15 -31 -32 -48 -63 -63 -41 -20 -58 -21 -485 -21 l-443 0 -61 -32 c-94 -50 -176 -137 -225 -238 -68 -139 -76 -203 -77 -575 0 -311 1 -321 22 -361 14 -26 36 -48 60 -60 36 -18 74 -19 666 -22 l627 -3 0 -594 0 -595 -618 0 -618 0 -44 -22 c-34 -18 -51 -35 -70 -73 l-25 -49 -3 -2908 -2 -2908 -943 0 c-897 0 -944 1 -983 19 -25 12 -50 33 -65 57 l-24 39 -3 2923 -2 2922 -600 0 -600 0 0 524 c0 347 4 534 11 553 13 35 45 70 84 91 26 15 88 18 505 22 377 4 480 8 500 19 37 20 71 53 83 81 7 16 13 170 18 415 4 276 11 425 23 510 55 399 163 713 333 970 92 139 283 329 423 421 292 194 645 302 1090 333 63 5 408 8 765 7 583 -1 653 -3 680 -17z"></path></g></svg> Facebook
                </a>';
        }

        $twitter = $socialMediaSettings['twitter'] ?? "";
        if (!empty($twitter)){
            $twitter = '<a href="'.$twitter.'" target="_blank">
                <svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="350 350 300 250" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M5195 2781 c-189 -54 -340 -197 -413 -389 -23 -59 -26 -82 -26 -201 l-1 -134 -77 6 c-185 15 -393 74 -583 167 -204 100 -360 218 -508 383 -38 43 -72 77 -77 77 -4 0 -17 -22 -29 -49 -70 -164 -69 -360 3 -516 32 -69 106 -164 163 -210 l48 -38 -52 6 c-29 3 -81 18 -115 32 -35 15 -74 29 -87 32 -24 6 -24 6 -17 -60 24 -236 195 -449 414 -517 31 -10 50 -20 43 -24 -6 -4 -61 -6 -121 -5 -60 1 -113 0 -116 -4 -10 -10 16 -73 56 -136 101 -155 257 -256 435 -279 l60 -8 -55 -36 c-209 -135 -472 -208 -707 -195 -83 5 -104 3 -114 -9 -16 -19 -20 -16 131 -93 228 -117 457 -177 715 -187 590 -25 1099 212 1447 671 219 290 357 695 358 1053 0 50 4 92 9 92 28 0 293 281 279 295 -2 3 -27 -4 -55 -15 -51 -19 -179 -53 -243 -64 l-35 -6 30 22 c69 49 161 155 197 228 21 41 36 76 34 78 -2 2 -27 -8 -55 -22 -76 -39 -146 -66 -240 -92 l-84 -23 -51 45 c-60 53 -166 107 -251 129 -80 20 -233 18 -310 -4z"></path></g></svg> Twitter
                </a>';
        }

        $instagram = $socialMediaSettings['instagram'] ?? "";
        if (!empty($instagram)){
            $instagram = '<a href="'.$instagram.'" target="_blank">
                <svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M3035 12789 c-144 -13 -390 -55 -540 -94 -1169 -301 -2089 -1221 -2390 -2390 -40 -157 -81 -397 -95 -560 -6 -78 -10 -1256 -10 -3346 0 -3508 -2 -3362 55 -3675 157 -855 646 -1617 1358 -2118 498 -350 1058 -549 1677 -596 214 -16 6632 -9 6750 8 576 82 1009 238 1444 519 193 125 323 230 509 413 320 312 548 637 722 1033 134 302 211 587 267 977 17 118 24 6536 8 6750 -45 585 -225 1118 -541 1595 -503 760 -1282 1276 -2173 1440 -313 57 -163 55 -3695 54 -1785 -1 -3291 -6 -3346 -10z m6705 -1099 c927 -114 1678 -775 1905 -1675 69 -274 65 -29 65 -3620 0 -2856 -2 -3258 -15 -3362 -50 -379 -194 -738 -423 -1047 -96 -130 -328 -362 -458 -458 -309 -229 -668 -373 -1047 -423 -104 -13 -506 -15 -3362 -15 -3591 0 -3346 -4 -3620 65 -903 228 -1571 990 -1675 1914 -8 68 -10 1065 -8 3386 l3 3290 28 138 c162 811 724 1453 1494 1707 132 43 345 91 463 103 91 10 6571 6 6650 -3z"></path><path d="M9785 10656 c-278 -65 -491 -272 -559 -547 -23 -93 -21 -255 4 -353 110 -432 583 -666 995 -493 111 46 253 160 316 253 175 258 173 598 -6 849 -89 125 -211 215 -362 268 -75 26 -106 30 -208 33 -78 3 -141 -1 -180 -10z"></path><path d="M6195 9574 c-786 -62 -1494 -384 -2039 -930 -504 -503 -813 -1135 -913 -1864 -24 -177 -24 -596 1 -775 71 -521 231 -950 511 -1370 436 -655 1087 -1123 1837 -1320 470 -123 982 -137 1457 -39 612 126 1160 422 1606 869 507 507 815 1133 912 1855 22 167 25 590 5 750 -94 744 -404 1383 -922 1900 -500 499 -1139 811 -1855 905 -114 16 -494 27 -600 19z m400 -975 c428 -36 853 -207 1195 -479 113 -91 299 -283 386 -400 224 -300 361 -628 421 -1005 24 -154 24 -487 0 -644 -152 -971 -897 -1716 -1868 -1868 -153 -24 -499 -24 -647 0 -732 118 -1341 565 -1662 1220 -332 676 -298 1470 91 2114 177 292 460 575 752 752 399 241 869 350 1332 310z"></path></g></svg> Instagram
                </a>';
        }

        $linkedin = $socialMediaSettings['linkedin'] ?? "";
        if (!empty($linkedin)){
            $linkedin = '<a href="'.$linkedin.'" target="_blank">
                <svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M2200 12793 c-234 -23 -378 -53 -565 -114 -341 -112 -655 -309 -930 -584 -389 -390 -609 -831 -687 -1380 -19 -132 -19 -8498 0 -8630 78 -548 299 -993 686 -1381 388 -387 833 -608 1381 -686 132 -19 8498 -19 8630 0 549 78 990 298 1380 687 389 390 609 831 687 1380 19 132 19 8498 0 8630 -53 375 -171 695 -361 980 -104 158 -189 262 -319 392 -384 386 -802 600 -1347 691 -92 15 -429 17 -4310 18 -2316 0 -4226 -1 -4245 -3z m1017 -2167 c318 -65 568 -253 703 -531 124 -255 137 -564 33 -823 -86 -218 -285 -421 -507 -522 -298 -135 -731 -133 -1025 6 -121 57 -198 113 -291 210 -126 132 -200 264 -242 431 -19 74 -23 114 -23 258 1 145 4 182 24 255 46 170 124 305 250 431 155 155 357 256 571 288 41 6 86 13 100 15 50 9 337 -4 407 -18z m5758 -2650 c257 -38 443 -94 650 -196 561 -274 932 -757 1094 -1423 106 -435 113 -590 109 -2692 l-3 -1580 -962 -3 -962 -2 -4 1687 c-3 1851 0 1747 -62 1993 -104 407 -337 643 -697 706 -128 22 -370 14 -482 -16 -164 -44 -296 -122 -429 -253 -105 -104 -174 -197 -237 -319 -63 -121 -87 -214 -100 -382 -6 -80 -10 -774 -10 -1773 l0 -1643 -959 0 -959 0 2 2895 1 2895 957 0 958 0 2 -407 3 -408 53 77 c116 172 308 374 462 489 274 205 597 328 965 368 121 13 486 5 610 -13z m-5075 -3001 l0 -2895 -962 2 -963 3 -3 2880 c-1 1584 0 2886 3 2893 3 9 206 12 965 12 l960 0 0 -2895z"></path></g></svg> Linkedin
                </a>';
        }

        $youtube = $socialMediaSettings['youtube'] ?? "";
        if (!empty($youtube)){
            $youtube = '<a href="'.$youtube.'" target="_blank">
                <svg class="contact-svg" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none"><path d="M6120 12794 c-710 -48 -1205 -142 -1790 -342 -674 -231 -1331 -585 -1885 -1017 -302 -236 -722 -639 -952 -915 -851 -1021 -1339 -2202 -1469 -3555 -22 -224 -25 -826 -6 -1045 50 -570 150 -1054 320 -1560 398 -1179 1112 -2203 2067 -2965 920 -733 2023 -1197 3200 -1344 311 -39 425 -45 800 -45 372 0 552 11 840 50 1210 163 2339 663 3295 1459 195 162 608 574 767 765 851 1022 1339 2202 1469 3555 22 224 25 826 6 1045 -39 446 -108 834 -217 1225 -250 898 -677 1711 -1284 2440 -158 190 -572 604 -761 762 -1019 849 -2214 1343 -3550 1469 -154 14 -724 26 -850 18z m717 -1279 c787 -72 1483 -295 2158 -688 857 -500 1570 -1266 2005 -2152 275 -559 429 -1085 506 -1728 25 -202 30 -755 10 -973 -62 -673 -223 -1253 -511 -1839 -497 -1010 -1329 -1843 -2330 -2335 -559 -275 -1085 -429 -1728 -506 -202 -25 -755 -30 -973 -10 -799 73 -1491 294 -2169 689 -1158 676 -2032 1823 -2366 3102 -117 452 -163 812 -162 1290 0 532 67 993 214 1482 316 1049 947 1969 1803 2632 359 278 757 508 1166 674 457 186 967 312 1440 356 63 6 131 13 150 14 111 11 642 5 787 -8z"></path><path d="M4980 8648 c-60 -31 -99 -92 -141 -220 l-34 -103 2 -640 c1 -352 7 -1234 13 -1960 11 -1226 13 -1326 30 -1400 67 -289 179 -342 506 -237 12 4 774 441 1694 972 1567 904 1677 969 1740 1032 92 91 134 174 128 255 -6 81 -41 146 -123 228 -67 67 -141 111 -1760 1051 -1186 689 -1709 988 -1755 1002 -151 48 -234 53 -300 20z"></path></g></svg> Youtube
                </a>';
        }

        //hepsi boş ise boş dön
        if(empty($facebook) && empty($twitter) && empty($instagram) && empty($linkedin) && empty($youtube)){
            return "";
        }
        return '<div class="contactSocialMedia"><h2>'._iletisim_bizi_takip_edin_yazi.'</h2>'.$facebook.$twitter.$instagram.$linkedin.$youtube.'</div>';

    }

    public function getFirmInf($data){
        $companyInfo = $this->siteConfig['companySettings'];
        $domain = $this->siteConfig['generalSettings']['domain'];

        $pageContent = $data['sayfaicerik'];
        $pageContent = str_replace("[firmaunvan]",$companyInfo['ayarfirmaad'],$pageContent);
        $pageContent = str_replace("[firmatelefon]",$companyInfo['ayarfirmatelefon'],$pageContent);
        $pageContent = str_replace("[firmaeposta]",$companyInfo['ayarfirmaeposta'],$pageContent);
        $pageContent = str_replace("[firmafaks]",$companyInfo['ayarfirmafaks'],$pageContent);
        $pageContent = str_replace("[firmaulke]",$companyInfo['ayarfirmaulke'],$pageContent);
        $pageContent = str_replace("[firmasehir]",$companyInfo['ayarfirmasehir'],$pageContent);
        $pageContent = str_replace("[firmailce]",$companyInfo['ayarfirmailce'],$pageContent);
        $pageContent = str_replace("[firmasemt]",$companyInfo['ayarfirmasemt'],$pageContent);
        $pageContent = str_replace("[firmamahalle]",$companyInfo['ayarfirmamahalle'],$pageContent);
        $pageContent = str_replace("[firmaadres]",$companyInfo['ayarfirmaadres'],$pageContent);

        $pageContent = str_replace("[companytitle]",$companyInfo['ayarfirmaad'],$pageContent);
        $pageContent = str_replace("[companyphone]",$companyInfo['ayarfirmatelefon'],$pageContent);
        $pageContent = str_replace("[companyemail]",$companyInfo['ayarfirmaeposta'],$pageContent);
        $pageContent = str_replace("[companyfax]",$companyInfo['ayarfirmafaks'],$pageContent);
        $pageContent = str_replace("[companycountry]",$companyInfo['ayarfirmaulke'],$pageContent);
        $pageContent = str_replace("[companycity]",$companyInfo['ayarfirmasehir'],$pageContent);
        $pageContent = str_replace("[companydistrict]",$companyInfo['ayarfirmailce'],$pageContent);
        //$pageContent = str_replace("[firmasemt]",$companyInfo['ayarfirmasemt'],$pageContent);
        //$pageContent = str_replace("[firmamahalle]",$companyInfo['ayarfirmamahalle'],$pageContent);
        $pageContent = str_replace("[companyaddress]",$companyInfo['ayarfirmaadres'],$pageContent);

        $pageContent = str_replace("[sitedomain]",$domain,$pageContent);

        $data['sayfaicerik'] = $pageContent;
        return $data;
    }

    public function getPageFiles($pageID){
        $sql = "
            SELECT 
                dosyaid as fileID
            FROM 
                sayfalistedosya
            WHERE
                sayfalistedosya.sayfaid = :pageId
        ";
        return $this->db->select($sql, ['pageId' => $pageID]);
    }

    public function getPageGallery($pageID){
        $sql = "
            SELECT 
                resimgaleriid as galleryID
            FROM 
                sayfalistegaleri 
            WHERE sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function getPageVideos($pageID){
        $sql = "
            SELECT 
                videoid as videoID
            FROM 
                sayfalistevideo 
            WHERE sayfaid = :pageID
        ";

        return $this->db->select($sql, ['pageID' => $pageID]);
    }

    public function getPageLanguageCode($pageID){
        $sql = "
            SELECT 
                dilkisa as languageCode
            FROM 
                sayfalistekategori 
                INNER JOIN kategori ON kategori.kategoriid = sayfalistekategori.kategoriid
                INNER JOIN dil ON dil.dilid = kategori.dilid
            WHERE sayfaid = :pageID
        ";        return $this->db->select($sql, ['pageID' => $pageID])[0]['languageCode'] ?? 'tr';
    }      public function getAppointmentForm()
    {
        $appointmentForm ='
            <form id="appointment-form" action="/?/control/form/post/appointmentForm" method="POST">
                <h2>'._iletisim_form_randevu_baslik.'</h2>
                <input type="hidden" name="action" value="appointmentForm">
                <div class="form-group">
                    <label for="name">'._iletisim_form_adsoyad_yazi.' *</label>
                    <input type="text" name="name" id="name" placeholder="'._iletisim_form_adsoyad_yazi.'" required>
                </div>
                <div class="form-group">
                    <label for="email">'._iletisim_form_eposta_yazi.' *</label>
                    <input type="email" name="email" id="email" placeholder="'._iletisim_form_eposta_yazi.'" required>
                </div>
                <div class="form-group">
                    <label for="phone">'._iletisim_form_telefon_yazi.' *</label>
                    <input type="tel" name="phone" id="phone" placeholder="'._iletisim_form_telefon_yazi.'" required>
                </div>
                <div class="form-group">
                    <label for="appointmentDate">'._iletisim_form_randevu_tarihi.' *</label>
                    <input type="date" name="appointmentDate" id="appointmentDate" min="'.date('Y-m-d', strtotime('+1 day')).'" required>
                </div>
                <div class="form-group">
                    <label for="appointmentTime">'._iletisim_form_randevu_saati.' *</label>
                    <select name="appointmentTime" id="appointmentTime" required>
                        <option value="">'._iletisim_form_saat_seciniz.'</option>
                        <option value="09:00">09:00</option>
                        <option value="10:00">10:00</option>
                        <option value="11:00">11:00</option>
                        <option value="13:00">13:00</option>
                        <option value="14:00">14:00</option>
                        <option value="15:00">15:00</option>
                        <option value="16:00">16:00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">'._iletisim_form_mesajiniz_yazi.'</label>
                    <textarea name="message" id="message" placeholder="'._iletisim_form_mesajiniz_yazi.'"></textarea>
                </div>
                <input type="hidden" name="cf-turnstile-response" id="cf-token-appointment-form">
                <button class="btn btn-primary" type="submit">'._iletisim_form_buton_yazi.'</button>
            </form>
        ';
        return $appointmentForm;
    }
}