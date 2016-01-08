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
    require_once '../entities/Order.php';
    require_once '../helper/File.php';
    require_once '../helper/Controls.php';


    $page = new Page();

    $page->addCSS("assets/js/jRange-master/jquery.range.css");
    $page->addCSS("assets/css/jrange.css");
    $page->addCSS("assets/js/sweetalert/sweetalert.css");


    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->addJavascript("assets/js/jRange-master/jquery.range.js");
    $page->startBody();


    $update = null;

    $control = new Controls(Controls::Insert); //Chưa chọn loại sản phẩm;
    $Order = new Order();

    $maxTotal = Order::getValueMaxColName('Total');
    $maxTotal = empty($maxTotal) ? 0 : ceil($maxTotal / 1000000); // /1 triệu đồng

    if (isset($_POST["btnSave"])) {

        if (isset($_POST["txtOrderID"])) {
            $Order->setOrderID($_POST["txtOrderID"]);
        }
        if (isset($_POST["cboStatus"])) {
            $Order->setStatus(new Status($_POST["cboStatus"]));
        }
        $Order->update();
        $update = true;
    } else if (isset($_GET["OrderID"]) && isset($_GET["control"])) {
        $Order = Order::getOrder($_GET["OrderID"]);
        if ($Order != null) {
            echo "<script> $(function () { $(window).load(function(){ $('#modalOrder').modal( { backdrop: 'static', keyboard: false }, 'show');});  });</script>";
        } else {
            require_once '../helper/Utils.php';
            $url = "orders.php";
            Utils::Redirect($url);
        }
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
                <strong>Hóa Đơn</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Hóa Đơn</span>
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
                        <th style="width: 200px" data-name="OrderID">ID</th>
                        <th style="width: 200px" data-name="UserID">Khách Hàng</th>
                        <th style="width: 200px" data-name="OrderDate">Ngày Lập</th>
                        <th style="width: 200px" data-name="Total">Tổng tiền</th>
                        <th data-name="StatusID">Tình Trạng</th>
                    </tr>
                    <tr class="replace-inputs">
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterDeviceID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Khách Hàng" id="txtFilterUserID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Ngày Lập" id="txtFilterOrderDate">
                        </th>
                        <th class="filteringRange">
                            <input type="hidden" id="txtFilterTotal" value=""/>
                        </th>
                        <th class="filtering">
                            <select class="form-control" id="cboFilterStatus">
                                <option value="-1">Chọn ...</option>
                                <?php
                                require_once "../entities/Status.php";
                                $ListStatuses = Status::loadAll();
                                foreach ($ListStatuses as $ItemSta) {
                                    ?>
                                    <option value=<?php echo $ItemSta->getStatusID(); ?>>
                                        <?php echo $ItemSta->getStatusName(); ?>
                                    </option>
                                    <?php
                                } ?>
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

    <!-- Modal Loai san pham -->
    <div class="modal" id="modalOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="frmModify" name="frmModify" method="post" action=""
                      enctype=multipart/form-data>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">THÔNG TIN THIẾT BỊ</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="cboStatus">Mã Đơn Hàng:</label>

                            <div class="col-md-7">
                                <input type="text" readonly class="form-control" name="txtOrderID" id="txtOrderID"
                                       value=<?php echo $Order->getOrderID(); ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="cboStatus">Tình Trạng:</label>

                            <div class="col-md-7">
                                <select class="form-control" id="cboStatus" name="cboStatus">
                                    <?php
                                    require_once "../entities/Status.php";
                                    $ListStatuses = Status::loadAll();
                                    foreach ($ListStatuses as $ItemSta) {
                                        $option = "<option ";
                                        if ($ItemSta->getStatusID() == $Order->getStatus()->getStatusID()) {
                                            $option .= "selected ";
                                        }
                                        $option .= "value= " . $ItemSta->getStatusID();
                                        $option .= " >";
                                        $option .= $ItemSta->getStatusName();
                                        $option .= "</option>";
                                        echo $option;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer ">
                        <div class="col-md-11">
                            <button type="submit" class="btn btn-primary" name="btnSave"><i class="entypo-floppy"></i>
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" name="btnCancel"><i
                                    class="entypo-reply"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--/ Modal Loai san pham -->
    <script>

        $(document).ready(function () {
            var maxTotal = <?php echo $maxTotal ; ?>;
            var step = maxTotal < 100 ? 1 : maxTotal < 1000 ? 10 : 100
            var rangeTotal = $('#txtFilterTotal').jRange({
                from: 0,
                to: maxTotal,
                step: step,
                format: function (value, type) {
                    var text = value.format(0, 3, ',') + " tr";
                    return text;
                },
                width: 180,
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
            rangeTotal.jRange('updateRange', '0,' + maxTotal, '0,' + maxTotal);

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
                var OrderID = $('#txtFilterOrderID').val();
                var UserID = $('#txtFilterUserID').val();
                var Total = $('#txtFilterTotal').val().split(',');

                var Status = $('#cboFilterStatus').val();

                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('ordersBody.php', {
                    page: page,
                    show: show,
                    OrderID: OrderID,
                    UserID: UserID,
                    Total: Total,
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

            var _update = "<?php echo $update == null ? "" : $update ; ?>";
            if (_update) {
                swal({
                    title: "Thành công!",
                    type: "success",
                    confirmButtonText: "OK"
                }, function () {
                    var url = "orders.php?token=" + "<?php echo $_GET["token"] ?>";
                    window.location.href = url;
                });
            }
        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');
}



