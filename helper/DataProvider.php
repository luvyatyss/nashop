<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataProvider
 *
 * @author luvyatyss
 */
/*namespace Helper;*/
define("SERVER", "localhost");
define("DB", "nashop");
define("USERID", "root");
define("PASSWORD", "");

class DataProvider {
    public static function Load($sql){
        //Ket noi CSDL 
        $cn = new mysqli(SERVER, USERID, PASSWORD, DB);
        if ($cn->connect_errno) {
            die("Failed to connect to MySQL: (" . $cn->connect_errno . ") " . $cn->connect_error);
        }

        $cn->query("set names 'utf8'");
        $resultSet = $cn->query($sql);
        $cn->close();
        return $resultSet;
    }
    public static function execNonQuery($sql){
        //Ket noi CSDL 
         $cn = new mysqli(SERVER, USERID, PASSWORD, DB);
        if ($cn->connect_errno) {
            die("Failed to connect to MySQL: (" . $cn->connect_errno . ") " . $cn->connect_error);
        }

        $cn->query("set names 'utf8'");
        
        //Thuc thi truy van
        if (!$cn->query($sql)){
            die("Lỗi truy vấn: ". $cn->error);
        }
        //Dong Ket noi
        $cn->close();
    }
    public static function execNonQueryIdentity($sql){
        //Ket noi CSDL 
        $cn = new mysqli(SERVER, USERID, PASSWORD, DB);
         if ($cn->connect_errno) {
            die("Failed to connect to MySQL: (" . $cn->connect_errno . ") " . $cn->connect_error);
        }
        $cn->query("set names 'utf8'");
        //Thuc thi truy van
        if (!$cn->query($sql)){
            die("Lỗi truy vấn: ". $cn->error);
        }
  
        $id = $cn->insert_id;
        //Dong Ket noi
       
        $cn->close();
        return $id;
    }
}
