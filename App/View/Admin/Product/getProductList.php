<?php
/**
 * @var AdminSession $adminSession
 * @var Database $db
 * @var Router $router
 */

$requestData = $router->requestData;
$seoTitle = $router->seoTitle;

$productList = $adminSession->getSession("productList");
$totalProducts = $productList["totalProducts"];
$totalPages = $productList["totalPages"];
$productList = $productList["products"];

$currentPage = $requestData["page"] ?? 1;

?>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0"><?=$seoTitle?></h3>
            </div>
            <?php if($totalProducts>0){?>
            <div class="card-body pt-4">
                <div class="grid-margin">
                    <div class="">
                        <div class="panel panel-primary">

                            <div class="panel-body border-0 pt-0">

                                <div class="table-responsive">
                                    <table id="data-table"
                                           class="table table-bordered text-nowrap mb-0">
                                        <thead class="border-top">
                                        <tr>
                                            <th class="bg-transparent border-bottom-0 sorting sorting_asc"
                                                style="width: 5%;">ID</th>
                                            <th
                                                class="bg-transparent border-bottom-0 sorting">
                                                ÜRÜN</th>
                                            <th
                                                class="bg-transparent border-bottom-0 sorting">
                                                KATEGORİ</th>
                                            <th
                                                class="bg-transparent border-bottom-0 sorting">
                                                FİYAT</th>
                                            <th
                                                class="bg-transparent border-bottom-0 sorting">
                                                STOK</th>
                                            <th
                                                class="bg-transparent border-bottom-0 sorting">
                                                VİTRİN</th>
                                            <th class="bg-transparent border-bottom-0 sorting">FIRSAT</th>
                                            <th class="bg-transparent border-bottom-0 sorting">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($productList as $product):
                                            $product = $product[0];

                                            $productID = $product["sayfaid"];
                                            $productName = $product["sayfaad"];
                                            $productImage = $product["resim_url"];
                                            $productImage = explode(",",$productImage);
                                            $productCategory = $product["kategoriad"];
                                            $productAmount = $product["urunsatisfiyat"];
                                            $productCurrencySymbol = $product["parabirimsimge"];
                                            $productSeoLink = $product["link"];
                                            $productStock = $product["urunstok"];

                                            $productIsShowcase = $product["urunanasayfa"];
                                            $productIsOpportunity = $product["urungununfirsati"];
                                        ?>
                                        <tr class="border-bottom">
                                            <td class="text-center">
                                                <div class="mt-0 mt-sm-2 d-block">
                                                    <h6
                                                        class="mb-0 fs-14 fw-semibold">
                                                        #<?=$productID?></h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <span class="avatar bradius"
                                                          style="background-image: url(<?= imgRoot."?imagePath=".trim($productImage[0])."&width=100&height=100"?>)">

                                                    </span>
                                                    <div
                                                        class="ms-3 mt-0 mt-sm-2 d-block">
                                                        <h6
                                                            class="mb-0 fs-14 fw-semibold"><?=$productName?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <div
                                                        class="mt-0 mt-sm-3 d-block">
                                                        <h6
                                                            class="mb-0 fs-14 fw-semibold">
                                                            <?=$productCategory?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="mt-sm-2 d-block"><?=$productCurrencySymbol." ".$productAmount?></span></td>
                                            <td>
                                                <h6
                                                        class="mb-0 fs-14 fw-semibold"><?=$productStock?></h6>
                                            </td>
                                            <td style="text-align: center">

                                                <div
                                                    class="mt-0 mt-sm-1 d-block">
                                                    <a class="btn btn-sm <?=$productIsShowcase==1 ? 'bg-blue' : 'text-primary'?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-original-title="go"><span
                                                                class="fe fe-eye fs-14"></span></a>
                                                </div>

                                            </td>
                                            <td style="text-align: center">
                                                <div class="mt-sm-1 d-block">
                                                    <a class="btn btn-sm <?=$productIsShowcase==1 ? 'bg-blue' : 'text-primary'?>"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-original-title="go"><span
                                                                class="fe fe-star fs-14"></span></a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="g-2">
                                                    <a class="btn text-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-original-title="go"><span
                                                                class="fe fe-external-link fs-14"></span></a>
                                                    <a class="btn text-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-original-title="Edit"><span
                                                            class="fe fe-edit fs-14"></span></a>
                                                    <a class="btn text-danger btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-original-title="Delete"><span
                                                            class="fe fe-trash-2 fs-14"></span></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>

                                <?php if($totalPages>1):?>
                                <ul class="pagination mg-b-0 page-0 ">
                                    <?php

                                    for($i=1;$i<=$totalPages;$i++):
                                        $paginationLink = "http://l.yenimakina/?/admin/product/list/getProductList&page=".$i;
                                    ?>
                                    <?php if($i==1 && $currentPage>$i):?>
                                    <?php $paginationPrevLink = "http://l.yenimakina/?/admin/product/list/getProductList&page=".$currentPage-1;?>
                                    <li class="page-item">
                                        <a aria-label="Next" class="page-link" href="<?=$paginationPrevLink?>"><i class="fa fa-angle-left"></i></a>
                                    </li>
                                    <?php endif;?>

                                    <li class="page-item <?=$i==$currentPage ? 'active' : ''?>">
                                        <a class="page-link" href="<?=$paginationLink?>"><?=$i?></a>
                                    </li>

                                    <?php if($i==$totalPages && $currentPage<$i):?>
                                    <?php $paginationNextLink = "http://l.yenimakina/?/admin/product/list/getProductList&page=".$currentPage+1;?>
                                    <li class="page-item">
                                        <a aria-label="Next" class="page-link" href="<?=$paginationNextLink?>"><i class="fa fa-angle-right"></i></a>
                                    </li>
                                    <?php endif;?>

                                    <?php endfor;?>
                                </ul>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php }else{?>
                <h2>Hiç ürün bulunamadı</h2>
            <?php }?>
        </div>
    </div>
</div>
<!-- table th'lerine  -->