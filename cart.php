<?php
if (!isset($_SESSION)) {
    session_start();
}
$fail = false;

if (!isset($_GET["token"]) || $_SESSION["IsLogin"] == 0) {
    $fail = true;
} else {
    require_once './helper/crypter.php';
    $crypter = new Crypter("nhatanh");
    $decrypted = $crypter->Decrypt(str_replace(" ", "+", $_GET["token"]));
    //$data = explode("/", $decrypted);
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $decrypted) {
        $fail = true;
    } else {
        $fail = false;
    }
}
if ($fail) {
    require_once './helper/Utils.php';
    $_SESSION['showModalLogin'] = true;
    $url = "index.php";
    Utils::Redirect($url);

} else {
    require_once './helper/Page.php';
    require_once './entities/Cart.php';
    require_once './entities/Product.php';
    require_once './entities/Category.php';
    $page = new Page();
    $page->addCSS('assets/css/shoppingCart.css');
    $page->addCSS('assets/js/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css');
    $page->addCSS("assets/js/sweetalert/sweetalert.css");

    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->addJavascript('assets/js/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js');
    $page->setTitle('Danh Sách Sản Phẩm');
    $page->startBody();

    require_once './entities/Order.php';
    require_once './entities/OrderDetail.php';

    date_default_timezone_set('Asia/Bangkok');
    $insert = null;
    if (isset($_POST["txtDelProId"])) {
        $updProId = explode(',', $_POST["txtDelProId"]);
        for ($i = 0; $i < count($updProId); $i++) {
            Cart::removeItem($updProId[$i]);
        }
    }
    if (isset($_POST["txtUpdProId"])) {
        $updProId = explode(',', $_POST["txtUpdProId"]);
        $q = explode(',', $_POST["txtUpdQ"]);
        for ($i = 0; $i < count($updProId); $i++) {
            Cart::updateItem($updProId[$i], $q[$i]);
        }
    }

    if (isset($_POST["btnCheckout"])) {
        require_once 'helper/Context.php';
        $total = $_POST["txtTotal"];
        $ord = new Order();
        $ord->setOrderDate(new DateTime());
        $ord->setTotal($total);
        $ord->setUser(new User(Context::getCurrentUser()["userID"]));
        $ord->insert();

        foreach ($_SESSION["Cart"] as $proId => $quantity) {
            $pro = Product::loadProductByProID($proId);
            $amount = $pro->getPrice() * $quantity;
            $detail = new OrderDetail(-1, $pro, $ord, $pro->getPrice(), $quantity, $amount);
            $detail->insert();

            //Cap nhap lai so luong ton
            $inStock = $pro->getInStock() - $quantity;
            $pro->setInStock($inStock);
            $pro->updateInStock();

            //Cap nhap lai so luong ban
            $onOrder = $pro->getOnOrder() + $quantity;
            $pro->setOnOrder($onOrder);
            $pro->updateOnOrder();
        }
        $insert = true;
        Cart::destroyCart();
    }

    ?>
    <!--PRODUCTS-->
    <div class="box clearfix">
        <ol class="breadcrumb box-top">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <li class="active">Giỏ hàng</li>
        </ol>
        <div id="shopping-cart">
            <h1>Giỏ Hàng</h1>
            <?php
            $arrCarts = array();
            if (isset($_SESSION["Cart"]) && !empty($_SESSION["Cart"])) {
                $arrCarts = $_SESSION["Cart"];
            }
            if (count($arrCarts) == 0) {
                echo "<div class=\"alert alert-info\" role=\"alert\"><i class=\"fa fa-bullhorn\"></i>Không có sản phẩm nào trong giỏ!</div>";
            } else {
                ?>
                <form id="frmCart" action="cart.php?token=<?php echo $_GET["token"];?>" method="post">
                    <input type="hidden" value="" id="txtDelProId" name="txtDelProId"/>

                    <input type="hidden" value="" id="txtUpdProId" name="txtUpdProId"/>
                    <input type="hidden" value="" id="txtUpdQ" name="txtUpdQ"/>

                    <div class="text-right" id="modifyAll" style="margin-bottom: 10px;">
                        <button type="button" class="btn btn-update" data-toggle="tooltip"
                                data-placement="top" title="Cập Nhập Tất Cả" id="btnUpdateAll">
                            <i class="fa fa-refresh"></i>
                        </button>
                        <button type="button" class="btn btn-delete" data-toggle="tooltip"
                                data-placement="top" title="Xóa Tất Cả" id="btnDeleteAll">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    </div>
                    <!--Main-Shopping-cart-->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td class="text-center">Ảnh</td>
                                <td class="text-center">Tên Sản Phẩm</td>
                                <td class="text-center">Mã Sản Phẩm</td>
                                <td class="text-center">Số Lượng</td>
                                <td class="text-center">Đơn Giá</td>
                                <td class="text-center">Tổng tiền</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total = 0;
                            foreach ($arrCarts as $proID => $quantity) {
                                $pro = Product::loadProductByProID($proID);
                                $amount = $pro->getPrice() * $quantity;
                                $srcImage = $pro->getImageURL();
                                $srcImage = substr($srcImage, 1, strlen($srcImage));
                                $urlPro = 'details.php?ProID=' . $pro->getProID();
                                $urlCat = 'productsByCat.php?CatID=' . $pro->getCatPro()->getCatID();
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <a href="<?php echo $urlPro; ?>">
                                            <img style="width: 64px" src="<?php echo $srcImage; ?>" alt="Sản phẩm"
                                                 class="img-thumbnail">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $urlPro; ?>"><?php echo $pro->getProName(); ?></a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $urlCat; ?>">
                                            <?php echo $pro->getCatPro()->getCatName(); ?>
                                        </a>
                                    </td>
                                    <td class="text-left">
                                        <div class="input-group btn-block order-quantity">
                                            <input type="text" name="txtQuantity" class="form-control"
                                                   id="txtQuantity_<?php echo $proID; ?>"
                                                   value="<?php echo $quantity; ?>" size="1"/>
                                            <span class="input-group-btn btn-modify-group">
                                                <button type="button" class="btn btn-update"
                                                        data-toggle="tooltip"
                                                        data-placement="top" title="Cập Nhập"
                                                        id="btnUpdate_<?php echo $proID; ?>"
                                                        data-proid="<?php echo $proID; ?>">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                                <button type="button" class="btn btn-delete"
                                                        data-toggle="tooltip"
                                                        data-placement="top" title="Xóa"
                                                        id="btnDelete_<?php echo $proID; ?>"
                                                        data-proid="<?php echo $proID; ?>">
                                                    <i class="fa fa-times-circle"></i>
                                                </button>
                                            </span>
                                        </div><!--/input-group-->
                                    </td>
                                    <td class="text-right"><?php echo number_format($pro->getPrice(), 0); ?></td>
                                    <td class="text-right">
                                        <?php echo number_format($amount, 0); ?>
                                    </td>
                                </tr>
                                <?php
                                $total += $amount;

                            }
                            ?>
                            <input type="hidden" name="txtTotal" value="<?php echo $total; ?>"/>
                            </tbody>
                            <tfoot>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                    <span class="text- pull-left">Tổng hóa đơn: </span>
                                    <span class="text-right pull-right"><?php echo number_format($total, 0); ?></span>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                    <!--/Main-Shopping-cart-->
                    <div id="form-action" class="form-footer clearfix">
                        <a class="btn btn-primary pull-left" href="index.php"><i class="fa fa-mail-reply"></i> Tiếp tục
                            mua hàng</a>
                        <button type="submit" id="btnCheckout" name="btnCheckout" class="btn btn-primary pull-right">
                            <i class="fa fa-check"></i> Thanh toán
                        </button>
                    </div>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
    <!--/PRODUCTS-->

    <script type="text/javascript">
        $(document).ready(function () {

            $("input[id*='txtQuantity_']").TouchSpin({
                min: 1,
                verticalbuttons: true,
                buttondown_class: "btn btn-link",
                buttonup_class: "btn btn-link"
            });
            $('[data-toggle="tooltip"]').tooltip({
                placement: 'top'
            });
            $("button[id*='btnDelete_']").on('click', function () {
                var proId = $(this).data('proid');
                $('#txtDelProId').val(proId);
                $('#frmCart').submit();
            });
            $("#btnDeleteAll").on('click', function () {
                var arrPro = [];
                var arrQuantity = [];
                $("button[id*='btnDelete_']").each(function () {
                    var proId = $(this).data('proid');
                    arrPro.push(proId);
                });
                $('#txtDelProId').val(arrPro);
                $('#frmCart').submit();
            });
            $("button[id*='btnUpdate_']").on('click', function () {
                var proId = $(this).data('proid');
                $('#txtUpdProId').val(proId);
                var q = $('#txtQuantity_' + proId).val();
                $('#txtUpdQ').val(q);
                $('#frmCart').submit();
            });
            $("#btnUpdateAll").on('click', function () {
                var arrPro = [];
                var arrQuantity = [];
                $("button[id*='btnUpdate_']").each(function () {
                    var proId = $(this).data('proid');
                    arrPro.push(proId);
                    var q = $('#txtQuantity_' + proId).val();
                    arrQuantity.push(q);
                });
                $('#txtUpdProId').val(arrPro);
                $('#txtUpdQ').val(arrQuantity);
                $('#frmCart').submit();
            });
            var _insert = "<?php echo $insert == null ? "" : $insert ; ?>";

            if (_insert) {
                swal({
                        title: "Thành công !",
                        text: "Đơn hàng đang được xử lý! Cám ơn bạn đã đặt hàng !.",
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    },
                    function () {
                        var url = "index.php";
                        window.location.href = url;
                    }
                );
            }
        });
    </script>
    <?php
    $page->endBody();
    echo $page->render('Templates/Template.php');
}

