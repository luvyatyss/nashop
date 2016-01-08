<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author iRViNE
 */
class Utils {

    public static function Redirect($url) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
    }

}
