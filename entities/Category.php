<?php
if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";
include_once('Device.php');
include_once('Brand.php');
class Category
{
    var $iCatProID , $strCatName, $oDevice , $oBrand ,$iStatus ;

    function __construct($iCatProID = -1 , $strCatName = "", Brand $oBrand = null, Device $oDevice = null,  $iStatus = -1 ){
        $this->iCatProID = $iCatProID;
        $this->strCatName = $strCatName ;
        $this->oDevice = $oDevice == null ? new Device() : $oDevice;
        $this->oBrand = $oBrand == null ? new Brand() : $oBrand;
        $this->iStatus = $iStatus;
    }

    public function getCatID(){
        return $this->iCatProID;
    }
    public function getCatName(){
        return $this->strCatName;
    }
    public function getDevice(){
        return $this->oDevice;
    }
    public function getBrand(){
        return $this->oBrand;
    }
    public function getStatus(){
        return $this->iStatus;
    }

    //Methods SET
    public function setCatID($iCatProID){
        $this->iCatProID = $iCatProID;
    }
    public function setCatName($strCatName){
        $this->strCatName = $strCatName ;
    }
    public function setDevice($oDevice){
        $this->oDevice = $oDevice;
    }
    public function setStatus($iStatus){
        $this->iStatus = $iStatus ;
    }
    public function setBrand($oBrand){
        return $this->oBrand = $oBrand;
    }

    public static function loadAll() {
        $ret = array();
        $sql = "select CatProID, CatName, c.BraID as BraID, b.BraName as BraName,  c.DeviceID as DeviceID, d.DeviceName as DeviceName , c.Discontinued as Discontinued "
            . "from categories as c , devices as d , brands as b  "
            . "where d.DeviceID = c.DeviceID and b.BraID = c.BraID ";
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];

            $iBraID = $row["BraID"] ;
            $strBraName = $row["BraName"] ;
            $oBrand = new Brand($iBraID, $strBraName);

            $iDeviceID = $row["DeviceID"] ;
            $strDeviceName = $row["DeviceName"] ;
            $oDevice = new Device($iDeviceID, $strDeviceName);

            $status = $row["Discontinued"];
            $o = new Category($iCatProID, $strCatName , $oBrand, $oDevice , $status);
            array_push($ret, $o);
        }
        return $ret;
    }
    public function loadLimit( $rowsPerPage = 0 , $offset = 0 , $SortName="" , $SortType = "" ) {
        $iCatProID = $this->iCatProID == -1 ? "" : $this->iCatProID;
        $strCatName = $this->strCatName;
        $iDeviceID =  $this->oDevice->getDeviceID();
        $iBraID =  $this->oBrand->getBraID();
        $iStatus =  $this->iStatus ;

        $ret = array();
        $sql = "select CatProID, CatName, c.BraID as BraID, b.BraName as BraName,  c.DeviceID as DeviceID, d.DeviceName as DeviceName , c.Discontinued as Discontinued "
            . "from categories as c , devices as d , brands as b  "
            . "where d.DeviceID = c.DeviceID and b.BraID = c.BraID and CatProID like '%".$iCatProID."%' and CatName like '%". $strCatName ."%' ";
        if ($iDeviceID != -1){
            $sql .= " and c.DeviceID =". $iDeviceID ;
        }
        if ($iBraID != -1){
            $sql .= " and c.BraID =". $iBraID ;
        }
        if ($iStatus != -1){
            $sql .= " and c.Discontinued =". $iStatus ;
        }
        if ($SortName == "") {
            $sql .= " order by CatName " . $SortType;
        }
        else {
            $sql .= " order by ". $SortName . " " . $SortType;
        }
        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }
       // echo $sql;
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];

            $iBraID = $row["BraID"] ;
            $strBraName = $row["BraName"] ;
            $oBrand = new Brand($iBraID, $strBraName);

            $iDeviceID = $row["DeviceID"] ;
            $strDeviceName = $row["DeviceName"] ;
            $oDevice = new Device($iDeviceID, $strDeviceName);

            $status = $row["Discontinued"];
            $o = new Category($iCatProID, $strCatName, $oBrand , $oDevice , $status);
            array_push($ret, $o);
        }
        return $ret;
    }
    public function countRecords( ){
        $iCatProID = $this->iCatProID == -1 ? "" : $this->iCatProID;
        $strCatName = $this->strCatName;
        $iDeviceID =  $this->oDevice->getDeviceID();
        $iBraID =  $this->oBrand->getBraID();
        $iStatus =  $this->iStatus ;
        $sql = "select COUNT(*) AS numrows "
            . "from categories "
            . "where CatProID like '%".$iCatProID."%' and CatName like '%". $strCatName ."%' ";
        if ($iDeviceID != -1){
            $sql .= " and DeviceID =". $iDeviceID ;
        }
        if ($iBraID != -1){
            $sql .= " and BraID =". $iBraID ;
        }
        if ($iStatus != -1){
            $sql .= " and Discontinued =". $iStatus ;
        }

        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }

    public static function getCat( $iCatProID){
        $o = null;
        $sql = "select CatProID, CatName, c.DeviceID as DeviceID, d.DeviceName as DeviceName , c.Discontinued as Discontinued , c.BraID as BraID , b.BraName as BraName  "
            . "from categories as c , devices as d , brands as b where b.BraID = c.BraID and d.DeviceID = c.DeviceID and CatProID = " . $iCatProID ;
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $iBraID = $row["BraID"] ;
            $strBraName = $row["BraName"] ;
            $oBrand = new Brand($iBraID, $strBraName);
            $iDeviceID = $row["DeviceID"] ;
            $strDeviceName = $row["DeviceName"] ;
            $oDevice = new Device($iDeviceID, $strDeviceName);
            $status = $row["Discontinued"];
            $o = new Category($iCatProID, $strCatName  , $oBrand, $oDevice , $status);

        }
        return $o;
    }
    public function insert() {
        $CatName = $this->strCatName;
        $DeviceID = $this->oDevice->getDeviceID();
        $BraID = $this->oBrand->getBraID();
        $sql = "INSERT INTO categories (CatName, BraID ,DeviceID ,Discontinued) VALUES "
                . "('". $CatName ."' , " . $BraID ." , ". $DeviceID ."  , 0)";

        DataProvider::execNonQuery($sql);
    }
    public function update() {
        $CatProID = $this->iCatProID;
        $CatName = $this->strCatName;
        $DeviceID = $this->oDevice->getDeviceID();
        $BraID = $this->oBrand->getBraID();
        $Status = $this->iStatus;
        $sql = sprintf( "UPDATE categories SET CatName = '%s' ,Discontinued = %d, BraID = %d ,DeviceID = %d "
                . "WHERE CatProID = %d",$CatName , $Status , $BraID , $DeviceID, $CatProID);
        DataProvider::execNonQuery($sql);
    }
    public function delete() {
        $CatProID = $this->iCatProID;
        $sql = "DELETE FROM categories WHERE CatProID = " . $CatProID;
        DataProvider::execNonQuery($sql);
    }
}