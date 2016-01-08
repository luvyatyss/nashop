<?php
require_once "entities/User.php";
if (isset($_POST["username"]) && !empty($_POST["username"])){
    if (User::isExistsUserName($_POST["username"]) == 1){
        echo "false";
    } else {
        echo "true";
    }
}