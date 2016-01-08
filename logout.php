<?php

session_start();

if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}
require_once 'entities/User.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './helper/Context.php';
require_once './helper/Utils.php';

date_default_timezone_set('Asia/Bangkok');
if (Context::IsLogged()) {
    //Cập nhập lại đăng nhập lần cuối
    $User = new User();
    $User->setUserID( Context::getCurrentUser()["userID"]);
    $lastLogon = new DateTime();
    $User->setLastLogon($lastLogon);
    $User->updateLastLogon();

    Context::destroy();
    unset($_SESSION["token"]);
} else {
    
}
Utils::Redirect("index.php");
