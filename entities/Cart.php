<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cart
 *
 * @author iRViNE
 */
class Cart {

    public static function printCart() {
        print_r($_SESSION["Cart"]);
    }

    public static function count() {
        $ret = 0;
        foreach ($_SESSION["Cart"] as $proId => $quantity) {
            $ret += $quantity;
        }

        return $ret;
    }

    public static function addItem($proId, $quantity) {
        if (array_key_exists($proId, $_SESSION["Cart"])) {
            $_SESSION["Cart"][$proId] += $quantity;
        } else {
            $_SESSION["Cart"][$proId] = $quantity;
        }
    }

    public static function removeItem($delProId) {

        foreach ($_SESSION["Cart"] as $proId => $quantity) {
            if ($proId == $delProId) {
                unset($_SESSION["Cart"][$delProId]);
                return;
            }
        }
    }

    public static function updateItem($updProId, $updQuantity) {

        foreach ($_SESSION["Cart"] as $proId => $quantity) {
            if ($proId == $updProId) {
                $_SESSION["Cart"][$updProId] = $updQuantity;
                return;
            }
        }
    }

    public static function destroyCart() {
        unset($_SESSION["Cart"]);
        $_SESSION["Cart"] = array();
    }

}
