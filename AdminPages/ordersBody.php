<?php
if (!isset($_SESSION)) {
    session_start();
}
//Paging
require_once '../entities/Order.php';
require_once '../entities/Status.php';
require_once '../helper/Pagination.php';
require_once '../helper/Controls.php';
require_once '../helper/crypter.php';

$rowsPerPage = 5; // số lượng dòng được hiển thị 1 trang
$curPage = 1; // Trang hiện tại
if (isset($_POST['page']) && !empty(($_POST['page']))) {
    $curPage = $_POST['page'];//truyền thứ tự trang cần xem
}
if (isset($_POST['show']) && !empty(($_POST['show']))) {
    $rowsPerPage = $_POST['show'];//truyền số record cần hiển thịs
}
$offset = ($curPage - 1) * $rowsPerPage;// tính offset bắt đầu load

//filter
$SortName = "";
$SortType = true;
$filterOrd = new Order();

if (isset($_POST['OrderID']) & !empty($_POST['OrderID'])) {
    $filterOrd->setOrderID($_POST['OrderID']);
}
if (isset($_POST['UserID']) & !empty($_POST['UserID'])) {
    require_once "../entities/User.php";
    $filterOrd->setUser(new User($_POST['UserID']));
}
if (isset($_POST['Total']) & !empty($_POST['Total'])) {
    $filterOrd->setTotal($_POST['Total']);
}
if (isset($_POST['Status']) & !empty($_POST['Status'])) {
    require_once "../entities/Status.php";
    $filterOrd->setStatus(new Status($_POST['Status']));
}
if (isset($_POST['SortName']) & !empty($_POST['SortName'])) {
    $SortName = ($_POST['SortName']);
}
if (isset($_POST['SortType']) & !empty($_POST['SortType'])) {
    $SortType = ($_POST['SortType']);
}
$numberOfRows = $filterOrd->countRecords();// Số lượng dòng của bảng
$ListOrders = array();

if ($rowsPerPage == -1) { // nếu là chọn hiển thị là ALL
    $rowsPerPage = $numberOfRows;
    $ListOrders = $filterOrd->loadLimit($rowsPerPage, 0, $SortName, $SortType);
} else {
    $ListOrders = $filterOrd->loadLimit($rowsPerPage, $offset, $SortName, $SortType);
}


//tổng số trang cần hiển thị

$self = $_SERVER['PHP_SELF']; // Lay dia chi truc tiep cua PHP dang mo
$pagination = new Pagination($curPage, $rowsPerPage, $offset, $numberOfRows, $self);

//end Paging
?>

<div id="mainData">
    <table class="table table-bordered">
        <tbody>
        <?php
        $crypter = new Crypter("nhatanh");
        $encrypted = $crypter->Encrypt($_SESSION["token"]);
        $token = "&token=" . $encrypted;
        foreach ($ListOrders as $ItemOrder) {
            $urlPara = "OrderID=" . $ItemOrder->getOrderID() . "&control=";
            $urlUpdate = "orders.php?" . $urlPara;
            $urlInfo = "orderDetails.php?" . $urlPara;
            ?>
            <tr>
                <td style="width: 120px;" class="btn-modify-group text-center">
                    <a href="<?php echo $urlUpdate . Controls::Update . $token; ?>"
                       class="btn btn-update icon-left" data-toggle="tooltip" data-placement="top" title="Cập Nhập">
                        <i class="entypo-pencil"></i>
                    </a>
                    <a href="<?php echo $urlInfo . Controls::Information . $token; ?>"
                       class="btn btn-info icon-left" data-toggle="tooltip" data-placement="top" title="Thông tin">
                        <i class="entypo-info"></i>
                    </a>
                </td>
                <td style="width: 200px"><?php echo $ItemOrder->getOrderID(); ?></td>
                <td style="width: 200px"><?php echo $ItemOrder->getUser()->getUserID(); ?></td>
                <td style="width: 200px"><?php echo date_format($ItemOrder->getOrderDate(), 'd/m/y'); ?></td>
                <td style="width: 200px" class="text-right"><?php echo number_format($ItemOrder->getTotal(), 0); ?></td>
                <td class="status">
                    <?php
                    echo '<span class="label" style="background-color:'. $ItemOrder->getStatus()->getStatusColor() .';">' . $ItemOrder->getStatus()->getStatusName() . "</span>";
                    ?>
                </td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>
<table class="table">
    <tfoot>
    <tr>
        <td colspan="2" class="record" style="vertical-align: middle; padding: 0 20px;">
            <?php
            if ($numberOfRows != 0) {
                $maxItem = $offset + $rowsPerPage < $numberOfRows ? $offset + $rowsPerPage : $numberOfRows;
                echo ($offset + 1) . " - " . $maxItem . " of " . $numberOfRows . " items";
            } else {
                echo "No records to view";
            }
            ?>
        </td>
        <td class="text-right" id="paging" colspan="4">
            <!-- PAGINATION -->
            <?php $pagination->printPaging(); ?>
            <!-- /PAGINATION -->
        </td>
    </tr>
    </tfoot>
</table>
<script>
    var show = "<?php echo $rowsPerPage; ?>";
    var sortName = "<?php echo $SortName;?>";
    var sortType = "<?php echo $SortType; ?>";
    $('#paging a').click(function () {
        var page = $(this).text();
        if (page.length === 0) {
            page = $(this).find('.sr-only').text();
        }
        Filter(page, show);
    });
    //Filter
    var Filter = function (page, show) {
        var OrderID = $('#txtFilterOrderID').val();
        var UserID = $('#txtFilterUserID').val();
        var Total = $('#txtFilterTotal').val().split(',');

        var Status = $('#cboFilterStatus').val();

        var dataContent = $('#dataContent');
        dataContent.text("");
        var doc = document.documentElement;
        var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        $.post('ordersBody.php', {
            page: page,
            show: show,
            OrderID: OrderID,
            UserID: UserID,
            Total: Total,
            Status: Status,
            SortName: sortName,
            SortType: sortType
        }, function (data) {
            dataContent.append(data);
            window.scrollTo(left, top);
        });
    };
</script>