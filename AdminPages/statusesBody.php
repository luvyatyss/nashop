<?php
if (!isset($_SESSION)) {
    session_start();
}
//Paging
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
$filterStatus = new Status();
if (isset($_POST['StatusID']) & !empty($_POST['StatusID'])) {
    $filterStatus->setStatusID($_POST['StatusID']);
}
if (isset($_POST['StatusName']) & !empty($_POST['StatusName'])) {
    $filterStatus->setStatusName($_POST['StatusName']);
}

if (isset($_POST['SortName']) & !empty($_POST['SortName'])) {
    $SortName = ($_POST['SortName']);
}
if (isset($_POST['SortType']) & !empty($_POST['SortType'])) {
    $SortType = ($_POST['SortType']);
}
$numberOfRows = $filterStatus->countRecords();// Số lượng dòng của bảng
$ListStatuses = array();
if ($rowsPerPage == -1) { // nếu là chọn hiển thị là ALL
    $rowsPerPage = $numberOfRows;
    $ListStatuses = $filterStatus->loadLimit($rowsPerPage, 0, $SortName, $SortType);
} else {
    $ListStatuses = $filterStatus->loadLimit($rowsPerPage, $offset, $SortName, $SortType);
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
        foreach ($ListStatuses as $ItemStatus) {
            $urlPara = "StatusID=" . $ItemStatus->getStatusID() . "&control=";
            $url = "statuses.php?". $urlPara;
            ?>
            <tr>
                <td style="width: 120px;" class="btn-modify-group text-center">

                    <a href="<?php echo $url . Controls::Update . $token; ?>"
                       class="btn btn-update icon-left" data-toggle="tooltip"
                       data-placement="top" title="Cập Nhập">
                        <i class="entypo-pencil"></i>
                    </a>

                    <a href="<?php echo $url . Controls::Delete .$token ; ?>"
                       class="btn btn-delete  icon-left" data-toggle="tooltip"
                       data-placement="top" title="Xóa" name="btnDelete">
                        <i class="entypo-cancel"></i>
                    </a>

                    <a href="<?php echo $url . Controls::Information . $token; ?>"
                       class="btn btn-info icon-left" data-toggle="tooltip"
                       data-placement="top" name="btnInfo" title="Thông Tin Chi Tiết">
                        <i class="entypo-info"></i>
                    </a>
                </td>
                <td style="width: 200px;"><?php echo $ItemStatus->getStatusID(); ?></td>
                <td style="width: 200px;"><?php echo $ItemStatus->getStatusName(); ?></td>
                <td style="width: 200px; ">
                    <span ><i style="display: block; height:16px; width:100px;  background-color: <?php echo $ItemStatus->getStatusColor(); ?>"></i></span>
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
        var StatusID = $('#txtFilterStatusID').val();
        var StatusName = $('#txtFilterStatusName').val();
        var dataContent = $('#dataContent');
        dataContent.text("");
        var doc = document.documentElement;
        var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        $.post('statusesBody.php', {
            page: page,
            show: show,
            StatusID: StatusID,
            StatusName: StatusName,
            SortName: sortName,
            SortType: sortType
        }, function (data) {
            dataContent.append(data);
            window.scrollTo(left, top);
        });
    };
</script>