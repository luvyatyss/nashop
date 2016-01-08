<?php
if (!isset($_SESSION)) {
    session_start();
}


$fail = false;

if (!isset($_GET["token"]) || $_SESSION["IsLogin"] == 0) {
    $fail = true;
} else {
    require_once '../helper/crypter.php';
    $crypter = new Crypter("nhatanh");
    $decrypted = $crypter->Decrypt(str_replace(" ", "+", $_GET["token"]));
    //$data = explode("/", $decrypted);
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $decrypted) {
        $fail = true;
    } else {
        $fail = false;
    }
}
if ($fail) {
    require_once '../helper/Utils.php';
    $url = "adminLogin.php";
    Utils::Redirect($url);

} else {
    require_once '../helper/Page.php';
    require_once '../helper/Pagination.php';
    require_once '../entities/Category.php';
    require_once '../entities/Product.php';

    require_once '../helper/Controls.php';

    $page = new Page();

    $page->addCSS("assets/js/jRange-master/jquery.range.css");
    $page->addCSS("assets/css/jrange.css");


    $page->addJavascript("assets/js/jRange-master/jquery.range.js");

    $page->startBody();

    $control = new Controls(Controls::Insert); //Chưa chọn loại sản phẩm;

// khi chọn loại sản phẩm

//Load danh mục loại sản phầm
    $ListCat = Category::loadAll();
    $maxPrice = Product::getValueMaxColName('Price');
    $maxPrice = empty($maxPrice) ? 0 : ceil($maxPrice / 1000000); // /1 triệu đồng
    $maxInStock = Product::getValueMaxColName('InStock');
    $maxInStock = empty($maxInStock) ? 0 : $maxInStock;


    $token = "token=" . $_GET["token"];
    ?>
    <div class="page-header">
        <div class="pull-right">
            <span class="btn btn-default btn-file">
                <span><i class="entypo-upload"></i></span>
                <input type="file" id="files" name="exportExcel[]"/>
            </span>
            <span class="btn btn-default btn-file">
                <span><i class="entypo-upload"></i></span>
                <input type="file" id="files" name="importExcel"/>
            </span>
            <a href="productModify.php?<?php echo $token; ?>" class="btn btn-primary pull-right" data-placement="top" title="Thêm"
               name="btnAdd"
               id="btnAdd">
                <strong><i class="entypo-plus"></i></strong>
            </a>
        </div>
        <h1>Product</h1>
        <ol class="breadcrumb bc-3">
            <li>
                <a href="index.php">Trang Chủ</a>
            </li>
            <li class="active">
                <strong>Sản Phẩm</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Sản Phẩm</span>
        </div>
        <div class="panel-body">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-sm-6 pull-left">
                        <label>
                            <select class="form-control" id="cboRowsPerPage" name="cboRowsPerPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="-1">All</option>
                            </select>
                        </label>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="table-1">
                    <thead>
                    <tr class="header">
                        <th style="width: 120px;"></th>
                        <th style="width: 100px" data-name="ImageURL">Ảnh</th>
                        <th style="width: 100px" data-name="ProID">ID</th>
                        <th style="width: 150px" data-name="ProName">Sản Phẩm</th>
                        <th style="width: 150px" data-name="Price">Đơn Giá</th>
                        <th style="width: 150px" data-name="InStock">Tồn Kho</th>
                        <th style="width: 100px" data-name="ProCreated">Ngày Tạo</th>
                        <th style="width: 150px" data-name="CatProID">Loại Sản Phẩm</th>
                        <th style="width: 120px" data-name="Discontinued">Tình Trạng</th>
                    </tr>
                    <tr class="replace-inputs">
                        <th></th>
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterProID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Sản Phẩm" id="txtFilterProName">
                        </th>

                        <th class="filteringRange">
                            <input type="hidden" id="txtFilterPrice" value="1"/>
                        </th>
                        <th class="filteringRange">
                            <input type="hidden" id="txtFilterInStock" value="1"/>
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Loại Sản Phẩm" id="">
                        </th>

                        <th class="filtering">
                            <select class="form-control" name="cboFilterCategories" id="cboFilterCategories">
                                <option value="-1">Chọn ...</option>
                                <?php
                                foreach ($ListCat as $ItemCat) {
                                    ?>
                                    <option value=<?php echo $ItemCat->getCatID(); ?>>
                                        <?php echo $ItemCat->getCatName(); ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <!-- Load Danh mục Loại Sản Phẩm-->
                        </th>
                        <th class="filtering">
                            <select class="form-control" id="cboFilterStatus">
                                <option value="-1">Chọn ...</option>
                                <option value="false">Đang hoạt động</option>
                                <option value="true">Tạm ngưng</option>
                            </select>
                        </th>
                    </tr>
                    </thead>
                </table>

                <div id="dataContent">

                </div>
            </div>
        </div>
    </div>


    <script>

        Number.prototype.format = function (n, x, s, c) {
            var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
                num = this.toFixed(Math.max(0, ~~n));

            return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
        };
        $(document).ready(function () {
            var round = function (number) {
                var mu = 0;
                var temp = number;
                while (temp >= 100) {
                    mu++;
                    temp /= 10;
                }
                temp = Math.floor(temp);
                if (Math.pow(10, mu) * temp == number) {
                    return number;
                }
                return Math.pow(10, mu) * (temp + 1);

            };
            var step = function (number) {
                return number > 10000 ? 1000 : ( number > 100 ? 100 : 1);
            };
            var maxInStock = <?php echo $maxInStock ; ?>;
            maxInStock = round(maxInStock);
            var rangeInStock = $('#txtFilterInStock').jRange({
                from: 0,
                to: maxInStock,
                step: step(maxInStock),
                format: function (value, type) {
                    var text = value.format(0, 3, ',');
                    return text;
                },
                width: 120,
                showLabels: true,
                isRange: true,
                theme: "theme-red",
                ondragend: function () {
                    Filter(1, show);
                },
                onbarclicked: function () {
                    Filter(1, show);
                }
            });
            rangeInStock.jRange('updateRange', '0,' + maxInStock, '0,' + maxInStock);

            var maxPrice = <?php echo $maxPrice ; ?>;
            var rangePrice = $('#txtFilterPrice').jRange({
                from: 0,
                to: maxPrice,
                step: 1,
                format: function (value, type) {
                    var text = value.format(0, 3, ',') + ' tr';
                    return text;
                },
                width: 120,
                showLabels: true,
                isRange: true,
                theme: "theme-red",
                ondragend: function () {
                    Filter(1, show);
                },
                onbarclicked: function () {
                    Filter(1, show);
                }
            });
            rangePrice.jRange('updateRange', '0,' + maxPrice, '0,' + maxPrice);
            var page = 1;
            var show = 5;
            var sortName = '';
            var sortType = 'ASC';
            $('.header>th').click(function () {
                sortName = $(this).data("name");
                sortType = $(this).hasClass("sorting_asc") ? 'DESC' : 'ASC';
                Filter(page, show);
            });

            //Filter
            var Filter = function (page, show) {
                var ProID = $('#txtFilterProID').val();
                var ProName = $('#txtFilterProName').val();
                var Price = $('#txtFilterPrice').val().split(',');
                var InStock = $('#txtFilterInStock').val().split(',');


                var CatProID = $('#cboFilterCategories').val();
                var Status = $('#cboFilterStatus').val();

                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('productsBody.php', {
                    page: page,
                    show: show,
                    ProID: ProID,
                    ProName: ProName,
                    Price: Price,
                    InStock: InStock,
                    CatProID: CatProID,
                    Status: Status,
                    SortName: sortName,
                    SortType: sortType
                }, function (data) {
                    dataContent.append(data);
                    window.scrollTo(left, top);
                });
            };
            $("#cboRowsPerPage").change(function () {
                show = $(this).val();
                page = 1;
                Filter(page, show);
            });
            $(".filtering").each(function () {
                $(this).keyup(function () {
                    Filter(1, show);
                });
                $(this).change(function () {
                    Filter(1, show);
                });
            });
        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');

}

