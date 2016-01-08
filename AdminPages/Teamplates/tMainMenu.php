
<!-- add class "multiple-expanded" to allow multiple submenus to open -->
<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
<?php
require_once '../helper/crypter.php';
require_once '../helper/Utils.php';
require_once '../helper/Context.php';

if (isset($_SESSION["IsLogin"]) && $_SESSION["IsLogin"] == 1 && Context::getCurrentUser()["userPermission"] == 1) {
//set token
    $crypter = new Crypter("nhatanh");
    if (!isset($_SESSION["token"])) {
        $datetime = new DateTime();
        $str_datetime = date_format($datetime, 'Y-m-d H:i:s');
        $security = $str_datetime . " " . strval(rand());
        $encrypted = $crypter->Encrypt($security);
        $_SESSION["token"] = $security;
        $token = "token=" . $encrypted;
    } else {
        $encrypted = $crypter->Encrypt($_SESSION["token"]);
        $token = "token=" . $encrypted;
    }
}else{
    require_once '../helper/Utils.php';
    $url = "adminLogin.php";
    Utils::Redirect($url);
}
?>

<li class="">
    <a href="index.php?<?php echo $token; ?>">
        <i class="entypo-gauge"></i>
        <span>Dashboard</span>
    </a>
</li>
<li>
    <a href="devices.php?<?php echo $token; ?>">
        <i class="entypo-newspaper"></i>
        <span>Thiết Bị</span>
    </a>
</li>
<li>
    <a href="brands.php?<?php echo $token; ?>">
        <i class="entypo-newspaper"></i>
        <span>Nhà Sản Xuất</span>
    </a>
</li>
<li>
    <a href="categories.php?<?php echo $token; ?>">
        <i class="entypo-monitor"></i>
        <span>Loai Sản Phẩm</span>
    </a>
</li>
<li>
    <a href="products.php?<?php echo $token; ?>">
        <i class="entypo-layout"></i>
        <span>Sản Phẩm</span>
    </a>
</li>

<li>
    <a href="">
        <i class="entypo-mail"></i>
        <span>Quản Lý Đơn Hàng</span>
    </a>
    <ul>
        <li>
            <a href="orders.php?<?php echo $token; ?>">
                <i class="entypo-inbox"></i>
                <span>Danh Sách Hóa Đơn</span>
            </a>
        </li>
        <li>
            <a href="orderDetails.php?<?php echo $token; ?>">
                <i class="entypo-pencil"></i>
                <span>Chi Tiết Hóa Đơn</span>
            </a>
        </li>
        <li>
            <a href="statuses.php?<?php echo $token; ?>">
                <i class="entypo-attach"></i>
                <span>Tình Trạng Hóa Đơn</span>
            </a>
        </li>
    </ul>
</li>

<script>
    var pgurl =  window.location.href.split('/').pop();
    $("#main-menu li>a").each(function(){
        if($(this).attr("href") == pgurl ) {
            $(this).parent().addClass("active");
        }
    });
</script>