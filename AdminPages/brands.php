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
    require_once '../entities/Brand.php';
    require_once '../helper/File.php';
    require_once '../helper/Controls.php';

    $page = new Page();

    $page->addCSS("assets/js/sweetalert/sweetalert.css");

    $page->addJavascript("assets/js/jquery.validate.min.js");
    $page->addJavascript("assets/js/fileinput.js");
    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->startBody();

    function addLogo(Brand $Brand)
    {
        if (isset($_FILES['fileLogoURL']) && $_FILES['fileLogoURL']['size'] > 0) {
            $errors = array();
            $fileName = $_FILES['fileLogoURL']['name'];
            $tmpName = $_FILES['fileLogoURL']['tmp_name'];
            $fileSize = $_FILES['fileLogoURL']['size'];
            $fileType = $_FILES['fileLogoURL']['type'];
            $File = new File($fileName, $tmpName, $fileSize, $fileType);
            if ($fileSize > 2097152) {
                $errors[] = 'File phải nhỏ hơn 2 MB';
            }
            if (!$File->isImageType()) {
                $errors[] = "";
            }
            if (empty($errors) == true) {
                //remove Logo old
                $logo_Old = trim($Brand->getLogoURL(), '"');
                if (file_exists($logo_Old)) {
                    unlink($logo_Old);
                }
                //Add logo new
                $path = '../assets/images/logoBrands/' . $Brand->getBraID();
                if (!file_exists($path)) {// neu k ton tai duong dan thu muc cua id nay thi tạo mới
                    File::createDirectory($path);
                }
                $type = explode("/", $File->getFileType())[1];
                $find = array(" ", "\\", "/", ":", "*", "?", "\"", "<", ">", "|");
                $name = File::utf8convert(str_replace($find, '', $Brand->getBraName()));
                $pathNew = $path . '/logo_' . $name . "." . $type;
                $File->moveFile($pathNew);
                $Brand->setLogoURL($pathNew);
                $Brand->updateLogo();
            } else {
                //print_r($errors);
            }
            if (empty($error)) {
                //echo "Success";
            }
        }

    }

    $insert = null;
    $update = null;
    $delete = null;

    $control = new Controls(Controls::Insert); //Chưa chọn loại sản phẩm;
    $Brand = new Brand();

// khi chọn loại sản phẩm
    if (isset($_POST["btnSave"])) {
        if (isset($_POST["txtControl"])) {
            $control->setValue($_POST["txtControl"]);
        }
        if (isset($_POST["txtBraID"])) {
            $Brand->setBraID($_POST["txtBraID"]);
        }
        if (isset($_POST["txtBraName"])) {
            $Brand->setBraName($_POST["txtBraName"]);
        }
        if ($control == Controls::Insert) {
            if ($Brand->insert() > 0) {
                addLogo($Brand);
                $insert = true;
            }
        } else if ($control == Controls::Update) {
            if (isset($_POST["txtLogoURL_Old"])) {
                $Brand->setLogoURL($_POST["txtLogoURL_Old"]);
            }
            if (isset($_POST["chkStatus"])) {
                $Brand->setStatus($_POST["chkStatus"]);
            } else {
                $Brand->setStatus(0);
            }
            $Brand->update();
            addLogo($Brand);
            $update = true;
        }
    }
    else if (isset($_GET["BraID"]) && isset($_GET["control"])) {
        $control->setValue($_GET["control"]);
        $Brand = Brand::getBrand($_GET["BraID"]);
        if ($Brand != null) {
            if ($control == Controls::Update) {
                echo "<script> $(function () { $(window).load(function(){ $('#modalBra').modal( { backdrop: 'static', keyboard: false }, 'show');});  });</script>";
            } else if ($control == Controls::Delete) {
                $Brand->delete();
                $path = '../assets/images/logoBrands/' . $Brand->getBraID();
                if (file_exists($path)) {
                    File::removeDirectoryAllFiles($path);
                }
                $delete = true;
            }
        } else {
            require_once '../helper/Utils.php';
            $url = "brands.php";
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
            <a type="button" class="btn btn-primary pull-right" name="btnAdd" id="btnAdd" data-toggle="modal"
               data-target="#modalBra" data-backdrop="static"
               data-keyboard="false">
                <strong><i class="entypo-plus"></i></strong>
            </a>
        </div>
        <h1>Brand</h1>
        <ol class="breadcrumb bc-3">
            <li>
                <a href="index.php">Trang Chủ</a>
            </li>
            <li class="active">
                <strong>Nhà Sản Xuất</strong>
            </li>
        </ol>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span><i class="entypo-list"></i>Danh Sách Nhà Sản Xuất</span>
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
                        <th style="width: 200px" data-name="BraID">ID</th>
                        <th style="width: 200px" data-name="BraName">Nhà Sản Xuất</th>
                        <th style="width: 200px" data-name="LogoURL">Logo</th>
                        <th style="width: 200px" data-name="Discontinued">Tình Trạng</th>
                    </tr>
                    <tr class="replace-inputs">
                        <th></th>
                        <th class="filtering">
                            <input class="" type="text" id="txtFilterBraID" placeholder="ID">
                        </th>
                        <th class="filtering">
                            <input class="" type="text" placeholder="Nhà Sản Xuất" id="txtFilterBraName">
                        </th>
                        <th></th>
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
    <div class="modal fade" id="modalBra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="frmModify" name="frmModify" method="post" action=""
                      enctype="multipart/form-data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">THÔNG TIN NHÀ SẢN XUẤT</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="txtControl" id="txtControl"
                                   value=<?php echo $control ?>>

                            <div class="col-md-7">
                                <input type="hidden" class="form-control" name="txtBraID" id="txtBraID"
                                       value=<?php if ($control == Controls::Update) echo $Brand->getBraID(); ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="txtBraName">Nhà Sản Xuất:</label>

                            <div class="col-md-7">
                                <input type="text" class="form-control" name="txtBraName" id="txtBraName"
                                    <?php if ($control == Controls::Update) {
                                        echo 'value="' . $Brand->getBraName() . '"';
                                    } ?>
                                >
                            </div>
                        </div>
                        <!--Logo-->
                        <div class="form-group" id="logoBrand">
                            <label class="col-md-4 control-label" for="txtBraName">Logo:</label>

                            <div class="fileinput col-md-7 fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 128px;" data-trigger="fileinput">
                                    <img class="logo-brand" src="http://placehold.it/128x128" alt="...">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"
                                     style="max-width: 128px; ">
                                    <?php
                                    echo '<img class="logo-brand" src="' . $Brand->getLogoURL() . '" alt="' . $Brand->getBraName() . '">';
                                    ?>
                                </div>
                                <div class="btn-modify-group">
									<span class="btn btn-default btn-file">
										<span class="fileinput-new" title="Chọn ảnh"><i
                                                class="entypo-upload"></i></span>
										<span class="fileinput-exists" data-placement="top" title="Thay đổi"><i
                                                class="entypo-pencil"></i></span>
										<input type="file" accept="image/*" name="fileLogoURL" id="fileLogoURL">
									</span>
                                    <a href="#" id="btnRemoveURL" class="btn btn-delete fileinput-exists"
                                       data-dismiss="fileinput">
                                        <i class="entypo-cancel"></i>
                                    </a>
                                </div>
                                <input type="hidden" name="txtLogoURL_Old" id="txtLogoURL_Old"
                                       value='"<?php echo $Brand->getLogoURL(); ?>"'>
                                <input type="hidden" name="txtLogoURL" id="txtLogoURL">
                            </div>
                        </div>
                        <!--/Logo-->
                        <div class="checkbox col-md-offset-4 <?php if ($control != Controls::Update) echo "hidden" ?>"
                             id="status">
                            <label class="col-md-8">
                                <input type="checkbox" name="chkStatus" id="chkStatus"
                                    <?php
                                    if ($Brand->getStatus()) {
                                        echo "checked";
                                    }
                                    echo " value=" . $Brand->getStatus();
                                    ?>
                                > Tạm ngưng
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer ">
                        <div class="col-md-11">
                            <button type="submit" class="btn btn-primary" id="btnSave" name="btnSave"><i
                                    class="entypo-floppy"></i>
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

            $(window).load(function () {
                var control = parseInt("<?php echo $control; ?>");
                var controlUpdate = parseInt("<?php echo Controls::Update; ?>");
                var logoBrand = "<?php echo trim($Brand->getLogoURL(), '"');?> ";
                if (control === controlUpdate) {
                    logoBrand = logoBrand.trim() ;
                    if (logoBrand.trim().length > 0) {
                        var fileInput = $('#logoBrand .fileinput');
                        if (fileInput.hasClass("fileinput-new")) {
                            fileInput.removeClass("fileinput-new");
                            fileInput.addClass("fileinput-exists");
                        }
                        $('#txtLogoURL').val(logoBrand);
                    }
                }
            });
            //Form
            //kiem tra du lieu
            $.validator.addMethod(
                "regexp",
                function (value, element, regexp) {
                    var re = new RegExp(regexp);
                    return this.optional(element) || re.test(value.trim());
                },
                "Please check your input."
            );
            var validator = $("#frmModify").validate({
                ignore: [],
                rules: {
                    txtBraName: {
                        required: true,
                    },
                    txtLogoURL: {
                        required: true,
                        regexp: /^.+\.(jpg|JPG|png|PNG|jpeg|JPEG)$/
                    }
                },
                messages: {
                    txtBraName: {
                        required: "Vui lòng nhập Nhà Sản Xuất  !"
                    },
                    txtLogoURL: {
                        required: "Vui lòng Chọn Ảnh !",
                        regexp: "Vui lòng chọn Ảnh có định dạng .jpg , .png hoặc .jpeg  !"
                    }
                }

            });

            $("#chkStatus").click(function () {
                $(this).val($(this).val() == 0 ? 1 : 0);
            });

            $(".fileinput").on("change.bs.fileinput", function () {
                var e = $("#txtLogoURL");
                var filename = $('#fileLogoURL').val().split('\\').pop();
                e.val(filename);
                if ($("#logoBrand .fileinput").hasClass("fileinput-new")) {
                    e.val("");
                }
                validator.element("#txtLogoURL");
            });
            var resetForm = function () {
                $('#txtControl').val("<?php echo Controls::Insert ?>");
                $("#txtBraID").val("");
                $("#txtBraName").val("");
                $("#status").addClass("hidden");
                //$('#boardChangePassWord').remove();
                validator.resetForm();
                $('.error').each(function () {
                    $(this).removeClass('error');
                });
                $("#logoBrand .fileinput").fileinput('reset');
            };
            $("#btnAdd").click(function () {
                resetForm();
            });

            //End Form


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
                var BraID = $('#txtFilterBraID').val();
                var BraName = $('#txtFilterBraName').val();
                var Status = $('#cboFilterStatus').val();

                var dataContent = $('#dataContent');
                dataContent.text("");
                var doc = document.documentElement;
                var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                $.post('brandsBody.php', {
                    page: page,
                    show: show,
                    BraID: BraID,
                    BraName: BraName,
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
                    var url = "brands.php?token=" + "<?php echo $_GET["token"] ?>" ;
                    window.location.href = url;
                });
            }

        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');
}


