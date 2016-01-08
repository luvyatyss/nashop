<?php
if (!isset($_SESSION)) {
    session_start();
}

$fail = false;

if (!isset($_GET["token"]) || $_SESSION["IsLogin"] == 0) {
    $fail = true;
} else {
    require_once './helper/crypter.php';
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
    require_once './helper/Utils.php';
    $_SESSION['showModalLogin'] = true;
    $url = "index.php";
    Utils::Redirect($url);

} else {
    require_once './helper/Page.php';
    require_once './helper/Context.php';
    $page = new Page();
    $page->setTitle('Thông Tin Cá Nhân');
    $page->addCSS("assets/css/profile.css");

    $page->addJavascript("assets/js/jquery.inputmask.bundle.min.js");
    $page->startBody();

    $update = null;
    //update PassWord
    $updatePW = true;
    date_default_timezone_set('Asia/Bangkok');

    $User = new User();
    if (isset($_POST["btnUpdate"])) {
        $User->setUserID($_POST["txtUserID"]);
        $User->setUserName($_POST["txtUserName"]);
        $User->setEmail($_POST["txtEmail"]);
        $dateOfBirth = new DateTime(str_replace('/', '-', $_POST["txtBirthDay"])); //d-m-Y
        $User->setDateOfBirth($dateOfBirth);
        $User->setFullName($_POST["txtFullName"]);
        $User->setGender($_POST["cboGender"]);
        $User->setUserPermission(0);
        $userLastModified = new DateTime();
        $User->setUserLastModified($userLastModified);
        $User->setUserPassWord(Context::getCurrentUser()["userPassWord"]);

        $User->update();

        $_SESSION["CurrentUser"] = (array)$User;
        $update = true;
        unset($_SESSION['captcha']);

    } else if (isset($_POST["btnSave"]) && isset($_POST["txtPassWordOld"])) {
        $messagePW = "";
        $User->setUserID(Context::getCurrentUser()["userID"]);
        $passWordOld = $_POST["txtPassWordOld"];
        if (Context::getCurrentUser()["userPassWord"] == $passWordOld) {
            $User->setUserPassWord($_POST["txtPassWordNew"]);
            if ($User->updatePassWord()) {
                $updatePW = true;
                $_SESSION["CurrentUser"]["userPassWord"] = $_POST["txtPassWordNew"];
                $update = true;
            } else {
                $updatePW = false;
            }
        } else {
            $updatePW = false;
        }
        if (!$updatePW) {

            $javascript = "<script> $(function () "
                . "{ $(window).load(function(){ $('#modalChangePassWord').modal( { backdrop: 'static', keyboard: false }, 'show');});"
                . "$('[type = password]').val(''); "
                . "});</script>";
            echo $javascript;
        }
    }


    ?>
    <div class="box">
        <ol class="breadcrumb box-top">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <li class="active">Tài Khoản</li>
        </ol>
        <div id="profile">
            <!--INFOMATION CONTENT-->
            <?php if ($update) { ?>
                <div style="margin: 10px" class="alert alert-success alert-dismissible" id="boardLogin"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Cập nhập thông tin thành công !</strong>
                </div>
            <?php } ?>
            <div class="header">
                <h1>Thông tin cá nhân</h1>

                <p style="color: #EF3D3D">(*) Biểu thị thông tin bắt buộc</p>
            </div>

            <form id="frmProfile" name="frmProfile" class="form-horizontal form-black clearfix" method="post" action="profile.php?token=<?php echo $_GET["token"];?>>">
                <!--MAIN REGISTER-->
                <fieldset id="account-info">
                    <legend><i class="fa fa-info-circle"></i>THÔNG TIN TÀI KHOẢN</legend>
                    <input type="hidden" value="<?php echo Context::getCurrentUser()["userID"]; ?>" name="txtUserID">

                    <div class="form-group">
                        <label for="txtUserName" class="col-sm-3 control-label note">Tài Khoản :</label>

                        <div class="col-sm-7">
                            <label
                                class="control-label"><?php echo Context::getCurrentUser()["userName"]; ?></label>
                            <input type="hidden" readonly
                                   value="<?php echo Context::getCurrentUser()["userName"]; ?>"
                                   class="form-control" id="txtUserName"
                                   name="txtUserName">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label note ">Mật Khẩu :</label>

                        <div class="col-sm-7">
                            <label class="control-label">
                                <a id="link-changePassWord" data-toggle="modal" data-target="#modalChangePassWord">Thay
                                    đổi Mật Khẩu</a>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtEmail" class="col-sm-3 control-label note">Email :</label>

                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="txtEmail" name="txtEmail"
                                   value="<?php echo Context::getCurrentUser()["email"]; ?>">
                        </div>
                    </div>
                </fieldset>
                <fieldset id="profile-info">
                    <legend><i class="fa fa-info-circle"></i>THÔNG TIN CÁ NHÂN</legend>
                    <div class="form-group">
                        <label for="txtFullName" class="col-sm-3 control-label note">Họ và Tên:</label>

                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="txtFullName" name="txtFullName"
                                   value="<?php echo Context::getCurrentUser()["fullName"]; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtBirthDay" class="col-sm-3 control-label">Ngày Sinh :</label>

                        <div class="col-sm-7">
                            <input type="text" class="form-control datepicker date " id="txtBirthDay"
                                   name="txtBirthDay"
                                   value="<?php echo date_format(Context::getCurrentUser()["dateOfBirth"], 'd/m/Y') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cboSex" class="col-sm-3 control-label">Giới Tính :</label>

                        <div class="col-sm-7">
                            <?php $Gender = trim(Context::getCurrentUser()["gender"]); ?>
                            <select class="form-control" id="cboGender" name="cboGender">
                                <option value="Nam" <?php if ($Gender == "Nam") {
                                    echo "selected";
                                } ?>>Nam
                                </option>
                                <option value="Nữ" <?php if ($Gender == "Nữ") {
                                    echo "selected";
                                } ?>>Nữ
                                </option>
                                <option value="Khác" <?php if ($Gender == "Khác") {
                                    echo "selected";
                                } ?>>Khác
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtCaptcha" class="col-sm-3 control-label"></label>

                        <div class="col-sm-7">
                            <img src="lib/cool-php-captcha-0.3.1/captcha.php" id="captcha" style="cursor: pointer"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtCaptcha" class="col-sm-3 control-label note">Xác Nhận Captcha :</label>

                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="txtCaptcha" name="txtCaptcha">
                        </div>
                    </div>
                </fieldset>
                <!--MAIN REGISTER-->

                <div id="form-action">
                    <div class="col-sm-10 text-right">
                        <button class="btn btn-default " type="submit" name="btnUpdate" style="margin-right: 5px;">
                            Cập Nhập
                        </button>
                        <button class="btn btn-default " type="reset" name="btnCancel" id="btnCancel">Hủy</button>
                    </div>
                </div>
            </form>

            <!-- Modal Change PassWord -->
            <div class="modal fade" id="modalChangePassWord" tabindex="2" role="dialog"
                 aria-labelledby="myModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="frmChangePassWord" name="frmChangePassWord"
                              class="form-horizontal form-black clearfix"
                              method="post"
                              action="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>

                                <h4 class="modal-title">THAY ĐỔI MẬT KHẨU</h4>
                            </div>
                            <div class="modal-body">
                                <?php if (!$updatePW) { ?>
                                    <div class="col-sm-offset-4 alert alert-danger alert-dismissible"
                                         id="boardChangePassWord" role="alert">
                                        <label class="error">Đổi mật khẩu thất bại!</label>
                                    </div>
                                <?php } ?>
                                <input type="hidden" readonly
                                       value="<?php echo Context::getCurrentUser()["userName"]; ?>"
                                       class="form-control"
                                       id="txtUserName"
                                       name="txtUserName">

                                <div class="form-group">
                                    <label for="txtPassWordOld" class="col-sm-4 control-label note">Mật Khẩu Cũ
                                        :</label>

                                    <div class="col-sm-7">
                                        <input type="password" autocomplete="off" class="form-control"
                                               id="txtPassWordOld"
                                               name="txtPassWordOld">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtPassWordNew" class="col-sm-4 control-label note">Mật Khẩu Mới
                                        :</label>

                                    <div class="col-sm-7">
                                        <input type="password" class="form-control" id="txtPassWordNew"
                                               name="txtPassWordNew">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtConfirmPW" class="col-sm-4 control-label note">Xác Nhận Mật
                                        Khẩu:</label>

                                    <div class="col-sm-7">
                                        <input type="password" class="form-control" id="txtConfirmPW"
                                               name="txtConfirmPW">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" id="account-action">
                                <div class="col-sm-11 ">
                                    <input class="btn btn-default" type="submit" name="btnSave"
                                           value="Đổi Mật Khẩu">
                                    <button type="button" class="btn btn-default" id="btnCancelChangePW"
                                            data-dismiss="modal">Hủy
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <!--/ Modal Change PassWord-->
            <!--/INFORMATION CONTENT-->
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.validator.addMethod(
                "date",
                function (value, element) {
                    var check = false;
                    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                    if (re.test(value)) {
                        var adata = value.split('/');
                        var dd = parseInt(adata[0], 10);
                        var mm = parseInt(adata[1], 10);
                        var yyyy = parseInt(adata[2], 10);
                        var xdata = new Date(yyyy, mm - 1, dd);
                        var today = new Date();
                        if ((today.getFullYear() - 3 >= xdata.getFullYear() ) && ( xdata.getFullYear() == yyyy ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == dd ))
                            check = true;
                        else
                            check = false;
                    } else
                        check = false;
                    return this.optional(element) || check;
                },
                "Ngày không hợp lệ !"
            );
            var validatorChangeProfile = $("#frmProfile").validate({
                rules: {
                    txtEmail: {
                        required: true,
                        email: true
                    },
                    txtFullName: "required",
                    txtCaptcha: {
                        required: true,
                        remote: {
                            url: "check_Captcha.php",
                            type: "post",
                            data: {
                                captcha: function () {
                                    return $("#txtCaptcha").val();
                                }
                            }
                        }
                    },
                    txtBirthDay: {
                        required: true,
                        date: true
                    }
                },
                messages: {
                    txtEmail: {
                        required: "Vui lòng nhập Email !",
                        email: "Email không hợp lệ !"
                    },
                    txtFullName: "Vui lòng nhập họ và tên !",
                    txtCaptcha: {
                        required: "Vui lòng nhập captcha !",
                        remote: "Mã Captcha không đúng !"
                    },
                    txtBirthDay: {
                        required: "Vui lòng nhập ngày sinh !",
                        date: "Ngày sinh không hợp lệ !"
                    }
                }
            });

            //ChangePassWord
            var validatorChangePW = $("#frmChangePassWord").validate({
                rules: {
                    txtPassWordOld: {
                        required: true,
                        minlength: 5,
                        maxlength: 20
                    },
                    txtPassWordNew: {
                        required: true,
                        minlength: 5,
                        maxlength: 20
                    },
                    txtConfirmPW: {
                        required: true,
                        minlength: 5,
                        equalTo: "#txtPassWordNew"
                    }

                },
                messages: {
                    txtPassWordOld: {
                        required: "Vui lòng nhập mật khẩu cũ !",
                        minlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !",
                        maxlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !"
                    },
                    txtPassWordNew: {
                        required: "Vui lòng nhập mật khẩu mới !",
                        minlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !",
                        maxlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !"
                    },
                    txtConfirmPW: {
                        required: "Vui lòng nhập xác nhận mật khẩu !",
                        minlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !",
                        equalTo: "Mật khẩu xác nhận không trùng khớp !"
                    }

                }
            });
            //end kiem tra du lieu

            $('input.date').inputmask('dd/mm/yyyy');

            var resetForm = function () {
                $('[type = password]').val("");
                $('#boardChangePassWord').remove();
                validatorChangePW.resetForm();
                $('.error').each(function () {
                    $(this).removeClass('error');
                });
            };
            $('#link-changePassWord').click(function () {
                resetForm();
            });
            $('#btnCancel').on("click", function () {
                /* var labelError = $('label.error');
                 labelError.each(function () {
                 var e = $(this).prev();
                 $(e).removeClass('error');
                 $(this).remove();
                 });*/
                validatorChangeProfile.resetForm();

            });
            //change captcha
            $('#captcha').on('click', function () {

//                document.getElementById('captcha').src = 'lib/cool-php-captcha-0.3.1/captcha.php?' + Math.random();

                var src = 'lib/cool-php-captcha-0.3.1/captcha.php?' + Math.random();
                $('#captcha').attr('src', src);
            });
        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('Templates/Template.php');
}