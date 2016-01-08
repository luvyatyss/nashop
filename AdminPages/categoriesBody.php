<?php
if (!isset($_SESSION)) {
    session_start();
}
//Paging
require_once '../entities/Category.php';
require_once '../helper/Pagination.php';
require_once '../helper/Controls.php';
require_once '../helper/crypter.php';
$crypter = new Crypter("nhatanh");


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
$filterCategory = new Category(-1, "", new Brand(), new Device(), -1);
if (isset($_POST['CatProID']) & !empty($_POST['CatProID'])) {
    $filterCategory->setCatID($_POST['CatProID']);
}
if (isset($_POST['CatName']) & !empty($_POST['CatName'])) {
    $filterCategory->setCatName($_POST['CatName']);
}
if (isset($_POST['DeviceID']) & !empty($_POST['DeviceID'])) {
    $filterCategory->setDevice(new Device($_POST['DeviceID']));
}
if (isset($_POST['Status']) & !empty($_POST['Status'])) {
    $filterCategory->setStatus($_POST['Status']);
}
if (isset($_POST['SortName']) & !empty($_POST['SortName'])) {
    $SortName = ($_POST['SortName']);
}
if (isset($_POST['SortType']) & !empty($_POST['SortType'])) {
    $SortType = ($_POST['SortType']);
}
$numberOfRows = $filterCategory->countRecords();// Số lượng dòng của bảng
$ListCategories = array();
if ($rowsPerPage == -1) { // nếu là chọn hiển thị là ALL
    $rowsPerPage = $numberOfRows;
    $ListCategories = $filterCategory->loadLimit($rowsPerPage, 0, $SortName, $SortType);
} else {
    $ListCategories = $filterCategory->loadLimit($rowsPerPage, $offset, $SortName, $SortType);
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
        foreach ($ListCategories as $ItemCat) {
            $urlPara = "CatProID=" . $ItemCat->getCatID() . "&control=";
            $url = "categories.php?" . $urlPara;
            ?>
            <tr>
                <td style="width: 120px;" class="btn-modify-group text-center">

                    <a href="<?php echo $url . Controls::Update . $token; ?>"
                       class="btn btn-update icon-left" data-toggle="tooltip"
                       data-placement="top" title="Cập Nhập">
                        <i class="entypo-pencil"></i>
                    </a>

                    <a href="<?php echo $url . Controls::Delete . $token; ?>"
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
                <td style="width: 200px;"><?php echo $ItemCat->getCatID(); ?></td>
                <td style="width: 200px;"><?php echo $ItemCat->getCatName(); ?></td>
                <td style="width: 200px;"><?php
                    $Brand = $ItemCat->getBrand();
                    echo $Brand->getBraName();
                    ?></td>
                <td style="width: 200px;"><?php
                    $Device = $ItemCat->getDevice();
                    echo $Device->getDeviceName();
                    ?></td>
                <td class="status">
                    <?php
                    if ($ItemCat->getStatus() == 1) {
                        echo "<span class=\"label label-danger\">Tạm ngưng</span>";
                    } else {
                        echo "<span class=\"label label-success\">Đang hoạt động</span>";
                    }
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
    $(function () {
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
            var CatProID = $('#txtFilterCatProID').val();
            var CatName = $('#txtFilterCatName').val();
            var DeviceID = $('#cboFilterDeives').val();
            var BraID = $('#cboFilterBrands').val();
            var Status = $('#cboFilterStatus').val();

            var dataContent = $('#dataContent');
            dataContent.text("");
            var doc = document.documentElement;
            var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
            var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
            $.post('categoriesBody.php', {
                page: page,
                show: show,
                CatProID: CatProID,
                CatName: CatName,
                BraID: BraID,
                DeviceID: DeviceID,
                Status: Status,
                SortName: sortName,
                SortType: sortType
            }, function (data) {
                dataContent.append(data);
                window.scrollTo(left, top);
            });
        };
    });
</script>