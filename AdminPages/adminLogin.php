<?php
if(!isset($_SESSION))
{
    session_start();
}
if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}

require_once '../helper/crypter.php';
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Neon Admin Panel" />
    <meta name="author" content="" />

    <title>Neon | Login</title>


    <link rel="stylesheet" href="assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="assets/css/font-icons/entypo/css/entypo.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/neon-core.css">
    <link rel="stylesheet" href="assets/css/neon-theme.css">
    <link rel="stylesheet" href="assets/css/neon-forms.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script src="assets/js/jquery-1.11.3.min.js"></script>


</head>
<body class="page-body login-page login-form-fall" data-url="http://neon.dev">


<!-- This is needed when you send requests via Ajax --><script type="text/javascript">
   // var getUrl = window.location;
   // var baseurl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/" + getUrl.pathname.split('/')[2] + "/";
    var baseurl ='';
</script>

<div class="login-container">

    <div class="login-header login-caret">

        <div class="login-content">

            <a href="index.php" class="logo">
                <img src="../assets/images/logo.png" width="120" alt="" />
            </a>
            <!-- progress bar indicator -->
            <div class="login-progressbar-indicator">
                <h3>43%</h3>
                <span>logging in...</span>
            </div>
        </div>

    </div>

    <div class="login-progressbar">
        <div></div>
    </div>

    <div class="login-form">

        <div class="login-content">

            <div class="form-login-error">
                <h3>Tên đăng nhập hoặc mật khẩu không chính xác !</h3>
            </div>

            <form method="post" role="form" id="form_login">

                <div class="form-group">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-user"></i>
                        </div>

                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" autocomplete="off" />
                    </div>

                </div>

                <div class="form-group">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-key"></i>
                        </div>

                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" />
                    </div>

                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-login">
                        <i class="entypo-login"></i>
                        Login In
                    </button>
                </div>
            </form>
            <!--
            <div class="login-bottom-links">

                <a href="extra-forgot-password.html" class="link">Forgot your password?</a>

                <br />

            </div>
-->
        </div>

    </div>

</div>


<!-- Bottom Scripts -->
<script src="assets/js/gsap/main-gsap.js"></script>
<script src="assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/joinable.js"></script>
<script src="assets/js/resizeable.js"></script>
<script src="assets/js/neon-api.js"></script>
<script src="assets/js/jquery.validate.min.js"></script>
<script src="assets/js/neon-login.js"></script>
<script src="assets/js/neon-custom.js"></script>
<script src="assets/js/neon-demo.js"></script>
<script>

</script>

</body>
</html>