<?php
if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";

/**
 * Created by PhpStorm.
 * User: luvyatyss
 * Date: 12/21/2015
 * Time: 11:04 PM
 */
class Status {
    var $iStatusID , $strStatusName , $strStatusColor;

    function __construct($iStatusID = -1, $strStatusName = "" , $strStatusColor="" ){
        $this->iStatusID = $iStatusID;
        $this->strStatusName = $strStatusName ;
        $this->strStatusColor = $strStatusColor;
    }

    public function getStatusID()
    {
        return $this->iStatusID;
    }

    public function setStatusID($iStatusID)
    {
        $this->iStatusID = $iStatusID;
    }
    public function getStatusName()
    {
        return $this->strStatusName;
    }
    public function setStatusName($strStatusName)
    {
        $this->strStatusName = $strStatusName;
    }


    public function getStatusColor()
    {
        return $this->strStatusColor;
    }

    public function setStatusColor($strStatusColor)
    {
        $this->strStatusColor = $strStatusColor;
    }


    public static function loadAll() {
        $ret = array();

        $sql = "select StatusID, StatusName , StatusColor from statuses";
        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $iStatusID = $row["StatusID"];
            $strStatusName = $row["StatusName"];
            $strStatusColor = $row["StatusColor"];
            $o = new Status($iStatusID, $strStatusName, $strStatusColor );
            array_push($ret, $o);
        }

        return $ret;
    }
    public function loadLimit( $rowsPerPage = 0 , $offset = 0 , $SortName="" , $SortType = "" ) {
        $iStatusID = $this->iStatusID == -1 ? "" : $this->iStatusID;
        $strStatusName = $this->strStatusName;
        $ret = array();
        $sql = "select StatusID, StatusName , StatusColor "
            . "from statuses "
            . "where StatusID like '%".$iStatusID."%' and StatusName like '%". $strStatusName ."%' ";
        if ($SortName == "") {
            $sql .= " order by StatusID " . $SortType;
        }
        else{
            $sql .= " order by ". $SortName . " ". $SortType;
        }

        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }

        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $iStatusID = $row["StatusID"];
            $strStatusName = $row["StatusName"];
            $strStatusColor = $row["StatusColor"];
            $o = new Status($iStatusID, $strStatusName, $strStatusColor );
            array_push($ret, $o);
        }
        return $ret;
    }
    public function countRecords( ){
        $iStatusID = $this->iStatusID == -1 ? "" : $this->iStatusID;
        $strStatusName = $this->strStatusName;
        $sql = "select COUNT(*) AS numrows "
            . "from statuses "
            . "where StatusID like '%".$iStatusID."%' and StatusName like '%". $strStatusName ."%' ";
        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }
    public static function getStatus( $iStatusID){
        $o = null;
        $sql = "select StatusID, StatusName, StatusColor "
            . "from statuses where  StatusID = " . $iStatusID ;
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            $iStatusID = $row["StatusID"];
            $strStatusName = $row["StatusName"];
            $strStatusColor = $row["StatusColor"];
            $o = new Status($iStatusID, $strStatusName , $strStatusColor );
        }
        return $o;
    }
    public function insert() {
        $strStatusName = $this->strStatusName;
        $strStatusColor = $this->strStatusColor;
        $sql = "INSERT INTO statuses (StatusName , StatusColor ) VALUES "
            . "('{$strStatusName}', '{$strStatusColor}' )";

        DataProvider::execNonQuery($sql);
    }
    public function update() {
        $iStatusID = $this->iStatusID;
        $strStatusName = $this->strStatusName;
        $strStatusColor = $this->strStatusColor;
        $sql = sprintf( "UPDATE statuses SET StatusName = '%s' , StatusColor = '%s' "
            . "WHERE StatusID = %d",$strStatusName  , $strStatusColor , $iStatusID);
        DataProvider::execNonQuery($sql);
        return TRUE;
    }
    public function delete() {
        $iStatusID = $this->iStatusID;
        $sql = "DELETE from statuses WHERE StatusID =" . $iStatusID;
        DataProvider::execNonQuery($sql);
    }
}