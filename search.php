<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once './helper/Page.php';
require_once './entities/Product.php';

$page = new Page();
$page->setTitle('Tìm Kiếm');

$page->addCSS("assets/js/jRange-master/jquery.range.css");
$page->addJavascript("assets/js/jRange-master/jquery.range.js");
$page->startBody();
$ListPro = array();
$flag = false;

$rowsPerPage = 9; // số lượng dòng được hiển thị 1 trang
$curPage = 1; // Trang hiện tại
$numberOfRows = 0;

if (isset($_GET['page']) && !empty(($_GET['page']))) {
    $curPage = $_GET['page'];//truyền thứ tự trang cần xem
}
$offset = ($curPage - 1) * $rowsPerPage;// tính offset bắt đầu load

if (isset($_GET["btnSearch"])) {
    $Product = new Product();
    $Product->setProName($_GET["txtProName"]);
    $Price = $_GET["txtPrice"];
    $Price = explode(',', $Price);
    $Product->setPrice($Price);
    require_once './entities/Category.php';
    require_once './entities/Brand.php';
    require_once './entities/Device.php';
    $CatPro = new Category();
    $CatPro->setBrand(new Brand($_GET["cboBra"]));
    $CatPro->setDevice(new Device($_GET["cboDevice"]));
    $Product->setCatPro($CatPro);

    $ListPro = $Product->loadLimit($rowsPerPage, $offset, "ProName");
    $numberOfRows = $Product->countRecords();
    $flag = true;
}
?>
    <div class="box clearfix">
        <ol class="breadcrumb box-top">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <li class="active">Tìm Kiếm</li>
        </ol>
        <div id="searchBody">
            <form action="search.php" method="get">
                <div class="col-md-12 row">
                    <div class="form-group col-md-6">
                        <label for="txtProName">Tên Sản Phẩm:</label>
                        <input type="text" class="form-control note" name="txtProName" id="txtProName">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="txtPrice">Giá:</label>
                        <input type="hidden" class="form-control" name="txtPrice" id="txtPrice" value="1"/>
                    </div>

                </div>
                <div class="col-md-12 row">
                    <div class="form-group col-md-6">
                        <label for="cboDevice">Thiết Bị:</label>
                        <select class="form-control" name="cboDevice" id="cboDevice">
                            <?php
                            require_once "entities/Device.php";
                            $ListDevices = Device::loadAll();
                            echo '<option value="-1">Chọn ...</option>';
                            foreach ($ListDevices as $ItemDev) {
                                echo '<option value=' . $ItemDev->getDeviceID() . '>';
                                echo $ItemDev->getDeviceName();
                                echo '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">

                        <label for="cboBra">Nhà Sản Xuất</label>
                        <select class="form-control" name="cboBra" id="cboBra">
                            <option value="-1">Chọn ...</option>
                            <?php
                            require_once "entities/Brand.php";
                            $ListBrands = Brand::loadAll();
                            foreach ($ListBrands as $ItemBrand) {
                                ?>
                                <option value=<?php echo $ItemBrand->getBraID(); ?>>
                                    <?php echo $ItemBrand->getBraName(); ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 row">
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-primary" id="btnSearch" name="btnSearch">Tìm Kiếm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php if ($flag) { ?>
    <div id="searchResult" class="box clearfix">
        <div class="box-top">
            Kết quả tìm kiếm
        </div>
        <div class="">
            <form id="addToCart-form" method="post" action=""><input type="hidden" id="txtProID"
                                                                     name="txtProID"/></form>
            <?php if (empty($ListPro)) {
                echo "<div style='margin: 10px;'  class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-bullhorn\"></i>Không tìm thấy sản phẩm nào !</div>";
            } else {
                Product::printListProduct($ListPro);
                require_once './helper/Pagination.php';
                $self = $_SERVER['PHP_SELF']; // Lay dia chi truc tiep cua PHP dang mo
                $pagination = new Pagination($curPage, $rowsPerPage, $offset, $numberOfRows, $self);
                ?>
                <!--pagination-->
                <nav id="paging" class="text-center">
                    <?php $pagination->printPaging(); ?>
                </nav>
                <!--/pagination-->

            <?php } ?>
        </div>
    </div>
<?php } ?>
    <script type="text/javascript">
        $().ready(function () {
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

            var rangePrice = $('#txtPrice').jRange({
                from: 0,
                to: 25,
                step: 1,
                scale: [0, 5, 10, 15, 20, 25],
                format: function (value, type) {
                    var text = value.format(0, 3, ',') + ' tr';
                    return text;
                },
                showLabels: true,
                isRange: true
            });
            rangePrice.jRange('updateRange', '0,' + 25, '0,' + 25);
            $('.slider-container').css('width', '100%');
        });
    </script>
<?php
$page->endBody();
echo $page->render('Templates/Template.php');
