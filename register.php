<?php
date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION)) {
    session_start();
}
require_once './helper/Page.php';
require_once './entities/User.php';
$page = new Page();
$page->setTitle('Trang Chủ');

$page->addCSS("assets/js/sweetalert/sweetalert.css");
$page->addCSS("assets/css/register.css");
$page->addCSS("assets/css/bootstrap-datepicker.min.css");

//$page->addJavascript("assets/js/bootstrap-datepicker.min.js");
$page->addJavascript("assets/js/jquery.inputmask.bundle.min.js");
$page->addJavascript("assets/js/sweetalert/sweetalert.min.js");

$page->startBody();
$User = new User();
$insert = null;
if (isset($_SESSION["IsLogin"]) && $_SESSION["IsLogin"]){
    require_once './helper/Utils.php';
    $url = "index.php";
    Utils::Redirect($url);
}

if (isset($_POST["btnRegister"])) {
    $User->setUserName($_POST["txtUserName"]);
    $User->setUserPassWord($_POST["txtPassWord"]);
    $User->setEmail($_POST["txtEmail"]);
    $dateOfBirth = new DateTime(str_replace('/', '-', $_POST["txtBirthDay"])); //d-m-Y
    $User->setDateOfBirth($dateOfBirth);
    $User->setFullName($_POST["txtFullName"]);
    $User->setGender($_POST["cboGender"]);
    $User->setUserPermission(0);
    $userCreated = new DateTime();
    $User->setUserCreated($userCreated);

    $User->insert();
    $_SESSION["IsLogin"] = 1; // đã đăng nhập
    $_SESSION["CurrentUser"] = (array)$User;
    $insert = true;
    unset($_SESSION['captcha']);
}

?>

    <div class="box">
        <ol class="breadcrumb box-top">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <li><a href="profile.php">Tài Khoản</a></li>
            <li class="active">Đăng ký</li>
        </ol>
        <div id="register">
            <!--REGISTER CONTENT-->
            <div class="header">
                <h1>Đăng ký tài khoản</h1>

                <p style="color: #EF3D3D">(*) Biểu thị thông tin bắt buộc</p>
            </div>

            <form id="frmRegister" method="post" action="register.php" name="frmRegister" class="form-horizontal form-black clearfix" >
                <!--MAIN REGISTER-->
                <fieldset id="account-info">
                    <legend><i class="fa fa-info-circle"></i>THÔNG TIN TÀI KHOẢN</legend>
                    <div class="form-group">
                        <label for="txtUserName" class="col-sm-3 control-label note">Tài Khoản :</label>

                        <div class="col-sm-7">
                            <input type="text" autocomplete="off" class="form-control" id="txtUserName"
                                   name="txtUserName" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtPassWord" class="col-sm-3 control-label note">Mật Khẩu :</label>

                        <div class="col-sm-7">
                            <input type="password" autocomplete="off" class="form-control" id="txtPassWord"
                                   name="txtPassWord" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtComfirmPW" class="col-sm-3 control-label note">Xác Nhận Mật Khẩu :</label>

                        <div class="col-sm-7">
                            <input type="password" class="form-control" id="txtConfirmPW" name="txtConfirmPW" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtEmail" class="col-sm-3 control-label note">Email :</label>

                        <div class="col-sm-7">
                            <input type="email" class="form-control" id="txtEmail" name="txtEmail" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="profile-info">
                    <legend><i class="fa fa-info-circle"></i>THÔNG TIN CÁ NHÂN</legend>
                    <div class="form-group">
                        <label for="txtFullName" class="col-sm-3 control-label note">Họ và Tên:</label>

                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="txtFullName" name="txtFullName" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtBirthDay" class="col-sm-3 control-label">Ngày Sinh :</label>

                        <div class="col-sm-7">
                            <input type="text" class="form-control  date "
                                   id="txtBirthDay" name="txtBirthDay">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cboSex" class="col-sm-3 control-label">Giới Tính :</label>

                        <div class="col-sm-7">
                            <select class="form-control" id="cboGender" name="cboGender">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                    </div>
                    <!--Captcha-->
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label"></label>

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
                    <!--/Captcha-->
                </fieldset>
                <!--MAIN REGISTER-->

                <div id="form-action">
                    <div class="col-sm-10 ">
                        <button class="btn btn-default pull-right" type="submit" id="btnRegister" name="btnRegister">Đăng Ký</button>
                    </div>
                </div>
            </form>
            <!--/REGISTER CONTENT-->
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $.validator.addMethod(
                "regexp",
                function (value, element, regexp) {
                    var re = new RegExp(regexp);
                    return this.optional(element) || re.test(value);
                },
                "Please check your input."
            );
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
                "Vui lòng nhập ngày có dạng dd/mm/yyyy"
            );
            var validator = $("#frmRegister").validate({
                rules: {
                    txtUserName: {
                        required: true,
                        minlength: 5,
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        remote: {
                            url: "check_UserName.php",
                            type: "post",
                            data: {
                                username: function () {
                                    return $("#txtUserName").val();
                                }
                            }
                        }
                    },
                    txtPassWord: {
                        required: true,
                        minlength: 5,
                        maxlength: 20
                    },
                    txtConfirmPW: {
                        required: true,
                        minlength: 5,
                        equalTo: "#txtPassWord"
                    },
                    txtEmail: {
                        required: true,
                        email: true
                    },
                    txtBirthDay: {
                        required: true,
                        date: true
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
                    chkAgree: "required"
                },
                messages: {
                    txtUserName: {
                        required: "Vui lòng nhập tên đăng nhập !",
                        minlength: "Tên đăng nhập phải dài hơn 5 kí tự !",
                        regexp: "Vui lòng chỉ sử dụng chữ cái (a-z), số và dấu chấm !",
                        remote: "Tài khoản đã có người sử dụng !"
                    },
                    txtPassWord: {
                        required: "Vui lòng nhập mật khẩu !",
                        minlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !",
                        maxlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !"
                    },
                    txtConfirmPW: {
                        required: "Vui lòng nhập xác nhận mật khẩu !",
                        minlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự !",
                        equalTo: "Mật khẩu xác nhận không trùng khớp !"
                    },
                    txtEmail: {
                        required: "Vui lòng nhập Email !",
                        email: "Email không hợp lệ !"
                    },
                    txtBirthDay: {
                        required: "Vui lòng nhập ngày sinh !",
                        date: "Ngày sinh không hợp lệ"
                    },
                    txtFullName: "Vui lòng nhập họ và tên !",
                    txtCaptcha: {
                        required: "Vui lòng nhập captcha !",
                        remote: "Mã Captcha không đúng !"
                    },
                    chkAgree: "Để sử dụng dịch vụ của chúng tôi, bạn phải đồng ý với điều khoản và dịch vụ !"
                }
            });

            $('input.date').inputmask('dd/mm/yyyy');
            //change captcha
            $('#captcha').on('click', function () {

//                document.getElementById('captcha').src = 'lib/cool-php-captcha-0.3.1/captcha.php?' + Math.random();

                var src = 'lib/cool-php-captcha-0.3.1/captcha.php?' + Math.random();
                $('#captcha').attr('src', src);
            });

            $(window).load(function () {
                $("#txtUserName").val("");
                $("#txtPassWord").val("");
            });
            //alert
            var insert = "<?php echo $insert == null ? "" : $insert ; ?>";
            if (insert) {
                swal(
                    {
                        title: "Đăng kí thành công!",
                        text: "Vui lòng chờ trong 2s !",
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    },
                    function () {
                        var url = "index.php";
                        window.location.href = url;
                    }
                );
            }
        });
    </script>

<?php
$page->endBody();
echo $page->render('Templates/Template.php');
