<?php
require_once './helper/Page.php';
require_once './entities/Product.php';
require_once './entities/Category.php';
$page = new Page();
$page->addCSS('assets/css/productsByCat.css');
$page->setTitle('Danh Sách Sản Phẩm');
$page->startBody();
$ListPro = array();


$rowsPerPage = 9; // số lượng dòng được hiển thị 1 trang
$curPage = 1; // Trang hiện tại
$numberOfRows = 0;

if (isset($_GET['page']) && !empty(($_GET['page']))) {
    $curPage = $_GET['page'];//truyền thứ tự trang cần xem
}
$offset = ($curPage - 1) * $rowsPerPage;// tính offset bắt đầu load

$p = new Product();
$c = new Category();
$device = new Device();
$bra = new Brand();
$flag = 0;
if (isset($_GET["CatID"]) && !empty($_GET["CatID"])) {
    $catID = ($_GET["CatID"]);
    $c = Category::getCat($catID);
    $p->setCatPro($c);
    $flag = 1;
}
if (isset($_GET["DeviceID"]) && !empty($_GET["DeviceID"])) {
    $devID = $_GET["DeviceID"];
    $c->setDevice(new Device($devID));
    require_once 'entities/Device.php';
    $device = Device::getDevice($devID);
    $p->setCatPro($c);
    $flag = 2;
}
if (isset($_GET["BraID"]) && !empty($_GET["BraID"])) {
    $braID = $_GET["BraID"];
    $c->setBrand(new Brand($braID));
    require_once 'entities/Brand.php';
    $bra = Brand::getBrand($braID);
    $p->setCatPro($c);
    $flag = 3;
}
if ($flag != 0) {
    $ListPro = $p->loadLimit($rowsPerPage, $offset, "CatProID");
    $numberOfRows = $p->countRecords();
    ?>
    <form id="addToCart-form" method="post" action=""><input type="hidden" id="txtProID" name="txtProID"/></form>
    <!--PRODUCTS-->
    <div class="box clearfix">
        <ol class="breadcrumb box-top">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <?php if ($flag == 1) {
                echo '<li><a href="productsByCat.php?DeviceID=' . $c->getDevice()->getDeviceID() .'">' . $c->getDevice()->getDeviceName() . '</a></li>';
                echo '<li class="active">' . $c->getCatName() . '</li>';
            } else {
                if ($flag == 2) {
                    echo '<li class="active">' . $device->getDeviceName() . '</li>';
                } else {
                    echo '<li class="active">' . $bra->getBraName() . '</li>';
                }
            } ?>


        </ol>
        <div id="proBody">
            <?php Product::printListProduct($ListPro); ?>
        </div>

    </div>
    <!--/PRODUCTS-->

    <!--pagination-->
    <?php
    require_once './helper/Pagination.php';
    $self = $_SERVER['PHP_SELF']; // Lay dia chi truc tiep cua PHP dang mo
    $pagination = new Pagination($curPage, $rowsPerPage, $offset, $numberOfRows, $self);
    ?>

    <nav id="paging" class="text-center">
        <?php $pagination->printPaging(); ?>
    </nav>
    <!--/pagination-->
    <script>
        $(function () {
            $('#paging .pull-right').removeClass('pull-right');
            $('#paging a').click(function () {
                var page = $(this).text();
                if (page.length === 0) {
                    page = $(this).find('.sr-only').text();
                }
                var url = window.location.href;
                if (url.indexOf("&&page=") > 0) {
                    url = url.substr(0, url.indexOf("&&page="));
                }
                window.location.assign(url + '&&page=' + page);
            });
            $('button.btn-shopping-cart').on('click', function () {
                var proID = $(this).data('proid');
                $('#txtProID').val(proID);
                $('#addToCart-form').submit();
            });
        });

    </script>
    <?php
    $page->endBody();
    echo $page->render('Templates/Template.php');
} else {
    require_once './helper/Utils.php';
    $_SESSION['showModalLogin'] = true;
    $url = "index.php";
    Utils::Redirect($url);
}

