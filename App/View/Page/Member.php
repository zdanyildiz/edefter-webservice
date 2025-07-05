<?php
/**
 * @var Casper $casper
 * @var Session $session
 * @var string $query
 * @var string $membershipAgreementLink
 */

$config = $casper->getConfig();
$helper = $config->Helper;
$visitor = $casper->getVisitor();
$visitorUniqueID = $visitor['visitorUniqID'] ?? "";
$memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;

if(!empty($query)){
    parse_str($query,$parsedQuery);

    if(isset($parsedQuery['orderType'])){
        $query = str_replace("&orderType=".$parsedQuery['orderType'],"",$query);
    }
}

$siteConfig = $casper->getSiteConfig();
$specificPageLinks = $siteConfig['specificPageLinks'];
$memberPageLinks = array_filter($specificPageLinks, function($specificPageLink) {
    return $specificPageLink['sayfatip'] == 17;
});
$memberPageLinks = reset($memberPageLinks);
$memberPageLink = $memberPageLinks['link'];
//echo '<pre>';
//print_r($casper);exit();
?>
<?php if ($memberStatus) :
    switch ($query) {
        case 'address':
            $includeFile = VIEW.'Page/Member/Address.php';
            break;
        case 'updateAddress':
            $includeFile = VIEW.'Page/Member/UpdateAddress.php';
            break;
        case 'message':
            $includeFile = VIEW.'/Page/Member/Message.php';
            break;
        case 'orders':
            $includeFile = VIEW.'Page/Member/Order.php';
            break;
        case 'cancellation-refund-exchange':
            $includeFile = VIEW.'Page/Member/CancellationRefundExchange.php';
            break;
        default:
            $includeFile = VIEW.'Page/Member/Profile.php';
            break;
    }
    include_once $includeFile;
else :
    include_once VIEW.'Page/Member/Login.php';
endif; ?>

