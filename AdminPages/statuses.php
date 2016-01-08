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
    require_once '../entities/Status.php';
    require_once '../helper/File.php';
    require_once '../helper/Controls.php';


    $page = new Page();

    $page->addCSS("assets/js/sweetalert/sweetalert.css");

    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->addJavascript("assets/js/jquery.validate.min.js");
    $page->addJavascript("assets/js/bootstrap-colorpicker.js");

    $page->startBody();

    $insert = null;
    $update = null;
    $delete = null;

    $control = new Controls(Controls::Insert); //Chưa chọn loại sản phẩm;
    $Status = new Status();
// khi chọn loại sản phẩm
    if (isset($_POST["btnSave"])) {
        if (isset($_POST["txtControl"])) {
            $control->setValue($_POST["txtControl"]);
        }
        if (isset($_POST["txtStatusID"])) {
            $Status->setStatusID($_POST["txtStatusID"]);
        }
        if (isset($_POST["txtStatusName"])) {
            $Status->setStatusName($_POST["txtStatusName"]);
        }
        if (isset($_POST["txtStatusColor"])) {
            $Status->setStatusColor($_POST["txtStatusColor"]);
        }
        if ($control == Controls::Insert) {
            $Status->insert();
            $insert = true;
        } else if ($control == Controls::Update) {
            $Status->update();
            $update = true;
        }
    } else if (isset($_GET["StatusID"]) && isset($_GET["control"])) {
        $control->setValue($_GET["control"]);

        $Status = Status::getStatus($_GET["StatusID"]);
        if ($control == Controls::Update) {
            echo "<script> $(function () { $(window).load(function(){ $('#modalStatus').modal( { backdrop: 'static', keyboard: false }, 'show');});  });</script>";
        } else if ($control == Controls::Delete) {
            $Status->delete();
            $delete = true;

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
               id="btnAdd" data-toggle="modal" data-target="#modalStatus" data-backdrop="static"
               data-keyboard="false">
                <strong><i class="entypo-plus"></i></strong>
            </a>
        </div>
        <h1>Status</h1>
        <ol class="breadcrumb bc-3">
            <li>
                <a href="index.php">Trang Chủ</a>
            </li>
            <li class="active">
                <strong>Tình Trạng</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Tình Trạng</span>
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
                        <th style="width: 200px" data-name="StatusID">ID</th>
                        <th style="width: 200px" data-name="StatusName">Tình Trạng</th>
                        <th style="width: 200px">Màu Sắc</th>
                    </tr>
                    <tr class="replace-inputs">
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterStatusID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Tình Trạng" id="txtFilterStatusName">
                        </th>
                        <th class="filtering">
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
    <div class="modal" id="modalStatus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="frmModify" name="frmModify" method="post" action=""
                      enctype=multipart/form-data>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">THÔNG TIN TÌNH TRẠNG</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="txtControl" id="txtControl"
                                   value=<?php echo $control ?>>

                            <div class="col-md-7">
                                <input type="hidden" class="form-control" name="txtStatusID" id="txtStatusID"
                                       value=<?php if ($control == Controls::Update) echo $Status->getStatusID(); ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtStatusName">Tình Trạng:</label>

                            <div class="col-md-7">
                                <input type="text" class="form-control" name="txtStatusName" id="txtStatusName"
                                    <?php if ($control == Controls::Update) {
                                        echo 'value="' . $Status->getStatusName() . '"';
                                    } ?>
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtStatusName">Màu sắc:</label>

                            <div class="col-md-7 input-group " id="status_color">
                                <input type="text" readonly class="form-control" name="txtStatusColor"
                                       id="txtStatusColor"
                                    <?php if ($control == Controls::Update) {
                                        echo 'value="' . $Status->getStatusColor() . '"';
                                    } ?>
                                >
                                <span class="input-group-addon"><i style="background:
                                        <?php if ($control == Controls::Update) {
                                        echo $Status->getStatusColor();
                                    } ?>"></i>
                                </span>
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
            var Status_corpicker = $('#status_color').colorpicker();
            //kiem tra du lieu
            var validator = $("#frmModify").validate({
                rules: {
                    txtStatusName: {
                        required: true,
                    },
                    txtStatusColor: {
                        required: true,
                    }

                },
                messages: {
                    txtStatusName: {
                        required: "Vui lòng nhập Tình Trạng  !"
                    },
                    txtStatusColor: {
                        required: "Vui lòng chọn Màu Sắc  !"
                    }
                }
            });


            var resetForm = function () {
                $("#txtStatusID").val("");
                $("#txtStatusName").val("");
                $("#txtStatusColor").val("");
                $("#status_color span>i").css("background", "none");
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
                var StatusID = $('#txtFilterStatusID').val();
                var StatusName = $('#txtFilterStatusName').val();
                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('statusesBody.php', {
                    page: page,
                    show: show,
                    StatusID: StatusID,
                    StatusName: StatusName,
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
                    /*var token = location.search.split("&").pop();
                    var newURL = window.location.protocol + "//" + window.location.host + "/" + window.location.pathname;
                    window.location.href = newURL + "?" + token;*/
                    var url = "statuses.php?token=" + "<?php echo $_GET["token"] ?>" ;
                    window.location.href = url;
                });
            }
        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');

}
