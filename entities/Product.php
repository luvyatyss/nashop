<?php

if (file_exists( "../helper/DataProvider.php")){
    include_once "../helper/DataProvider.php";
}
else
    include_once "./helper/DataProvider.php";
include_once "Category.php";


/**
 * Description of products
 *
 * @author luvyatyss
 */
class Product
{
    //put your code here
    var $iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
        $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus;

    function __construct($iProID = -1, $strProName = "", $strImageURL = "", $fPrice = 0, DateTime $dProCreated = null, $iOnOrder = 0, $iInStock = 0,
                         $iView = 0, $strTinyDes = "", $strFullDes = "", Category $oCatPro = null, $iStatus = -1)
    {
        $this->iProID = $iProID;
        $this->strProName = $strProName;
        $this->strImageURL = $strImageURL;
        $this->fPrice = $fPrice;
        if ($dProCreated == null) {
            $dProCreated = new DateTime();
        }
        $this->dProCreated = $dProCreated;
        $this->iOnOrder = $iOnOrder;
        $this->iInStock = $iInStock;
        $this->iView = $iView;
        $this->strTinyDes = $strTinyDes;
        $this->strFullDes = $strFullDes;
        $this->oCatPro = $oCatPro == null ? new Category() : $oCatPro;
        $this->iStatus = $iStatus;
    }


    public function getProID()
    {
        return $this->iProID;
    }

    public function setProID($iProID)
    {
        $this->iProID = $iProID;
    }

    public function getProName()
    {
        return $this->strProName;
    }

    public function setProName($strProName)
    {
        $this->strProName = $strProName;
    }

    public function getImageURL()
    {
        return $this->strImageURL;
    }

    public function setImageURL($strImageURL)
    {
        $this->strImageURL = $strImageURL;
    }

    public function getPrice()
    {
        return $this->fPrice;
    }

    public function setPrice($fPrice)
    {
        $this->fPrice = $fPrice;
    }

    public function getProCreated()
    {
        return $this->dProCreated;
    }

    public function setProCreated($dProCreated)
    {
        $this->dProCreated = $dProCreated;
    }

    public function getOnOrder()
    {
        return $this->iOnOrder;
    }

    public function setOnOrder($iOnOrder)
    {
        $this->iOnOrder = $iOnOrder;
    }

    public function getInStock()
    {
        return $this->iInStock;
    }

    public function setInStock($iInStock)
    {
        $this->iInStock = $iInStock;
    }

    public function getView()
    {
        return $this->iView;
    }

    public function setView($iView)
    {
        $this->iView = $iView;
    }

    public function getTinyDes()
    {
        return $this->strTinyDes;
    }

    public function setTinyDes($strTinyDes)
    {
        $this->strTinyDes = $strTinyDes;
    }

    public function getFullDes()
    {
        return $this->strFullDes;
    }

    public function setFullDes($strFullDes)
    {
        $this->strFullDes = $strFullDes;
    }

    public function getCatPro()
    {
        return $this->oCatPro;
    }

    public function setCatPro($oCatPro)
    {
        $this->oCatPro = $oCatPro;
    }

    public function getStatus()
    {
        return $this->iStatus;
    }

    public function setStatus($iStatus)
    {
        $this->iStatus = $iStatus;
    }



    public static function loadProductByProID($iProID)
    {
        $o = null;
        $sql = "select ProID, ProName, ImageURL, ProCreated , Price, InStock , OnOrder, View, TinyDes, FullDes , c.CatProID as CatProID , c.CatName as CatName, p.Discontinued as Discontinued  "
            . "from products as p , categories as c where c.CatProID = p.CatProID and ProID = " . $iProID;
        $resultSet = DataProvider::Load($sql);
        if ($row = $resultSet->fetch_assoc()) {
            $iProID = $row["ProID"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["Price"];
            $dProCreated = new DateTime($row["ProCreated"]);
            $iOnOrder = $row["OnOrder"];
            $iInStock = $row["InStock"];
            $iView = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $oCatPro = new Category($iCatProID, $strCatName);
            $iStatus = $row["Discontinued"];

            $o = new Product($iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
                $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus);

        }
        return $o;
    }
/*
     public static function loadProductsByCatID($p_iCatProID)
    {
        $result = array();
        $sql = "SELECT ProId,ProName ,ImageURL ,DateInput, Quantity , QuantitySell , QuantityView,TinyDes ,FullDes , p.CatProID as CatProID , p.Discontinued as Discontinued"
            . "  FROM PRODUCT p , Categories c "
            . "  WHERE P.CatProID = C.CatProID AND P.CatProID = $p_iCatProID ";
        $list = DataProvider::execQuery($sql);
        while ($row = mysqli_fetch_array($list)) {
            $iProID = $row["ProId"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["ImageURL"];
            $dateInput = $row["DateInput"];
            $iQuantity = $row["Quantity"];
            $iQuantitySell = $row["Ins"];
            $iQuantityViewed = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $bDiscontinued = $row["Discontinued"];
            $OProduct = new Product($iProID, $strProName, $strImageURL, $fPrice, $dateInput, $iQuantity, $iQuantitySell,
                $iQuantityViewed, $strTinyDes, $strFullDes, $iCatProID, $bDiscontinued);

            array_push($result, $OProduct);
        }
        return $result;
    }
    public static function loadProductNew()
    {
        $result = array();
        $sql = "select ProID, ProName, ImageURL, ProCreated , Price, InStock , OnOrder, View, TinyDes, FullDes , c.CatProID as CatProID , c.CatName as CatName, p.Discontinued as Discontinued  "
            . "from products as p , categories as c where c.CatProID = p.CatProID "
            . "order by ProCreated DESC "
            . "limit 0,10 ";
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iProID = $row["ProID"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["Price"];
            $dProCreated = new DateTime($row["ProCreated"]);
            $iOnOrder = $row["OnOrder"];
            $iInStock = $row["InStock"];
            $iView = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $oCatPro = new Category($iCatProID, $strCatName);
            $iStatus = $row["Discontinued"];

            $o = new Product($iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
                $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus);
            array_push($result, $o);

        }
        return $result;
    }

    public static function loadProductMostVied()
    {
        $result = array();
        $sql = "select ProID, ProName, ImageURL, ProCreated , Price, InStock , OnOrder, View, TinyDes, FullDes , c.CatProID as CatProID , c.CatName as CatName, p.Discontinued as Discontinued  "
            . "from products as p , categories as c where c.CatProID = p.CatProID "
            . "order by View DESC "
            . "limit 0,10 ";
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iProID = $row["ProID"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["Price"];
            $dProCreated = new DateTime($row["ProCreated"]);
            $iOnOrder = $row["OnOrder"];
            $iInStock = $row["InStock"];
            $iView = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $oCatPro = new Category($iCatProID, $strCatName);
            $iStatus = $row["Discontinued"];

            $o = new Product($iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
                $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus);
            array_push($result, $o);

        }
        return $result;
    }

    public static function loadProductSell()
    {
        $result = array();
        $sql = "select ProID, ProName, ImageURL, ProCreated , Price, InStock , OnOrder, View, TinyDes, FullDes , c.CatProID as CatProID , c.CatName as CatName, p.Discontinued as Discontinued  "
            . "from products as p , categories as c where c.CatProID = p.CatProID "
            . "order by View DESC "
            . "limit 0,10 ";
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iProID = $row["ProID"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["Price"];
            $dProCreated = new DateTime($row["ProCreated"]);
            $iOnOrder = $row["OnOrder"];
            $iInStock = $row["InStock"];
            $iView = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $oCatPro = new Category($iCatProID, $strCatName);
            $iStatus = $row["Discontinued"];

            $o = new Product($iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
                $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus);
            array_push($result, $o);

        }
        return $result;
    }
*/
    public static function printListProduct($ListPro){
        if (empty($ListPro)){
            echo "<div style='margin: 20px;' class=\"alert alert-info\" role=\"alert\">Không có sản phẩm !</div>" ;
        }
        for ($i = 0; $i < count($ListPro); $i++) {
            $html = '';
            $html .= '<div class="product-row clearfix">';
            $html .= '<!--Product-col-->';
            for ($j = $i + 3; $i < $j && $i < count($ListPro); $i++) {
                $html .= '<div class="col-md-4 col-sm-12 product-col">';
                $html .= '<div class="product-block" >';
                $html .= '<div class="image" >';
                $srcImage = $ListPro[$i]->getImageURL();
                $srcImage = substr($srcImage, 1, strlen($srcImage));
                $html .= '<a class="img" href = "details.php?ProID=' . $ListPro[$i]->getProID() . '">';
                $html .= '<img src ="' . $srcImage . '" alt = "' . $ListPro[$i]->getProName() . '" class="img-responsive" ></a >';
                $html .= '</div ><div class="product-meta" ><div class="name" >';
                $html .= '<a href = "details.php?ProID=' . $ListPro[$i]->getProID() . '">' . $ListPro[$i]->getProName() . '</a>';
                $html .= '</div><div class="description">' . $ListPro[$i]->getTinyDes() . '</div>';
                $html .= '<div class="price">';
                $html .= '<div class="price-new">' . number_format($ListPro[$i]->getPrice(), 0) . ' đ </div>';
                if ($ListPro[$i]->getPrice() > 0) {
                    $html .= '<div class="price-old">' . number_format($ListPro[$i]->getPrice(), 0) . ' đ </div>';
                }
                $html .= '</div>';
                $html .= '<div class="bottom">';
                $html .= '<div class="cart"> <span class="icon-cart"></span>';
                $html .= '<button class="btn btn-shopping-cart" data-proid="'. $ListPro[$i]->getProID().'"><span>Thêm vào giỏ</span></button>';
                $html .= '</div></div></div></div></div>';
                $html .= '<!--/Product-col-->';
            }
            $i--;
            $html .= '</div>';
            echo $html;
        }
    }
    public static function loadAll()
    {
        $ret = array();
        $sql = "select ProID, ProName, ImageURL, ProCreated , Price, InStock , OnOrder, View, TinyDes, FullDes , c.CatProID as CatProID , c.CatName as CatName, p.Discontinued as Discontinued "
            . "from products as p , categories as c where c.CatProID = p.CatProID ";
        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iProID = $row["ProID"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["Price"];
            $dProCreated = new DateTime($row["ProCreated"]);
            $iOnOrder = $row["OnOrder"];
            $iInStock = $row["InStock"];
            $iView = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $oCatPro = new Category($iCatProID, $strCatName);
            $iStatus = $row["Discontinued"];

            $o = new Product($iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
                $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus);
            array_push($ret, $o);
        }
        return $ret;
    }

    public function loadLimit($rowsPerPage = 0 , $offset = 0 , $SortName="" , $SortType = "" )
    {
        $iProID = $this->iProID == -1 ? "" : $this->iProID;
        $strProName = $this->strProName;

        $iCatProID = $this->oCatPro->getCatID();
        $iBraID = $this->oCatPro->getBrand()->getBraID();
        $iDeviceID = $this->oCatPro->getDevice()->getDeviceID();
        $iStatus = $this->iStatus;
        $aPrice = $this->getPrice();
        $PriceFrom = $aPrice[0] * 1000000;
        $PriceTo = $aPrice[1] * 1000000;

        $aInStock = $this->getInStock();
        $InStockFrom = $aInStock[0] * 1 ;
        $InStockTo = $aInStock[1] * 1 ;
        $ret = array();
        $sql = "select ProID, ProName, ImageURL, ProCreated , Price, InStock , OnOrder, View, TinyDes, FullDes , c.CatProID as CatProID , c.CatName as CatName, p.Discontinued as Discontinued "
            . "from products as p , categories as c   "
            . "where c.CatProID = p.CatProID and ProID like '%" . $iProID . "%' and ProName like '%" . $strProName . "%' ";
        if (!empty($aPrice)){
            $sql .= "and Price between " . $PriceFrom ." and " . $PriceTo;
        }
        if (!empty($aInStock)) {
            $sql .= " and InStock between " . $InStockFrom . " and " . $InStockTo;
        }
        if ($iCatProID != -1) {
            $sql .= " and p.CatProID =" . $iCatProID;
        }
        if ($iBraID != -1) {
            $sql .= " and c.BraID =" . $iBraID;
        }
        if ($iDeviceID != -1) {
            $sql .= " and c.DeviceID =" . $iDeviceID;
        }
        if ($iStatus != -1) {
            $sql .= " and p.Discontinued =" . $iStatus;
        }
        if ($SortName == "") {
            $sql .= " order by ProID " . $SortType;
        } else {
            $sql .= " order by " . $SortName . " " . $SortType;
        }
        if ($rowsPerPage != 0) {
            $sql .= " LIMIT " . $offset . ", " . $rowsPerPage;
        }

        $resultSet = DataProvider::Load($sql);
        while ($row = $resultSet->fetch_assoc()) {
            $iProID = $row["ProID"];
            $strProName = $row["ProName"];
            $strImageURL = $row["ImageURL"];
            $fPrice = $row["Price"];
            $dProCreated = new DateTime($row["ProCreated"]);
            $iOnOrder = $row["OnOrder"];
            $iInStock = $row["InStock"];
            $iView = $row["View"];
            $strTinyDes = $row["TinyDes"];
            $strFullDes = $row["FullDes"];
            $iCatProID = $row["CatProID"];
            $strCatName = $row["CatName"];
            $oCatPro = new Category($iCatProID, $strCatName);
            $iStatus = $row["Discontinued"];

            $o = new Product($iProID, $strProName, $strImageURL, $fPrice, $dProCreated, $iOnOrder, $iInStock,
                $iView, $strTinyDes, $strFullDes, $oCatPro, $iStatus);
            array_push($ret, $o);
        }
        return $ret;
    }

    public function countRecords()
    {
        $iProID = $this->iProID == -1 ? "" : $this->iProID;
        $strProName = $this->strProName;
        //$strProCreated = date('Y-m-d H:i:s', $this->dProCreated);
        $aPrice = $this->getPrice();
        $PriceFrom = $aPrice[0] * 1000000;
        $PriceTo = $aPrice[1] * 1000000;

        $aInStock = $this->getInStock();
        $InStockFrom = $aInStock[0] * 1 ;
        $InStockTo = $aInStock[1] * 1;
        $iCatProID = $this->oCatPro->getCatID();
        $iBraID = $this->oCatPro->getBrand()->getBraID();
        $iDeviceID = $this->oCatPro->getDevice()->getDeviceID();
        $iStatus = $this->iStatus;
        $sql = "select COUNT(*) AS numrows "
            . "from products as p , categories as c "
            . "where c.CatProID = p.CatProID and  ProID like '%" . $iProID . "%' and ProName like '%" . $strProName . "%' ";
        if (!empty($aPrice)){
            $sql .= "and Price between " . $PriceFrom ." and " . $PriceTo;
        }
        if (!empty($aInStock)) {
            $sql .= " and InStock between " . $InStockFrom . " and " . $InStockTo;
        }
        if ($iCatProID != -1) {
            $sql .= " and c.CatProID =" . $iCatProID;
        }
        if ($iBraID != -1) {
            $sql .= " and c.BraID =" . $iBraID;
        }
        if ($iDeviceID != -1) {
            $sql .= " and c.DeviceID =" . $iDeviceID;
        }
        if ($iStatus != -1) {
            $sql .= " and p.Discontinued =" . $iStatus;
        }

        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $numrows = $row['numrows'];
        return $numrows;
    }
    public static function getValueMaxColName($MaxColName){
        $sql = "select MAX(" . $MaxColName . ") AS Max "
            . "from products ";
        $resultSet = DataProvider::Load($sql);
        $row = $resultSet->fetch_assoc();
        $max = $row['Max'];
        return $max;
    }
    public function insert()
    {
        $strProName = $this->strProName;
        $strProCreated = date('Y-m-d H:i:s', $this->dProCreated);
        $fPrice = $this->fPrice;
        $strTinyDes = $this->strTinyDes;
        $strFullDes = $this->strFullDes;
        $iInStock = $this->iInStock;
        $iCatProID = $this->oCatPro->getCatID();

        $sql = "INSERT INTO products (ProName, ProCreated , Price, InStock, TinyDes, FullDes , CatProId, Discontinued ) VALUES "
            . "( '{$strProName}' , '{$strProCreated}' , {$fPrice} , {$iInStock} , '{$strTinyDes}' , '{$strFullDes}' , {$iCatProID} , 0)";

        $iProID = DataProvider::execNonQueryIdentity($sql);
        $this->setProID($iProID);
    }

    public function update()
    {
        $iProID = $this->iProID;
        $strProName = $this->strProName;
        $strProCreated = date('Y-m-d H:i:s', $this->dProCreated);
        $fPrice = $this->fPrice;
        $strTinyDes = $this->strTinyDes;
        $strFullDes = $this->strFullDes;
        $iInStock = $this->iInStock;
        $iCatProID = $this->oCatPro->getCatID();
        $iStatus = $this->iStatus;

        $sql = "UPDATE products  SET ProName = '{$strProName}', ProCreated = '{$strProCreated}', Price ={$fPrice} , "
            . "InStock =  {$iInStock} , TinyDes = '{$strTinyDes}', FullDes ='{$strFullDes}' , CatProId = {$iCatProID} , Discontinued = {$iStatus}  "
            . "Where ProID = " . $iProID;

        DataProvider::execNonQuery($sql);
    }

    public function updateImageURL()
    {
        $iProID = $this->iProID;
        $strImageURL = $this->strImageURL;
        $sql = "UPDATE products SET  ImageURL = '{$strImageURL}'  "
            . "WHERE ProID = " . $iProID;
        DataProvider::execNonQuery($sql);
        return TRUE;
    }
    public function updateView()
    {
        $iProID = $this->iProID;
        $iView = $this->iView;
        $sql = "UPDATE products SET  View = {$iView}  "
            . "WHERE ProID = " . $iProID;
        DataProvider::execNonQuery($sql);
    }

    public function updateInStock()
    {
        $iProID = $this->iProID;
        $iInStock = $this->iInStock;
        $sql = "UPDATE products SET  InStock = {$iInStock}  "
            . "WHERE ProID = " . $iProID;

        DataProvider::execNonQuery($sql);

    }
    public function updateOnOrder()
    {
        $iProID = $this->iProID;
        $iOnOrder = $this->iOnOrder;
        $sql = "UPDATE products SET OnOrder = {$iOnOrder}  "
            . "WHERE ProID = " . $iProID;

        DataProvider::execNonQuery($sql);

    }
    public function delete()
    {
        $iProID = $this->iProID;
        $sql = "DELETE from products WHERE ProID =" . $iProID;
        DataProvider::execNonQuery($sql);
    }
}
