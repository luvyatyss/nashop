<?php

if (file_exists("../helper/DataProvider.php")) {
    include_once "../helper/DataProvider.php";
} else
    include_once "./helper/DataProvider.php";
include_once('Product.php');
include_once('Order.php');

/**
 * Description of OrderDetail
 *
 * @author luvyatyss
 */
class OrderDetail
{
    var $iOrderDetailID, $fPrice, $iQuantity, $oProduct, $oOrder, $fAmount;

    function __construct($iOrderDetailID = -1, Product $oProduct = null, Order $oOrder = null, $fPrice = 0, $iQuantity = 0, $fAmount = 0)
    {
        $this->iOrderDetailID = $iOrderDetailID;
        $this->oProduct = $oProduct == null ? new Product() : $oProduct;
        $this->oOrder = $oOrder == null ? new Order() : $oOrder;
        $this->fPrice = $fPrice;
        $this->iQuantity = $iQuantity;
        $this->fAmount = $fAmount;
    }


    public function getOrderDetailID()
    {
        return $this->iOrderDetailID;
    }

    public function setOrderDetailID($iOrderDetailID)
    {
        $this->iOrderDetailID = $iOrderDetailID;
    }

    public function getPrice()
    {
        return $this->fPrice;
    }

    public function setPrice($fPrice)
    {
        $this->fPrice = $fPrice;
    }

    public function getQuantity()
    {
        return $this->iQuantity;
    }

    public function setQuantity($iQuantity)
    {
        $this->iQuantity = $iQuantity;
    }

    public function getProduct()
    {
        return $this->oProduct;
    }

    public function setProduct($oProduct)
    {
        $this->oProduct = $oProduct;
    }

    public function getOrder()
    {
        return $this->oOrder;
    }

    public function setOrder($oOrder)
    {
        $this->oOrder = $oOrder;
    }

    public function getAmount()
    {
        return $this->fAmount;
    }

    public function setAmount($fAmount)
    {
        $this->fAmount = $fAmount;
    }

    public static function loadAll()
    {
        $ret = array();

        $sql = "select OrderDetailID, OrderID, ProID, od.Quantity, od.Price, Amount  from orderdetails";
        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $id = $row["OrderDetailID"];
            $oOrder = new Order($row["OrderID"]);
            $oProduct = new Product($row["ProductID"]);
            $quantity = $row["Quantity"];
            $price = $row["Price"];
            $amount = $row["Amount"];
            $o = new OrderDetail($id, $oProduct, $oOrder, $price, $quantity, $amount);
            array_push($ret, $o);
        }

        return $ret;
    }

    public function loadLimit($rowsPerPage, $offset, $SortName, $SortType)
    {
        $iOrderDetailID = $this->iOrderDetailID == -1 ? "" : $this->iOrderDetailID;
        $iOrderID= $this->oOrder->getOrderID();
        $ret = array();
        $sql = "select OrderDetailID, OrderID, p.ProID  as ProID, ProName ,od.Quantity, od.Price, Amount  "
            . " from orderdetails od, products p"
            . " where od.ProID  = p.ProID  and OrderDetailID  like '%" . $iOrderDetailID . "%' "
            . " and od.OrderID = " . $iOrderID ;

        if ($SortName == "") {
            $sql .= " order by OrderDetailID " . $SortType;
        } else {
            $sql .= " order by " . $SortName . " " . $SortType;
        }

        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }
        $resultSet = DataProvider::Load($sql);

        while ($row = $resultSet->fetch_assoc()) {
            $id = $row["OrderDetailID"];
            $oOrder = new Order($row["OrderID"]);
            $oProduct = new Product($row["ProID"], $row["ProName"]);
            $quantity = $row["Quantity"];
            $price = $row["Price"];
            $amount = $row["Amount"];
            $o = new OrderDetail($id, $oProduct, $oOrder, $price, $quantity, $amount);
            array_push($ret, $o);
        }
        return $ret;
    }

    public function countRecords()
    {
        $iOrderDetailID = $this->iOrderDetailID == -1 ? "" : $this->iOrderDetailID;
        $iOrderID= $this->oOrder->getOrderID();
        $sql = "select COUNT(*) AS numrows  from orderdetails od, products p"
            . " where od.ProID = p.ProID and OrderDetailID  like '%" . $iOrderDetailID . "%' "
            . " and od.OrderID = " . $iOrderID ;
        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }

    public static function getOrderDetail($iOrderDetailID)
    {
        $o = null;
        $sql = "select OrderDetailID, OrderID, p.ProID  as ProID, ProName , od.Quantity, od.Price, Amount  from orderdetails od, products p"
            . " where od.ProID  = p.ProID  and OrderDetailID  = " . $iOrderDetailID;
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            $id = $row["OrderDetailID"];
            $oOrder = new Order($row["OrderID"]);
            $oProduct = new Product($row["ProID"], $row["ProName"]);
            $quantity = $row["Quantity"];
            $price = $row["Price"];
            $amount = $row["Amount"];
            $o = new OrderDetail($id, $oProduct, $oOrder, $price, $quantity, $amount);
        }
        return $o;
    }

    public function insert()
    {

        $iOrderID = $this->oOrder->getOrderID();
        $iProID = $this->oProduct->getProID();
        $iQuantity = $this->iQuantity;
        $fPrice = $this->fPrice;
        $fAmount = $this->fAmount;
        $sql = "INSERT INTO orderdetails (OrderID, ProID,Quantity ,Price , Amount) VALUES "
            . "('{$iOrderID}' , {$iProID}, {$iQuantity}, {$fPrice} , {$fAmount} )";


        DataProvider::execNonQuery($sql);
    }
}
