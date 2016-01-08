<?php

if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";
class Brand
{
    var $iBraID , $strBraName , $strLogoURL ,$iStatus ;

    function __construct($iBraID = -1 , $strBraName = "" , $strLogoURL = "", $iStatus = -1 ){
        $this->iBraID = $iBraID;
        $this->strBraName = $strBraName ;
        $this->strLogoURL = $strLogoURL;
        $this->iStatus = $iStatus;
    }

    public function getBraID(){
        return $this->iBraID;
    }
    public function getBraName(){
        return $this->strBraName;
    }
    public function getLogoURL(){
        return $this->strLogoURL;
    }
    public function getStatus(){
        return $this->iStatus;
    }

    //Methods SET
    public function setBraID($iBraID){
        $this->iBraID = $iBraID;
    }
    public function setBraName($strBraName){
        $this->strBraName = $strBraName ;
    }
    public function setLogoURL($strLogoURL){
        $this->strLogoURL = $strLogoURL ;
    }
    public function setStatus($iStatus){
        $this->iStatus = $iStatus ;
    }

    public static function loadAll() {
        $ret = array();
        $sql = "select BraID, BraName, LogoURL  , Discontinued  "
            . "from brands order by BraName";
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iBraID = $row["BraID"];
            $strBraName = $row["BraName"];
            $strLogoURL = $row["LogoURL"];
            $status = $row["Discontinued"];
            $o = new Brand($iBraID, $strBraName,$strLogoURL, $status);
            array_push($ret, $o);
        }
        return $ret;
    }
    public function loadLimit( $rowsPerPage = 0 , $offset = 0 , $SortName="" , $SortType = "" ) {
        $iBraID = $this->iBraID == -1 ? "" : $this->iBraID;
        $strBraName = $this->strBraName;
    //    $strLogoURL =  $this->strLogoURL;
        $iStatus =  $this->iStatus ;
        $ret = array();
        $sql = "select BraID, BraName, LogoURL  , Discontinued "
            . "from brands "
            . "where BraID like '%".$iBraID."%' and BraName like '%". $strBraName ."%' ";
        if ($iStatus != -1){
            $sql .= " and Discontinued =". $iStatus ;
        }
        if ($SortName == "") {
            $sql .= " order by BraID " . $SortType;
        }
        else{
            $sql .= " order by ". $SortName . " ". $SortType;
        }
        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iBraID = $row["BraID"];
            $strBraName = $row["BraName"];
            $strLogoURL = $row["LogoURL"];
            $status = $row["Discontinued"];
            $o = new Brand($iBraID, $strBraName,$strLogoURL, $status);
            array_push($ret, $o);
        }
        return $ret;
    }
    public function countRecords( ){
        $iBraID = $this->iBraID == -1 ? "" : $this->iBraID;
        $strBraName = $this->strBraName;
        //    $strLogoURL =  $this->strLogoURL;
        $iStatus =  $this->iStatus ;
        $sql = "select COUNT(*) AS numrows "
            . "from brands "
            . "where BraID like '%".$iBraID."%' and BraName like '%". $strBraName ."%' ";
        if ($iStatus != -1){
            $sql .= " and Discontinued =". $iStatus ;
        }

        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }
    public static function getBrand( $iBraID){
        $sql = "select BraID, BraName, LogoURL  , Discontinued "
            . "from brands where  BraID = " . $iBraID ;
        $resultSet = DataProvider::Load($sql);
        $o = null;
        if ($row = $resultSet->fetch_assoc()) {
            $iBraID = $row["BraID"];
            $strBraName = $row["BraName"];
            $strLogoURL = $row["LogoURL"];
            $status = $row["Discontinued"];
            $o = new Brand($iBraID, $strBraName,$strLogoURL, $status);
        }
        return $o;
    }
    public function insert() {
        $strBraName = $this->strBraName;
        $sql = "INSERT INTO brands (BraName ,Discontinued) VALUES "
            . "('{$strBraName}'  , 0)";
        $id = DataProvider::execNonQueryIdentity($sql);
        if ($id != null){
            $this->iBraID = $id;
        }
        return $id;
    }
    public function update() {
        $BraID = $this->iBraID;
        $strBraName = $this->strBraName;
        $Status = $this->iStatus;
        $sql = sprintf( "UPDATE brands SET BraName = '%s' ,Discontinued = %d "
            . "WHERE BraID = %d",$strBraName , $Status , $BraID);
        DataProvider::execNonQuery($sql);
        return TRUE;
    }
    public function updateLogo() {
        $BraID = $this->iBraID;
        $strLogoURL =  $this->strLogoURL;
        $sql = sprintf( "UPDATE brands SET  LogoURL = '%s' "
            . "WHERE BraID = %d", $strLogoURL, $BraID);
        DataProvider::execNonQuery($sql);
        return TRUE;
    }
    public function delete() {
        $BraID = $this->iBraID;
        $sql = "DELETE from brands WHERE BraID =" . $BraID;
        DataProvider::execNonQuery($sql);

    }
}