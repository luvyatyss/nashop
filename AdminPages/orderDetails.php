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
    require_once '../helper/Controls.php';

    $page = new Page();

    $page->startBody();
    $OrderID = "";
    if (isset($_GET["OrderID"]) && !empty($_GET["OrderID"])) {
        $OrderID = $_GET["OrderID"];
    } else {
        require_once '../helper/Utils.php';
        $url = "orders.php";
        Utils::Redirect($url);
    }
    ?>
    <div class="page-header">
        <div class="pull-right">
        </div>
        <h1>Order</h1>
        <ol class="breadcrumb bc-3">
            <li>
                <a href="index.php">Trang Chủ</a>
            </li>
            <li class="active">
                <strong>Chi Tiết Hóa Đơn</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Chi Tiết Hóa Đơn</span>
            <span class="pull-right"
                  style="color: #ff7457; font-weight: bold; font-size: 15px;">HĐ: <?php echo str_pad($OrderID, 6, "0", STR_PAD_LEFT);; ?></span>
            <input type="hidden" id="txtOrderID" value="<?php echo $OrderID; ?>">
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
                        <th style="width: 200px" data-name="OrderDetailID">ID</th>
                        <th style="width: 200px" data-name="ProID">Sản Phẩm</th>
                        <th style="width: 200px" data-name="Quantity">Số Lượng</th>
                        <th style="width: 200px" data-name="Price">Đơn Giá</th>
                        <th style="width: 200px" data-name="Amount">Thành Tiền</th>
                    </tr>
                    <!--
                    <tr class="replace-inputs">
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterOrderDetailID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Sản Phẩm" id="txtFilterProductName">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Số Lượng" id="txtFilterQuantity">
                        </th>
                    </tr>
                    -->
                    </thead>
                </table>
                <div id="dataContent">

                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {

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
                var OrderDetailID = $('#txtFilterOrderDetailID').val();
                var OrderID = $('#txtOrderID').val();

                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('orderDetailsBody.php', {
                    page: page,
                    show: show,
                    OrderDetailID: OrderDetailID,
                    OrderID: OrderID,
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