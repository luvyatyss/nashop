<?php
/*
	Sample Processing of Forgot password form via ajax
	Page: extra-register.html
*/

# Response Data Array
if (!isset($_SESSION)) {
    session_start();
}
require_once "../entities/User.php";
require_once '../helper/Context.php';
$user = new User();
$resp = array();

// Fields Submitted
$user->setUserName($_POST["username"]);
$user->setUserPassWord($_POST["password"]);


// This array of data is returned for demo purpose, see assets/js/neon-forgotpassword.js
$resp['submitted_data'] = $_POST;


// Login success or invalid login data [success|invalid]
// Your code will decide if username and password are correct
$login_status = 'invalid';

$ret = $user->login();
// $ret: true => đăng nhập thành công, $user có đủ thông tin
// $ret: false => đăng nhập thất bại
if ($ret && $user->getUserPermission() == 1) {
    $login_status = 'success';
    $_SESSION["IsLogin"] = 1; // đã đăng nhập
    $_SESSION["CurrentUser"] = (array)$user;
}

$resp['login_status'] = $login_status;


// Login Success URL
if($login_status == 'success')
{
    $resp['redirect_url'] = 'index.php';
}


echo json_encode($resp);