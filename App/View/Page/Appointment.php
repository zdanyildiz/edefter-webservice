<?php
/**
 * @var Session $session
 * @var Config $config
 * @var array $page
 * @var Casper $casper
 * @var string $languageCode
 */

$helper = $config->Helper;
$companyInfo = $casper->getSiteConfig()['companySettings'];
?>
<div class="page-container">
    <div class="page-title">
        <h1><?php echo $page['sayfaad']; ?></h1>
    </div>
    <div class="page-content">
        <div class="page-text">
            <?php echo $page['sayfaicerik']; ?>
        </div>
    </div>
</div>
