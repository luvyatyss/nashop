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
    require_once '../entities/Device.php';
    require_once '../entities/Brand.php';

    require_once '../helper/Controls.php';


    $page = new Page();

    $page->addCSS("assets/js/sweetalert/sweetalert.css");

    $page->addJavascript("assets/js/jquery.validate.min.js");
    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->startBody();

    $insert = null;
    $update = null;
    $delete = null;

    $page->startBody();

    $control = new Controls(Controls::Insert); //Chưa chọn loại sản phẩm;
    $Category = new Category(-1, "", new Brand(), new Device(), -1);

    if (isset($_POST["btnSave"])) {
        if (isset($_POST["txtControl"])) {
            $control->setValue($_POST["txtControl"]);
        }
        if (isset($_POST["txtCatProID"])) {
            $Category->setCatID($_POST["txtCatProID"]);
        }
        if (isset($_POST["txtCatName"])) {
            $Category->setCatName($_POST["txtCatName"]);
        }
        if (isset($_POST["cboDevice"])) {
            $oDevice = new Device($_POST["cboDevice"]);
            $Category->setDevice($oDevice);
        }
        if (isset($_POST["cboBrand"])) {
            $oBrand = new Brand($_POST["cboBrand"]);
            $Category->setBrand($oBrand);
        }

        if ($control == Controls::Insert) {
            $Category->insert();
            $insert = true;

        } else if ($control == Controls::Update) {
            if (isset($_POST["chkStatus"])) {
                $Category->setStatus($_POST["chkStatus"]);
            } else {
                $Category->setStatus(0);
            }
            $Category->update();
            $update = true;
        }
    }
    else if (isset($_GET["CatProID"]) && isset($_GET["control"])) {
        $control->setValue($_GET["control"]);
        $Category = Category::getCat($_GET["CatProID"]);
        if ($Category != null) {
            if ($control == Controls::Update ) {
                echo "<script> $(function () { $(window).load(function(){ $('#modalCat').modal( { backdrop: 'static', keyboard: false }, 'show');})});</script>";
            } else if ($control == Controls::Delete) {
                $Category->delete();
                $delete = true;
            }
        } else {
            require_once '../helper/Utils.php';
            $url = "categories.php";
            Utils::Redirect($url);
        }
    }

//Load danh mục thiết bị
    $ListDevices = array();
    $ListDevices = Device::loadAll();
    $ListBrands = array();
    $ListBrands = Brand::loadAll();
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
            <a type="button" class="btn btn-primary pull-right" data-placement="top" title="Thêm" name="btnAdd"
               id="btnAdd" data-toggle="modal" data-target="#modalCat" data-backdrop="static"
               data-keyboard="false">
                <strong><i class="entypo-plus"></i></strong>
            </a>
        </div>
        <h1>Product</h1>
        <ol class="breadcrumb bc-3">
            <li>
                <a href="index.php">Trang Chủ</a>
            </li>
            <li class="active">
                <strong>Loại Sản Phẩm</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Loại Sản Phẩm</span>
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
                        <th style="width: 200px" data-name="CatProID">ID</th>
                        <th style="width: 200px" data-name="CatName">Loại Sản Phẩm</th>
                        <th style="width: 200px" data-name="BraID">Nhà Sản Xuất</th>
                        <th style="width: 200px" data-name="DeviceID">Thiết Bị</th>
                        <th data-name="Discontinued">Tình Trạng</th>
                    </tr>
                    <tr class="replace-inputs">
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterCatProID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Loại Sản Phẩm" id="txtFilterCatName">
                        </th>
                        <th class="filtering">
                            <!-- Load Danh muc NSX-->
                            <select class="form-control" name="cboFilterBrands" id="cboFilterBrands">
                                <option value="-1">Chọn ...</option>
                                <?php
                                foreach ($ListBrands as $ItemBrand) {
                                    ?>
                                    <option value=<?php echo $ItemBrand->getBraID(); ?>>
                                        <?php echo $ItemBrand->getBraName(); ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <!-- /Load Danh muc NSX-->
                        </th>
                        <th class="filtering">
                            <!-- Load Các loại thiết bị-->
                            <select class="form-control" name="cboFilterDeives" id="cboFilterDeives">
                                <option value="-1">Chọn ...</option>
                                <?php
                                foreach ($ListDevices as $ItemDev) {
                                    ?>
                                    <option value=<?php echo $ItemDev->getDeviceID(); ?>>
                                        <?php echo $ItemDev->getDeviceName(); ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <!-- /Load Các loại thiết bị-->
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
    <div class="modal" id="modalCat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="frmModify" name="frmModify" method="post"
                      action="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">THÔNG TIN LOẠI SẢN PHẨM</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="txtControl" id="txtControl"
                                   value=<?php echo $control ?>>

                            <div class="col-md-7">
                                <input type="hidden" class="form-control" name="txtCatProID" id="txtCatProID"
                                       value=<?php if ($control == Controls::Update) echo $Category->getCatID(); ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtCatName">Loại Sản Phẩm:</label>

                            <div class="col-md-7">
                                <input type="text" class="form-control" name="txtCatName" id="txtCatName"
                                    <?php if ($control == Controls::Update) {
                                        echo 'value="' . $Category->getCatName() . '"';
                                    } ?>
                                >
                            </div>
                        </div>
                        <!--Combobox Nhà Sản Xuất-->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtCatName">Nhà Sản Xuất:</label>

                            <div class="col-md-7">

                                <select class="form-control" name="cboBrand" id="cboBrand">
                                    <?php
                                    foreach ($ListBrands as $ItemBrand) {
                                        $option = "<option ";
                                        if ($control == Controls::Update && $ItemBrand->getBraID() == $Category->getBrand()->getBraID()) {
                                            $option .= "selected ";
                                        }
                                        $option .= "value= " . $ItemBrand->getBraID();
                                        $option .= " >";
                                        $option .= $ItemBrand->getBraName();
                                        $option .= "</option>";
                                        echo $option;
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <!--/Combobox Nhà Sản Xuất-->
                        <!--Combobox Thiết Bị-->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtCatName">Thiết Bị:</label>

                            <div class="col-md-7">

                                <select class="form-control" name="cboDevice" id="cboDevice">
                                    <?php
                                    foreach ($ListDevices as $ItemDev) {
                                        $option = "<option ";
                                        if ($control == Controls::Update && $ItemDev->getDeviceID() == $Category->getDevice()->getDeviceID()) {
                                            $option .= "selected ";
                                        }
                                        $option .= "value= " . $ItemDev->getDeviceID();
                                        $option .= " >";
                                        $option .= $ItemDev->getDeviceName();
                                        $option .= "</option>";
                                        echo $option;
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <!--/Combobox Thiết Bị-->


                        <div class="checkbox col-md-offset-4 <?php if ($control != Controls::Update) echo "hidden" ?>"
                             id="status">
                            <label class="col-md-8">
                                <input type="checkbox" name="chkStatus" id="chkStatus"
                                    <?php
                                    if ($Category->getStatus()) {
                                        echo "checked";
                                    }
                                    echo " value=" . $Category->getStatus();
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
                    txtCatName: {
                        required: true,
                    },
                    cboDevice: "required",
                    cboBrand: "required"
                },
                messages: {
                    txtCatName: {
                        required: "Vui lòng nhập Loại Sản Phẩm !"
                    },
                    cboDevice: "Vui lòng chọn Loại Thiết Bị !",
                    cboBrand: "Vui lòng chọn Nhà Sản Xuất !"
                }
            });

            $("#chkStatus").click(function () {
                $(this).val($(this).val() == 0 ? 1 : 0);
            });

            var resetForm = function () {
                $("#txtCatProID").val("");
                $("#txtCatName").val("");
                $("#cboBrand").val("-1");
                $("#cboDevice").val("-1");
                $("#status").addClass("hidden");
            }

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
                var CatProID = $('#txtFilterCatProID').val();
                var CatName = $('#txtFilterCatName').val();
                var DeviceID = $('#cboFilterDeives').val();
                var BraID = $('#cboFilterBrands').val();
                var Status = $('#cboFilterStatus').val();

                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('categoriesBody.php', {
                    page: page,
                    show: show,
                    CatProID: CatProID,
                    CatName: CatName,
                    BraID: BraID,
                    DeviceID: DeviceID,
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
                    var url = "categories.php?token=" + "<?php echo $_GET["token"] ?>" ;
                    window.location.href = url;
                });
            }

        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');
}



