<?php
/**
 * @var Session $session
 * @var Casper $casper
 * @var Helper $helper
 * @var Router $router // $router'ın index.php'den global olarak erişilebilir olduğu varsayılıyor
 */

$visitorDataInCasper = $casper->getVisitor();
$visitorModel = null; // Gerektiğinde yüklenecek (Lazy load)
$getMemberInfo = false;
// Oturumda geçerli ziyaretçi verisi yoksa başlatmayı/yüklemeyi dene
if (empty($visitorDataInCasper) || !isset($visitorDataInCasper['visitorUniqID'])) {
    $visitorModel = new Visitor();
    $visitorCookieData = $session->getCookie("visitor");

    if (empty($visitorCookieData)) {
        // --- Senaryo: Yeni Ziyaretçi (Oturumda veri yok, Çerez yok) ---
        if ($visitorModel->isBot()) {
            $url = $router->url ?? $_SERVER['REQUEST_URI'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $ref = $_SERVER['HTTP_REFERER'] ?? "No referrer";
            Log::write("Bot tespit edildi! [$ip] [$url] [$ref]", "error");
            // Botlar için sonraki adımı belirleyin (örn: çıkış yap, bayrak ayarla)
        } else {
            $newVisitorInfo = $visitorModel->getVisitorInformation();
            if ($newVisitorInfo) {
                $casper->setVisitor($newVisitorInfo);
                $session->updateSession("casper", $casper); // Yeni ziyaretçi verisiyle oturumu güncelle

                $cookiePayload = $newVisitorInfo;
                // Mevcut davranışla tutarlı: visitorGeo çerezde saklanmıyor
                unset($cookiePayload['visitorGeo']);
                $session->addCookie("visitor", $cookiePayload, 1); // 1 ay geçerli çerez
            } else {
                Log::write("Yeni ziyaretçi bilgisi alınamadı.", "error");
            }
        }
    } else {
        // --- Senaryo: Geri Dönen Ziyaretçi (Oturumda veri yok, Çerez var) ---
        // Casper'ı (oturumu) çerezden gelen verilerle doldur
        $dataFromCookie = $visitorCookieData;

        if (!isset($dataFromCookie['visitorRemember']) || $dataFromCookie['visitorRemember'] == false) {
            $dataFromCookie['visitorIsMember'] = ['memberStatus' => false];
            $dataFromCookie['visitorRemember'] = false;
        } else {
            // Üye hatırlandı, tam üye bilgilerini daha sonra almak için bayrak ayarla
            $getMemberInfo = true;
        }

        $dataFromCookie['visitorGetCart'] = true; // Varsayılan
        if (in_array($dataFromCookie['visitorIP'], $visitorModel->getBankIps())) {
            $dataFromCookie['visitorGetCart'] = false;
            Log::write("Banka robotu (çerezden oturuma aktarım), sepet bilgisi alınmayacak", "special");
        }

        $casper->setVisitor($dataFromCookie);
        $session->updateSession("casper", $casper); // Çerezden gelen veriyle oturumu güncelle

        // Şimdi, çerezin kendisinin yenilenmesi gerekip gerekmediğini kontrol et
        $cookieIP = $visitorCookieData['visitorIP'];
        $cookieUniqID = $visitorCookieData['visitorUniqID'] ?? 'N/A';
        $cookieEntryTime = $visitorCookieData['visitorEntryTime'];
        $currentRealIP = $helper->getIP();

        $now = new DateTime();
        $lastVisitDateTime = new DateTime($cookieEntryTime);
        $secondsSinceLastVisit = $now->getTimestamp() - $lastVisitDateTime->getTimestamp();
        $hoursSinceLastVisit = $secondsSinceLastVisit / 3600;

        if ($hoursSinceLastVisit >= 24 || $cookieIP !== $currentRealIP) {
            Log::write("Ziyaretçi çerezi 24 saati geçti veya IP değişti, yenileniyor [$cookieUniqID]", "special");

            $refreshedCookiePayload = $visitorCookieData; // Mevcut çerez verisiyle başla
            $refreshedCookiePayload['visitorVisitCount'] = ($refreshedCookiePayload['visitorVisitCount'] ?? 0) + 1;
            $refreshedCookiePayload['visitorIP'] = $currentRealIP;
            $refreshedCookiePayload['visitorBrowser'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            // visitorEntryTime orijinal yenileme mantığında güncellenmiyor, bu yüzden onu koruyoruz.

            // Mevcut davranışla tutarlı: visitorGeo yenilenen çerezde saklanmıyor
            unset($refreshedCookiePayload['visitorGeo']);

            // $ssession hatası düzeltildi, 12 ay geçerli çerez
            $session->addCookie("visitor", $refreshedCookiePayload, 12);
        }
    }
}
// Eğer $visitorDataInCasper zaten geçerliyse, bu bloğun tamamı atlanır.
// Ziyaretçi mevcut oturumdan tanınır.