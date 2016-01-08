<?php
if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";
include_once "Status.php";
include_once  "User.php";

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Order
 *
 * @author luvyatyss
 */
class Order {
    var $iOrderID , $dOrderDate, $fTotal, $oUser , $oStatus;

    function __construct($iOrderID = -1, $fTotal = 0.0 , DateTime $dOrderDate = null  , User $oUser = null, Status $oStatus = null){
        $this->iOrderID = $iOrderID;
        $this->dOrderDate = $dOrderDate == null ? new DateTime() : $dOrderDate;
        $this->oUser = $oUser == null ? new User() : $oUser;
        $this->oStatus =  $oStatus == null ? new Status() : $oStatus;
        $this->fTotal = $fTotal ;
    }

    public function getOrderID()
    {
        return $this->iOrderID;
    }

    public function setOrderID($iOrderID)
    {
        $this->iOrderID = $iOrderID;
    }

    public function getOrderDate()
    {
        return $this->dOrderDate;
    }
    public function setOrderDate($dOrderDate)
    {
        $this->dOrderDate = $dOrderDate;
    }
    public function getTotal()
    {
        return $this->fTotal;
    }
    public function setTotal($fTotal)
    {
        $this->fTotal = $fTotal;
    }

    public function getUser()
    {
        return $this->oUser;
    }

    public function setUser($oUser)
    {
        $this->oUser = $oUser;
    }

    public function getStatus()
    {
        return $this->oStatus;
    }

    public function setStatus($oStatus)
    {
        $this->oStatus = $oStatus;
    }


    public static function loadAll() {
        $ret = array();

        $sql = "select OrderID, OrderDate, Total, UserID, o.StatusID as StatusID,  s.StatusName as StatusName , StatusColor " .
            " from orders o, statuses s" .
            " where o.StatusID = s.StatusID";
        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $iOrderID = $row["OrderID"];
            $dOrderDate = $row["OrderDate"];
            $fTotal = $row["Total"];
            $iUserID = $row["UserID"];
            $oUser = new User($iUserID);
            $iStatusID = $row["StatusID"];
            $strStatusName = $row["StatusName"];
            $strStatusColor = $row["StatusColor"];
            $oStatus = new Status($iStatusID,$strStatusName, $strStatusColor);

            $d = new Order($iOrderID, $fTotal, $dOrderDate,$oUser , $oStatus );
            array_push($ret, $d);
        }

        return $ret;
    }
    public function loadLimit( $rowsPerPage , $offset, $SortName, $SortType ) {

        $iOrderID = $this->iOrderID == -1 ? "" : $this->iOrderID;
        $dOrderDate = $this->getOrderDate();
      // $strOrderDate = date("y-m-d", $dOrderDate);
        $aTotal = $this->getTotal();
        $TotalFrom = $aTotal[0] * 1000000;
        $TotalTo = $aTotal[1] * 1000000;
        $iUserID = $this->oUser->getUserID() == -1 ? "" : $this->oUser->getUserID();
        $iStatusID =  $this->oStatus->getStatusID() ;
        $ret = array();
        $sql = "select OrderID, OrderDate, Total, UserID, o.StatusID as StatusID,  s.StatusName as StatusName , StatusColor "
            . " from orders o, statuses s "
            . " where o.StatusID = s.StatusID and OrderID like '%".$iOrderID."%' and UserID like '%". $iUserID ."%' ";
        if (!empty($aTotal)){
            $sql .= " and Total between " . $TotalFrom ." and " . $TotalTo;
        }
            //. "and DATE(o.OrderDate) = '" . $strOrderDate . "' " ;
        if ($iStatusID != -1){
            $sql .= " and o.StatusID =". $iStatusID ;
        }
        if ($SortName == "") {
            $sql .= " order by DeviceID " . $SortType;
        }
        else{
            $sql .= " order by ". $SortName . " ". $SortType;
        }

        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }

        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $iOrderID = $row["OrderID"];
            $dOrderDate = new DateTime($row["OrderDate"]);
            $fTotal = $row["Total"];
            $oUser = new User($row["UserID"]);
            $iStatusID = $row["StatusID"];
            $strStatusName = $row["StatusName"];
            $strStatusColor = $row["StatusColor"];
            $oStatus = new Status($iStatusID,$strStatusName, $strStatusColor);
            $o = new Order($iOrderID, $fTotal, $dOrderDate, $oUser, $oStatus);
            array_push($ret, $o);
        }
        return $ret;
    }
    public function countRecords(){
        $iOrderID = $this->iOrderID == -1 ? "" : $this->iOrderID;
        $dOrderDate = $this->getOrderDate();
        // $strOrderDate = date("y-m-d", $dOrderDate);
        $aTotal = $this->getTotal();
        $TotalFrom = $aTotal[0] * 1000000;
        $TotalTo = $aTotal[1] * 1000000;
        $iUserID = $this->oUser->getUserID() == -1 ? "" : $this->oUser->getUserID();
        $iStatusID =  $this->oStatus->getStatusID() ;

        $sql = "select COUNT(*) AS numrows "
            . " from orders o, statuses s "
            . " where o.StatusID = s.StatusID and OrderID like '%".$iOrderID."%' and UserID like '%". $iUserID ."%' ";
        if (!empty($aTotal)){
            $sql .= " and Total between " . $TotalFrom ." and " . $TotalTo;
        }
        //. "and DATE(o.OrderDate) = '" . $strOrderDate . "' " ;
        if ($iStatusID != -1){
            $sql .= " and o.StatusID =". $iStatusID ;
        }
        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }
    public static function getOrder( $iOrderID){
        $o = null;
        $sql = "select OrderID, OrderDate, Total, UserID, o.StatusID as StatusID,  s.StatusName as StatusName  , StatusColor"
            . " from orders o, statuses s "
            . " where  OrderID = " . $iOrderID ;
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            $iOrderID = $row["OrderID"];
            $dOrderDate = new DateTime($row["OrderDate"]);
            $fTotal = $row["Total"];
            $oUser = new User($row["UserID"]);
            $iStatusID = $row["StatusID"];
            $strStatusName = $row["StatusName"];
            $strStatusColor = $row["StatusColor"];
            $oStatus = new Status($iStatusID,$strStatusName, $strStatusColor);
            $o = new Order($iOrderID, $fTotal, $dOrderDate, $oUser, $oStatus);
        }
        return $o;
    }
    public static function getValueMaxColName($MaxColName){
        $sql = "select MAX(" . $MaxColName . ") AS Max "
            . "from orders ";
        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $max = $row['Max'];
        return $max;
    }
    public function insert() {

        $strOrderDate =  date_format($this->dOrderDate, 'Y-m-d H:i:s');
        $iUserID = $this->oUser->getUserID();
        $fTotal = $this->fTotal;
        $sql = "INSERT INTO orders (OrderDate ,UserID, Total, StatusID ) VALUES "
            . "('{$strOrderDate}' , {$iUserID}, {$fTotal}, 1 )";

        $iOrderID = DataProvider::execNonQueryIdentity($sql);
        $this->setOrderID($iOrderID);
    }
    public function update() {
        $iOrderID = $this->iOrderID;
        $iStatus = $this->oStatus->getStatusID();
        $sql = sprintf( "UPDATE orders SET StatusID = {$iStatus} "
            . "WHERE OrderID ={$iOrderID}");
        DataProvider::execNonQuery($sql);
    }

}
