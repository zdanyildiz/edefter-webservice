<?php
/**
 * Bu sayfada ödeme işlemleri yapılır. Bu sayfada isek, satın alınacak ürünler ve kargo, fatura adresi gibi seçimler yapılmış demektir.
 * Kredi Kartı formu, banka havalesi ile ödeme butonu ya da kapıda ödeme butonu görüntülenir. Ayrıca müşteri için sipariş not ekranı görünmektedir.
 * Tercih edilen ödeme yöntemine göre havale ya da kapıda ödeme seçilirse doğrudan siparişi alındı kabul edip hem alıcıya hem satıcıya mail gönderilir.
 * Kredi kartı ödeme seçilirse aracı ödeme kuruluşunun kk formu görüntülenir. Bu formda kart bilgileri girilir ve ödeme denenir. Ödeme başarılı olursa taraflara bilgilendirme maili gönderilir.
 */
/**
 * @var array $orderData
 * @var Casper $casper
 * @var Session $session
 * @var array $query
  */

$config = $casper->getConfig();
$helper = $config->Helper;
$routeResult = $session->getSession('routerResult');
//Log::write("url: ". json_encode($routeResult),"info");
$query =$routeResult['query'];
$query = is_string($query) ? $query : '';
parse_str($query,$parsedQuery);
$paymentResult = $parsedQuery['paymentResult'] ?? '';

$visitor = $casper->getVisitor();
$visitorUniqueID = $visitor['visitorUniqID'] ?? "";
$memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;

$creditCardBankName = $orderData['paymentData']['creditCardBankName'] ?? "";
?>

<?php
if ($memberStatus) :
    switch ($paymentResult) {
        case 'success':
            $includeFile = VIEW.'Page/Payment/PaymentResultSuccess.php';
            break;
        case 'error':
            $includeFile = VIEW.'Page/Payment/PaymentResultError.php';
            break;
        case 'iyzico':
            $includeFile = VIEW.'Page/Payment/PaymentResultIyzico.php';
            break;
        default:
            $includeFile = VIEW.'Page/Payment/Payment.php';
            break;
    }
    include_once $includeFile;
else :
    include_once VIEW.'Page/Member/Login.php';
endif;

?>
