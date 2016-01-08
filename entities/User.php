<?php
if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";

class User
{
    var $userID, $userName, $userPassWord, $email, $fullName, $gender, $phoneNo, $dateOfBirth,
        $userCreated, $userLastModified, $lastLogon, $userPermission, $discontinued;

    function __construct($userID = -1, $userName = "", $userPassWord = "", $email = "", $fullName = "", $gender = "", DateTime $dateOfBirth = null, DateTime $userCreated = null, DateTime $userLastModified = null, DateTime $lastLogon = null, $userPermission = -1)
    {
        $this->userID = $userID;
        $this->userName = $userName;
        $this->userPassWord = $userPassWord;
        $this->fullName = $gender;
        $this->gender = $fullName;
        $this->email = $email;
        $this->dateOfBirth = $dateOfBirth == null ? new DateTime() : $dateOfBirth;
        $this->userCreated = $userCreated == null ? new DateTime() : $userCreated;
        $this->userLastModified = $userLastModified == null ? new DateTime() : $userLastModified;
        $this->lastLogon = $lastLogon == null ? new DateTime() : $lastLogon;
        $this->userPermission = $userPermission;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getUserID()
    {
        return $this->userID;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getUserPassWord()
    {
        return $this->userPassWord;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function getUserCreated()
    {
        return $this->userCreated;
    }

    public function getUserLastModified()
    {
        return $this->userLastModified;
    }

    public function getLastLogon()
    {
        return $this->lastLogon;
    }

    public function getUserPermission()
    {
        return $this->userPermission;
    }

    public function getDiscontinued()
    {
        return $this->discontinued;
    }

    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function setUserPassWord($userPassWord)
    {
        $this->userPassWord = $userPassWord;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function setUserCreated($userCreated)
    {
        $this->userCreated = $userCreated;
    }

    public function setUserLastModified($userLastModified)
    {
        $this->userLastModified = $userLastModified;
    }

    public function setLastLogon($lastLogon)
    {
        $this->lastLogon = $lastLogon;
    }

    public function setUserPermission($userPermission)
    {
        $this->userPermission = $userPermission;
    }

    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function insert()
    {
        $str_userName = str_replace("'", "''", trim(strtolower($this->userName)));
        $str_fullName = str_replace("'", "''", $this->fullName);
        $str_email = str_replace("'", "''", $this->email);
        $enc_passWord = md5($this->userPassWord);
        $str_dob = date_format( $this->dateOfBirth, 'Y-m-d H:i:s');
        $str_gender = str_replace("'", "''", $this->gender);
        $str_created = date_format($this->userCreated, 'Y-m-d H:i:s');

        $sql = "insert into users (UserName, UserPassWord, FullName, Email, DateOfBirth, Gender, UserCreated ,UserPermission) "
            . "values('$str_userName', '$enc_passWord', '$str_fullName', '$str_email', '$str_dob','$str_gender', '$str_created' , $this->userPermission)";
        DataProvider::execNonQuery($sql);
    }

    public function update()
    {
        $id = $this->userID;
        $str_fullName = str_replace("'", "''", $this->fullName);
        $str_email = str_replace("'", "''", $this->email);

        $str_dob = date_format( $this->dateOfBirth, 'Y-m-d H:i:s');
        $str_gender = str_replace("'", "''", $this->gender);
        $str_lastModified = date_format($this->userLastModified, 'Y-m-d H:i:s');

        $sql = "update users set FullName = '{$str_fullName}' , Email = '{$str_email}' , DateOfBirth = '{$str_dob}' , Gender = '{$str_gender}'  , UserLastModified ='{$str_lastModified}'  "
            . "where UserID = ".$id ;
        DataProvider::execNonQuery($sql);
    }

    public function updatePassWord()
    {
        $id = $this->userID;
        $enc_passWord = md5($this->userPassWord);

        $sql = "update users set UserPassWord = '$enc_passWord'  "
            . "where UserID = ".$id ;
        DataProvider::execNonQuery($sql);
        return true;
    }
    public function updateLastLogon()
    {
        $id = $this->userID;
        $str_LastLogon = date_format($this->lastLogon, 'Y-m-d H:i:s');

        $sql = "update users set LastLogon = '$str_LastLogon'  "
            . "where UserID = ".$id ;
        DataProvider::execNonQuery($sql);
        return true;
    }

    public function login()
    {
        $ret = false;

        $userName = $this->userName;
        $enc_pwd = md5($this->userPassWord);
        $sql = "select UserID, FullName, Email, DateOfBirth, Gender, UserPermission  from users where UserName= '{$userName}' and UserPassWord='{$enc_pwd}'";
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {

            $this->userID = $row["UserID"];
            $this->fullName = $row["FullName"];
            $this->email = $row["Email"];
            $this->dateOfBirth = new DateTime($row["DateOfBirth"]);
            $this->gender = $row["Gender"];
            $this->userPermission = $row["UserPermission"];

            $ret = true;
        }
        return $ret;
    }

    public static function isExistsUserName($userName)
    {
        $sql = "select *  from users where UserName = '{$userName}'";
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            return true;
        }
        return false;
    }

    public static function FromUserName($userName)
    {

        $o = NULL;

        $sql = "select UserID , UserName, UserPassWord, FullName, Email, DateOfBirth, Gender, UserCreated, UserLastModified, LastLogon  ,UserPermission   from users where UserName = '{$userName}'";
        $resultSet = DataProvider::Load($sql);

        if ($row = $resultSet->fetch_assoc()) {
            $UserID = $row["UserID"];
            $UserName = $row["UserName"];
            $Password = $row["UserPassWord"];
            $FullName = $row["FullName"];
            $Email = $row["Email"];
            $DateOfBirth = new DateTime($row["DateOfBirth"]);
            $Gender = $row["Gender"];
            $UserCreated = new DateTime($row["UserCreated"]);
            $UserLastModified = new DateTime($row["UserLastModified"]);
            $LastLogon = new DateTime($row["LastLogon"]);
            $Permission = $row["UserPermission"];

            $o = new User($UserID, $UserName, $Password, $Email, $FullName, $Gender, $DateOfBirth, $UserCreated , $UserLastModified,$LastLogon , $Permission);
        }

        return $o;
    }
}