<?php

if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";
class Device {
    var $iDeviceID , $strDeviceName, $iStatus;

    function __construct($iDeviceID = -1, $strDeviceName = "" , $iStatus = -1){
        $this->iDeviceID = $iDeviceID;
        $this->strDeviceName = $strDeviceName ;
        $this->iStatus = $iStatus;
    }

    public function getDeviceID(){
        return $this->iDeviceID;
    }
    public function getDeviceName(){
        return $this->strDeviceName;
    }
    public function getStatus(){
        return $this->iStatus;
    }

    //Methods SET
    public function setDeviceID($iDeviceID){
        $this->iDeviceID = $iDeviceID;
    }
    public function setDeviceName($strDeviceName){
        $this->strDeviceName = $strDeviceName ;
    }
    public function setStatus($iStatus){
        $this->iStatus = $iStatus ;
    }

    public static function loadAll() {
        $ret = array();

        $sql = "select DeviceID, DeviceName, Discontinued from devices";
        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $id = $row["DeviceID"];
            $name = $row["DeviceName"];
            $status = $row["Discontinued"];
            $d = new Device($id, $name , $status);
            array_push($ret, $d);
        }
        
        return $ret;
    }
    public function loadLimit( $rowsPerPage = 0 , $offset = 0 , $SortName="" , $SortType = "") {
        $iDeviceID = $this->iDeviceID == -1 ? "" : $this->iDeviceID;
        $strDeviceName = $this->strDeviceName;
        $iStatus =  $this->iStatus ;
        $ret = array();
        $sql = "select DeviceID, DeviceName  , Discontinued "
            . "from devices "
            . "where DeviceID like '%".$iDeviceID."%' and DeviceName like '%". $strDeviceName ."%' ";
        if ($iStatus != -1){
            $sql .= " and Discontinued =". $iStatus ;
        }
        if ($SortName == "") {
            $sql .= " order by DeviceName " . $SortType;
        }
        else{
            $sql .= " order by ". $SortName . " ". $SortType;
        }

        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }

        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $iDeviceID = $row["DeviceID"];
            $strDeviceName = $row["DeviceName"];
            $status = $row["Discontinued"];
            $o = new Device($iDeviceID, $strDeviceName, $status);
            array_push($ret, $o);
        }
        return $ret;
    }
    public function countRecords( ){
        $iDeviceID = $this->iDeviceID == -1 ? "" : $this->iDeviceID;
        $strDeviceName = $this->strDeviceName;
        $iStatus =  $this->iStatus ;
        $sql = "select COUNT(*) AS numrows "
            . "from devices "
            . "where DeviceID like '%".$iDeviceID."%' and DeviceName like '%". $strDeviceName ."%' ";
        if ($iStatus != -1){
            $sql .= " and Discontinued =". $iStatus ;
        }

        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }
    public static function getDevice( $iDeviceID){
        $o = null;
        $sql = "select DeviceID, DeviceName  , Discontinued "
            . "from devices where  DeviceID = " . $iDeviceID ;
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            $iDeviceID = $row["DeviceID"];
            $strDeviceName = $row["DeviceName"];
            $status = $row["Discontinued"];
            $o = new Device($iDeviceID, $strDeviceName, $status);
        }
        return $o;
    }
    public function insert() {
        $strDeviceName = $this->strDeviceName;
        $sql = "INSERT INTO devices (DeviceName ,Discontinued) VALUES "
            . "('". $strDeviceName ."'  , 0)";

        DataProvider::execNonQuery($sql);
    }
    public function update() {
        $iDeviceID = $this->iDeviceID;
        $strDeviceName = $this->strDeviceName;
        $Status = $this->iStatus;
        $sql = sprintf( "UPDATE devices SET DeviceName = '%s' ,Discontinued = %d "
            . "WHERE DeviceID = %d",$strDeviceName , $Status , $iDeviceID);
        DataProvider::execNonQuery($sql);

    }
    public function delete() {
        $iDeviceID = $this->iDeviceID;
        $sql = "DELETE from devices WHERE DeviceID =" . $iDeviceID;
        DataProvider::execNonQuery($sql);
    }
}
