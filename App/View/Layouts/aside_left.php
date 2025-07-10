<?php
/**
 * @var Casper $casper
 * @var Session $session
 * @var string $memberLink
 */
// Eğer kullanıcı giriş yapmışsa kullanıcı menüsü ve bilgileri görüntülenir
// ziyaretçi modelinden ziyaretçinin üye olup olmadığı bilgisini alalım, üye değilse üye olma ya da giriş yapma formlarını getirelim

$visitor = $casper->getVisitor();
$visitorUniqID = $visitor['visitorUniqID'] ?? null;

$memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;

if ($memberStatus) {
    $memberInfo = $visitor['visitorIsMember'];
    $memberID = $memberInfo['memberID'];
    $memberUniqID = $visitorUniqID;
    $memberCreateDate = $memberInfo['memberCreateDate'] ?? null;
    $memberUpdateDate = $memberInfo['memberUpdateDate'] ?? null;
    $memberType = $memberInfo['memberType'];
    $memberName = $memberInfo['memberName'] ?? null;
    $memberFirstName = $memberInfo['memberFirstName']?? null;
    $memberLastName = $memberInfo['memberLastName'] ?? null;
    $memberEmail = $memberInfo['memberEmail'];
    $memberPhone = $memberInfo['memberPhone'] ?? null;
    $memberDescription = $memberInfo['memberDescription'] ?? null;
    $memberInvoiceName = $memberInfo['memberInvoiceName'] ?? null;
    $memberInvoiceTaxOffice = $memberInfo['memberInvoiceTaxOffice'] ?? null;
    $memberInvoiceTaxNumber = $memberInfo['memberInvoiceTaxNumber'] ?? null;
    $memberActive = $memberInfo['memberActive'] ?? null;
}
else{
    $visitorIP = $visitor['visitorIP'] ?? null;
    $visitorVisitCount = $visitor['visitorVisitCount'] ?? null;
}
if ($memberStatus) {
?>
<aside class="aside-left-user">
    <div class="user-container">
        <div class="user-header">
            <h1><?=_uyelik_bilgilerim_yazi?></h1>
            <span class="aside-left-user-close btn" title="<?=_sol_kapat_yazi?>">X</span>
        </div>
        <div class="user-content">
            <div class="user-info">
                <div class="user-info-item">
                    <?=$memberFirstName?> <?=$memberLastName?>
                </div>
                <div class="user-info-item small">
                    <?=$visitorUniqID?>
                </div>
                <div class="user-info-item">
                    <?=$memberEmail?>
                </div>
                <div class="user-info-item">
                    <?=$memberPhone?>
                </div>
                <div class="user-info-item">
                    <a href="/?/control/member/get/logout" class="btn btn-danger"><?=_sol_uyelik_cikis_yazi?></a>
                </div>
            </div>
        </div>
        <nav class="user-menu">
            <ul>
                <li><a href="/?/control/member/get/profile"><?=_sol_uyelik_bilgilerim_yazi?></a></li>
                <li><a href="/?/control/member/get/cart"><?=_sol_uyelik_sepetim_yazi?></a></li>
                <li><a href="/?/control/member/get/orders"><?=_sol_uyelik_siparislerim_yazi?></a></li>
                <li><a href="/?/control/member/get/getAddresses"><?=_sol_uyelik_adreslerim_yazi?></a></li>
                <li><a href="/?/control/member/get/message"><?=_sol_uyelik_mesajlarim_yazi?></a></li>
                <li><a href="/?/control/member/get/favorite"><?=_sol_uyelik_favorilerim_yazi?></a></li>
                <li><a href="/?/control/member/get/cancellation-refund-exchange"><?=_sol_uyelik_iptal_iade_degisim_yazi?></a></li>
            </ul>
        </nav>
    </div>
</aside>
<?php }else{ ?>
<aside class="aside-left-user">
    <div class="user-container">
        <div class="user-header">
            <h1><?=_sol_ziyaretci_yazi?></h1>
            <span class="aside-left-user-close btn">X</span>
        </div>
        <div class="user-content">
            <div class="user-info">
                <div class="user-info-item">
                    <?=$visitorUniqID?>
                </div>
                <div class="user-info-item">
                    <?=$visitorIP?>
                </div>
                <div class="user-info-item">
                    <?=$visitorVisitCount?>
                </div>
                <div class="user-info-item">
                    <a href="<?=$memberLink?>" class="btn btn-danger"><?=_giris_form_giris_yazi?> |  <?=_uye_olun?></a>
                </div>
            </div>
        </div>

    </div>
</aside>
<?php }?>
<button id="show-aside-left" class="show-aside-left"><label class="arrow-label">></label></button>
