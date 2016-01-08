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
    require_once '../entities/Device.php';
    require_once '../helper/File.php';
    require_once '../helper/Controls.php';

    $page = new Page();

    $page->addCSS("assets/js/sweetalert/sweetalert.css");

    $page->addJavascript("assets/js/jquery.validate.min.js");
    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->startBody();

    $insert = null;
    $update = null;
    $delete = null;

    $control = new Controls(Controls::Insert); //Chưa chọn loại sản phẩm;
    $Device = new Device();
    if (isset($_POST["btnSave"])) {
        if (isset($_POST["txtControl"])) {
            $control->setValue($_POST["txtControl"]);
        }
        if (isset($_POST["txtDeviceID"])) {
            $Device->setDeviceID($_POST["txtDeviceID"]);
        }
        if (isset($_POST["txtDeviceName"])) {
            $Device->setDeviceName($_POST["txtDeviceName"]);
        }
        if ($control == Controls::Insert) {
            $Device->insert();
            $insert = true;
        } else if ($control == Controls::Update) {
            if (isset($_POST["chkStatus"])) {
                $Device->setStatus($_POST["chkStatus"]);
            } else {
                $Device->setStatus(0);
            }
            $Device->update();
            $update = true;
        }
    }
    else if (isset($_GET["DeviceID"]) && isset($_GET["control"])) {
        $control->setValue($_GET["control"]);

        $Device = Device::getDevice($_GET["DeviceID"]);
        if ($Device != null) {
            if ($control == Controls::Update) {
                echo "<script> $(function () { $(window).load(function(){ $('#modalDevice').modal( { backdrop: 'static', keyboard: false }, 'show');});  });</script>";
            } else if ($control == Controls::Delete) {
                $Device->delete();
                $delete = true;
            }
        } else {
            require_once '../helper/Utils.php';
            $url = "devices.php";
            Utils::Redirect($url);
        }
    }


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
            <a type="button" class="btn btn-primary pull-right" name="btnAdd"
               id="btnAdd" data-toggle="modal" data-target="#modalDevice" data-backdrop="static"
               data-keyboard="false">
                <strong><i class="entypo-plus"></i></strong>
            </a>
        </div>
        <h1>Device</h1>
        <ol class="breadcrumb bc-3">
            <li>
                <a href="index.php">Trang Chủ</a>
            </li>
            <li class="active">
                <strong>Thiết Bị</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Thiết Bị</span>
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
                        <th style="width: 200px" data-name="DeviceID">ID</th>
                        <th style="width: 200px" data-name="DeviceName">Thiết Bị</th>
                        <th style="width: 200px" data-name="Discontinued">Tình Trạng</th>
                    </tr>
                    <tr class="replace-inputs">
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterDeviceID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Thiết Bị" id="txtFilterDeviceName">
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

    <!-- Modal Loai san pham -->
    <div class="modal" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                            <input type="hidden" class="form-control" name="txtControl" id="txtControl"
                                   value=<?php echo $control ?>>

                            <div class="col-md-7">
                                <input type="hidden" class="form-control" name="txtDeviceID" id="txtDeviceID"
                                       value=<?php if ($control == Controls::Update) echo $Device->getDeviceID(); ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtDeviceName">Thiết Bị:</label>

                            <div class="col-md-7">
                                <input type="text" class="form-control" name="txtDeviceName" id="txtDeviceName"
                                    <?php if ($control == Controls::Update) {
                                        echo 'value="' . $Device->getDeviceName() . '"';
                                    } ?>
                                >
                            </div>
                        </div>
                        <div class="checkbox col-md-offset-4 <?php if ($control != Controls::Update) echo "hidden" ?>"
                             id="status">
                            <label class="col-md-8">
                                <input type="checkbox" name="chkStatus" id="chkStatus"
                                    <?php
                                    if ($Device->getStatus()) {
                                        echo "checked";
                                    }
                                    echo " value=" . $Device->getStatus();
                                    ?>
                                > Tạm ngưng
                            </label>
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

            //kiem tra du lieu
            var validator = $("#frmModify").validate({
                rules: {
                    txtDeviceName: {
                        required: true,
                    }
                },
                messages: {
                    txtDeviceName: {
                        required: "Vui lòng nhập Nhà Sản Xuất  !"
                    }
                }
            });

            $("#chkStatus").click(function () {
                $(this).val($(this).val() == 0 ? 1 : 0);
            });

            var resetForm = function () {
                $("#txtDeviceID").val("");
                $("#txtDeviceName").val("");
                $("#status").addClass("hidden");
            };

            $("#btnAdd").click(function () {
                resetForm();
                $('#txtControl').val("<?php echo Controls::Insert ?>");
            });


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
                var DeviceID = $('#txtFilterDeviceID').val();
                var DeviceName = $('#txtFilterDeviceName').val();
                var Status = $('#cboFilterStatus').val();

                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('devicesBody.php', {
                    page: page,
                    show: show,
                    DeviceID: DeviceID,
                    DeviceName: DeviceName,
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
            var _insert = "<?php echo $insert == null ? "" : $insert ; ?>";
            var _update = "<?php echo $update == null ? "" : $update ; ?>";
            var _delete = "<?php echo $delete == null ? "" : $delete ; ?>";
            if (_insert || _update || _delete) {
                swal({
                    title: "Thành công!",
                    type: "success",
                    confirmButtonText: "OK"
                }, function () {
                    var url = "devices.php?token=" + "<?php echo $_GET["token"] ?>" ;
                    window.location.href = url;
                });
            }
        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');
}


