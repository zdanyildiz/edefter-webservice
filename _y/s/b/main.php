<?php
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminOrder $adminOrder
 * @var AdminSession $adminSession
 */
include_once MODEL . "Admin/AdminOrder.php";
$adminOrder = new AdminOrder($db, $config);

$siteType = 0;
if(!empty($checkGeneralSettings)){
    $siteType = $checkGeneralSettings[0]["sitetip"];
}

$newOrderCreditCard = $adminOrder->getOrdersByPaymentTypeAndOrderStatusCount("kk", 1);
$newOrderBankTransfer = $adminOrder->getOrdersByPaymentTypeAndOrderStatusCount("bh", 1);
$newOrderCashOnDelivery = $adminOrder->getOrdersByPaymentTypeAndOrderStatusCount("ko", 1);

$unconfirmedCreditCard = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(0, 6);


$orderBeingSupplied = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,9);
$orderBeingPrepared = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,2);
$orderReadyForCargo = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,0);

$orderDeliveredToCargo = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,3);
$orderDeliveryMade = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,4);

$orderCanceled = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,11);
$orderReturnReceived = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,10);

$orderCancellationRequest = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,8);
$orderReturnRequest = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,5);
$orderExchangeRequest = $adminOrder->getOrdersByPaymentStatusAndOrderStatusCount(1,7);

$orderByCity = $adminOrder->getMostOrderedCities();

include_once MODEL . "Location.php";
$location = new Location($db);

$mostOrderedUsers = $adminOrder->getMostOrderedUsers();

?>
<div id="content">
	<section>
		<div class="section-body">
            <?php if($siteType==1): ?>
            <h3>Sipariş Durum Kartları</h3>
			<div class="row">
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-danger no-margin">
								<h1 class="pull-right text-danger"><i class="md md-add-shopping-cart"></i><i class="md md-credit-card"></i></h1>
								<strong class="text-xl"><span><?=$newOrderCreditCard?>*</span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=99"><span class="opacity-50">YENİ SİPARİŞ KREDİ KARTI</span></a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-danger no-margin">
								<h1 class="pull-right text-danger"><i class="md md-add-shopping-cart"></i><i class="md md-attach-money"></i></h1>
								<strong class="text-xl"><span><?=$newOrderBankTransfer?>*</span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=98"><span class="opacity-50">YENİ SİPARİŞ BANKA HAVALESİ</span></a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-danger no-margin">
								<h1 class="pull-right text-danger"><i class="md md-add-shopping-cart"></i><i class="md md-attach-money"></i></h1>
								<strong class="text-xl"><span><?=$newOrderCashOnDelivery?>*</span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=97"><span class="opacity-50">YENİ SİPARİŞ KAPIDA ÖDEME</span></a>
							</div>
						</div>
					</div>
				</div>
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <div class="card-body no-padding">
                            <div class="alert alert-callout alert-danger no-margin">
                                <h1 class="pull-right text-danger"><i class="md md-credit-card"></i></h1>
                                <strong class="text-xl"><span><?=$unconfirmedCreditCard?>*</span> </strong><br/>
                                <a href="/_y/s/s/siparisler/OrderList.php?orderStatus=96"><span class="opacity-50">TAMAMLANMAMIŞ İŞLEM </span></a>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

			<div class="row">
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-warning no-margin">
								<h1 class="pull-right text-warning"><i class="md md-event-busy"></i></h1>
								<strong class="text-xl"><span><?=$orderBeingSupplied?></span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=9"><span class="opacity-50">TEDARİK BEKLEYEN</span></a>
							</div>
						</div>
					</div>
				</div>
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <div class="card-body no-padding">
                            <div class="alert alert-callout alert-warning no-margin">
                                <h1 class="pull-right text-warning"><i class="md md-event-busy"></i></h1>
                                <strong class="text-xl"><span><?=$orderBeingPrepared?></span> </strong><br/>
                                <a href="/_y/s/s/siparisler/OrderList.php?orderStatus=2"><span class="opacity-50">HAZIRLANIYOR</span></a>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-warning no-margin">
								<h1 class="pull-right text-warning"><i class="md md-notifications-on"></i></h1>
								<strong class="text-xl"><span><?=$orderReadyForCargo?></span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=0"><span class="opacity-50">KARGOYA HAZIR</span></a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <div class="card-body no-padding">
                            <div class="alert alert-callout alert-success no-margin">
                                <h1 class="pull-right text-success"><i class="md md-local-shipping"></i></h1>
                                <strong class="text-xl"><span><?=$orderDeliveredToCargo?></span> </strong><br/>
                                <a href="/_y/s/s/siparisler/OrderList.php?orderStatus=3"><span class="opacity-50">KARGOLANAN</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <div class="card-body no-padding">
                            <div class="alert alert-callout alert-success no-margin">
                                <h1 class="pull-right text-success"><i class="md md-play-install"></i></h1>
                                <strong class="text-xl"><span><?=$orderDeliveryMade?></span> </strong><br/>
                                <a href="/_y/s/s/siparisler/OrderList.php?orderStatus=4"><span class="opacity-50">TESLİM EDİLEN</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <div class="card-body no-padding">
                            <div class="alert alert-callout alert-success no-margin">
                                <h1 class="pull-right text-success"><i class="md md-play-install"></i></h1>
                                <strong class="text-xl"><i class="md md-error danger"></i> <span><?=$orderCanceled?></span></strong><br/>
                                <a href="/_y/s/s/siparisler/OrderList.php?orderStatus=11"><span class="opacity-50">İPTAL EDİLEN</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card">
                        <div class="card-body no-padding">
                            <div class="alert alert-callout alert-success no-margin">
                                <h1 class="pull-right text-success"><i class="md md-vertical-align-center"></i></h1>
                                <strong class="text-xl"><span><?=$orderReturnReceived?></span> </strong><br/>
                                <a href="/_y/s/s/siparisler/OrderList.php?orderStatus=10"><span class="opacity-50">İADE ALINANLAR</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-success no-margin">
								<h1 class="pull-right text-success"><i class="md md-vertical-align-top"></i></h1>
								<strong class="text-xl"><span><?=$orderReturnRequest?></span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=5"><span class="opacity-50">İADE TALEBİ</span></a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-success no-margin">
								<h1 class="pull-right text-success"><i class="md md-cancel"></i></h1>
								<strong class="text-xl"><span><?=$orderCancellationRequest?></span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=8"><span class="opacity-50">İPTAL TALEBİ</span></a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="card">
						<div class="card-body no-padding">
							<div class="alert alert-callout alert-success no-margin">
								<h1 class="pull-right text-success"><i class="md md-vertical-align-center"></i></h1>
								<strong class="text-xl"><span><?=$orderExchangeRequest?></span> </strong><br/>
								<a href="/_y/s/s/siparisler/OrderList.php?orderStatus=7"><span class="opacity-50">DEĞİŞİM TALEBİ</span></a>
							</div>
						</div>
					</div>
				</div>

			</div>

            <div class="row">
                <h3>Şehirlere Göre Sipariş Özetleri</h3>
                <?php
                foreach ($orderByCity as $city) {
                    $cityName = $location->getCityNameById($city['siparisteslimatadressehir']);
                    $cityTotal = $city['total'];
                    ?>
                        <div class="col-md-2">
                            <div class="card">
                                <div class="card-body small-padding text-center">
                                    <strong class="text-xl"><?=$cityTotal?></strong><br>
                                    <span><?=$cityName?></span>
                                </div>
                            </div>
                        </div>
                    <?php
                }
                ?>
            </div>
            <div class="row">
                <h3>Firmalara Göre Sipariş Özetleri</h3>
                <?php
                foreach ($mostOrderedUsers as $orderedUser) {
                    ?>
                    <div class="col-md-2">
                        <div class="card">
                            <div class="card-body small-padding text-center">
                                <strong class="text-xl"><?=$orderedUser['total']?></strong><br>
                                <span><?=$orderedUser['siparisfaturaunvan']?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php endif; ?>
		</div>
	</section>
</div>