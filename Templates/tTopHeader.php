<?php
require_once './helper/Utils.php';
require_once './helper/Context.php';
require_once './entities/User.php';
require_once './entities/Cart.php';
require_once './helper/crypter.php';


//security
if (isset($_POST["txtProID"])) {
    if (Context::IsLogged()) {
        $proID = $_POST["txtProID"];
        $quantity = 1;
        if (isset($_POST["txtOrderQ"]) && !empty($_POST["txtOrderQ"])) {
            $quantity = $_POST["txtOrderQ"];
        }
        Cart::addItem($proID, $quantity);
    } else {
        $_SESSION['showModalLogin'] = true;
    }
    //Cart::printCart();
}
if (isset($_SESSION["IsLogin"]) && $_SESSION["IsLogin"] == 1) {
//set token
    $crypter = new Crypter("nhatanh");
    if (!isset($_SESSION["token"])) {
        $datetime = new DateTime();
        $str_datetime = date_format($datetime, 'Y-m-d H:i:s');
        $security = $str_datetime . " " . strval(rand());
        $encrypted = $crypter->Encrypt($security);
        $_SESSION["token"] = $security;
        $token = "token=" . $encrypted;
    } else {
        $encrypted = $crypter->Encrypt($_SESSION["token"]);
        $token = "token=" . $encrypted;
    }

}

$user = new User();
//Chuyen trang khac neu da dang nhap

/*if (Context::IsLogged()) {
    Utils::Redirect("index.php");
}*/
$boardLogin = true;
$mess ="";
if (isset($_POST["btnLogin"])) {
    $user->setUserName($_POST["login_txtUserName"]);
    $user->setUserPassWord($_POST["login_txtPassWord"]);
    $remember = isset($_POST['chkRemember']) ? true : false;
    $ret = $user->login();
// $ret: true => đăng nhập thành công, $user có đủ thông tin
// $ret: false => đăng nhập thất bại
    if ($ret) {
        $_SESSION["IsLogin"] = 1; // đã đăng nhập
        $_SESSION["CurrentUser"] = (array)$user;
        // ghi nho dang nhap
        if ($remember) {
            $expire = time() + 15 * 24 * 60 * 60;
            setcookie("UserName", $user->getUserName(), $expire);
        }

        $url = "index.php";
        Utils::Redirect($url);
    } else {
        $boardLogin = false;
        $mess = "Tài khoản hoặc mật khẩu không chính xác !";
        $javascript = "<script> $(function () "
            . "{ $(window).load(function(){ $('#modalLogin').modal( { backdrop: 'static', keyboard: false }, 'show');});"
            . " $('#login_txtUserName').val('" . $user->getUserName() . "'); $('#login_txtPassWord').val(''); "
            . "});</script>";
        echo $javascript;
    }
}
if (isset($_SESSION['showModalLogin']) && $_SESSION['showModalLogin'] == true) {
    $boardLogin = false;
    $mess = "Vui lòng đăng nhập !";
    $javascript = "<script> $(function () "
        . "{ $(window).load(function(){ $('#modalLogin').modal( { backdrop: 'static', keyboard: false }, 'show');});"
        . "});</script>";
    echo $javascript;
    unset($_SESSION['showModalLogin']);
}

?>


<div class="container">
    <div class="row">
        <!--Main Top Header-->
        <div class="pull-right" id="user-command">
            <?php if (!Context::IsLogged()) { ?>
                <ul class="nav navbar-nav" id="notLogged">
                    <li>
                        <a href="" id="link_login" data-toggle="modal" data-target="#modalLogin">
                            <i class="fa fa-sign-in"></i>Đăng nhập
                        </a>
                    </li>
                    <li><a href="register.php"><i class="fa fa-pencil"></i>Đăng ký</a></li>
                </ul>
            <?php } else { ?>
                <ul class="nav navbar-nav" id="Logged">
                    <li>
                        <a href="profile.php?<?php echo $token ?>"><i class="fa fa-user"></i>Thông tin tài khoản</a>
                    </li>
                    <li>
                        <a href="cart.php?<?php echo $token ?>">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="badge badge-warning"><?php echo Cart::count(); ?></span>Giỏ hàng
                        </a>
                    </li>
                    <li>
                        <a href="logout.php?<?php echo $token ?>"><i class="fa fa-sign-out"></i>Thoát</a>
                    </li>
                </ul>
            <?php } ?>
        </div>

        <!--/Main Top Header-->
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="frmLogin" name="frmLogin" method="POST" class="form-horizontal form-black clearfix" action="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>

                        <h4 class="modal-title">Đăng Nhập</h4>
                    </div>
                    <div class="modal-body">
                        <div id="account-info">
                            <?php if (!$boardLogin) { ?>
                                <div class="col-sm-offset-3 alert alert-danger alert-dismissible" id="boardLogin"
                                     role="alert">
                                    <label class="error"><?php echo $mess; ?></label>
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label for="login_txtUserName" class="col-sm-3 control-label">Tài Khoản :</label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="login_txtUserName"
                                           name="login_txtUserName">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="login_txtPassWord" class="col-sm-3 control-label">Mật Khẩu :</label>

                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="login_txtPassWord"
                                           name="login_txtPassWord">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-7">
                                    <div class=" checkbox " id="remember">
                                        <label>
                                            <input type="checkbox" name="chkRemember" id="chkRemember">
                                            Ghi Nhớ
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" id="account-action">
                        <div class="col-sm-10">
                            <input class="btn btn-default" type="submit" name="btnLogin" value="Đăng Nhập">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
        var validator = $("#frmLogin").validate({
            rules: {
                login_txtUserName: {
                    required: true,
                    minlength: 5,
                    regexp: /^[a-zA-Z0-9_\.]+$/
                },
                login_txtPassWord: {
                    required: true,
                    minlength: 5,
                    maxlength: 20
                }
            },
            messages: {
                login_txtUserName: {
                    required: "Vui lòng nhập tên đăng nhập!",
                    minlength: "Tên đăng nhập phải dài hơn 5 kí tự!",
                    regexp: "Vui lòng chỉ sử dụng chữ cái (a-z), số và dấu chấm !",
                },
                login_txtPassWord: {
                    required: "Vui lòng nhập mật khẩu!",
                    minlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự!",
                    maxlength: "Mật khẩu phải nằm từ 5 đến 20 kí tự!"
                }
            }
        });
        var resetForm = function () {
            $('#boardLogin').remove();
            validator.resetForm();
            $('.error').each(function () {
                $(this).removeClass('error');
            });
        };
        $('#link_login').click(function () {
            resetForm();
        });
    });
</script>

