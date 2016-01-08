<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once './helper/Page.php';
require_once 'entities/Product.php';
require_once './entities/Cart.php';
require_once './helper/Context.php';
$p = new Product();
$ListProMostViewed = $p->loadLimit(10, 0 , "View", "DESC");
$ListProNew = $p->loadLimit(10, 0 , "ProCreated", "DESC");
$ListProBestSell =  $p->loadLimit(10, 0 , "OnOrder", "DESC");
$page = new Page();
$page->setTitle('Trang Chủ');
$page->startBody();

//SLIDE-SHOW
include './slideshow.php';
?>

    <!--/SLIDE-SHOW-->
    <form id="addToCart-form" method="post" action=""><input type="hidden" id="txtProID" name="txtProID"/></form>
    <!--LATEST-->
    <div class="latest box clearfix" id="proLatest">
        <div class="box-top">
            <p>Mới nhất</p>
        </div>
        <div id="proLatestBody">
            <?php Product::printListProduct($ListProNew); ?>
        </div>
    </div>
    <!--/LATEST-->

    <!--SELLEST-->
    <div class="sellest box clearfix" id="proBestSell">
        <div class="box-top">
            <p>Bán chạy</p>
        </div>
        <div id="proBestSellBody">
            <?php Product::printListProduct($ListProBestSell); ?>
        </div>
    </div>

    <!--/SELLEST-->

    <!--MOST-VIEWED-->
    <div class="most-viewed box clearfix" id="proMostViewed">
        <div class="box-top">
            <p>Xem nhiều</p>
        </div>
        <div id="proMostViewed">
            <?php Product::printListProduct($ListProMostViewed); ?>
        </div>
    </div>
    <!--/MOST-VIEWED-->

    <script type="text/javascript">
        $().ready(function () {
            $('#slideshow').insertBefore('#content');
            $('button.btn-shopping-cart').on('click', function () {
                var proID = $(this).data('proid');
                $('#txtProID').val(proID);
                $('#addToCart-form').submit();
            });
            $('#proLatest .product-block').each(function(){
                var html = '<div class="new" ></div >';
                $(this).append(html);
            });
        });
    </script>
<?php
$page->endBody();
echo $page->render('Templates/Template.php');

