<?php
require_once './helper/Page.php';
$page = new Page();
$page->setTitle('Trang Chủ');
$page->addCSS("assets/css/details.css");
$page->addCSS("assets/js/jquery.rondell/jquery.rondell.min.css");

$page->addJavascript("assets/js/jquery.waterwheelCarousel.min.js");
$page->addJavascript("assets/js/jquery.elevatezoom.js");

$page->startBody();
$p = null;
$c = null;
if (isset($_GET["ProID"]) && !empty($_GET["ProID"])) {
    require_once "entities/Product.php";
    require_once "entities/Category.php";
    $p = Product::loadProductByProID($_GET["ProID"]);
    if (!$p == null) {
        $p->setView($p->getView() + 1);
        $p->updateView();
        $c = Category::getCat($p->getCatPro()->getCatID());
    }
}
if ($p == null) {
    require_once './helper/Utils.php';
    $_SESSION['showModalLogin'] = true;
    $url = "index.php";
    Utils::Redirect($url);
}
?>

    <div class="box clearfix">
        <ol class="breadcrumb box-top">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <?php
            echo '<li><a href="productsByCat.php?DeviceID=' . $c->getDevice()->getDeviceID() . '">' . $c->getDevice()->getDeviceName() . '</a></li>';
            echo '<li><a href="productsByCat.php?CatID=' . $c->getCatID() . '">' . $c->getCatName() . '</a></li>';
            echo '<li class="active">' . $p->getProName() . '</li>';
            ?>
        </ol>
        <form id="addToCart-form" method="post" action="">
            <input type="hidden" id="txtProID" name="txtProID"/>
            <input type="hidden" id="txtOrderQ" name="txtOrderQ"/>
        </form>
        <div id="product-detail">
            <!--PRODUCT DETAIL-->
            <div class="product-block clearfix">
                <div class="col-md-6 image">
                    <div id="image_main">
                        <?php
                        $srcImage = $p->getImageURL();
                        $srcImage = substr($srcImage, 1, strlen($srcImage));
                        // echo '<a class="img" href = "details.php?ProID=' . $p->getProID() . '">';
                        echo '<img id="zoom_01" src ="' . $srcImage . '" alt = "' . $p->getProName() . '" class="img-responsive" data-zoom-image="' . $srcImage . '" ></a >';
                        ?>
                    </div>

                    <div id="gallery_img">
                        <?php
                        $path = "./assets/images/productImages/" . $p->getProID();
                        echo '<a href="#" data-image="' . $srcImage . '" data-zoom-image="' . $srcImage . '">'
                            . '<img id="zoom_01" src="' . $srcImage . '" class="img-thumbnail" style="width: 72px; height:72px" />'
                            . '</a>';
                        foreach (glob("{$path}/*") as $file) {
                            if (is_dir($file) || $file == $srcImage) {
                                continue;
                            }
                            echo '<a href="#" data-image="' . $file . '" data-zoom-image="' . $file . '">'
                                . '<img id="zoom_01" src="' . $file . '" class="img-thumbnail" style="width: 72px; height:72px" />'
                                . '</a>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-6 product-meta">
                    <div class="name">
                        <?php echo $p->getProName(); ?>
                    </div>
                    <div class="price">
                        <div class="price-new">
                            <?php echo number_format($p->getPrice(), 0) . "đ"; ?>
                        </div>
                        <div class="price-old">
                            <?php echo number_format($p->getPrice(), 0) . "đ"; ?>
                        </div>
                    </div>
                    <ul>
                        <li class="product-code">
                            <label><i class="fa fa-check"></i>Mã Sản Phẩm :</label>
                            <span><?php echo $p->getProID(); ?></span>
                        </li>
                        <li class="category">
                            <label><i class="fa fa-check"></i>Loại Sản Phẩm :</label>
                            <span><a
                                    href="productsByCat.php?CatID=<?php echo $c->getCatID(); ?>"><?php echo $c->getCatName(); ?></a></span>
                        </li>
                        <li class="device">
                            <label><i class="fa fa-check"></i>Loại Thiết Bị :</label>
                            <span><a
                                    href="productsByCat.php?DeviceID=<?php echo $c->getDevice()->getDeviceID(); ?>"><?php echo $c->getDevice()->getDeviceName(); ?></a></span>
                        </li>
                        <li class="brand">
                            <label><i class="fa fa-check"></i>Nhà Sản Xuất :</label>
                            <span><a
                                    href="productsByCat.php?BraID=<?php echo $c->getBrand()->getBraID(); ?>"><?php echo $c->getBrand()->getBraName(); ?></a></span>
                        </li>
                        <li class="view">
                            <label><i class="fa fa-check"></i>Số Lượt Xem :</label>
                            <span><?php echo number_format($p->getView(), 0); ?></span>
                        </li>
                        <li class="status">
                            <label><i class="fa fa-check"></i>Tình trạng :</label>
                            <span>Còn hàng</span>
                        </li>
                    </ul>
                    <div class="order-quantity">
                        <label for="txtOrderQuantity">Số Lượng :</label>
                        <input id="txtOrderQuantity" class="form-control" name="txtOrderQuantity" value="1">
                        <ul class="btn">
                            <li>
                                <button id="plus" class="btn btn-primary">+</button>
                            </li>
                            <li>
                                <button id="minus" class="btn btn-primary">-</button>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom">
                        <div class="cart">
                            <span class="icon-cart"></span>
                            <button class="btn btn-shopping-cart" data-proid="<?php echo $p->getProID() ?>">
                                <span>Thêm vào giỏ</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-description">
                <div class="box-top">
                    <p>Thông tin chi tiết</p>
                </div>
                <div class="col-md-12 description" id="pro_description">
                    <?php echo $p->getFullDes(); ?>
                </div>
            </div>
            <!--/PRODUCT DETAIL-->
        </div>
    </div>

    <!--CUNG LOAI SAN PHAM-->
    <div class="latest box clearfix">
        <div class="box-top">
            <p>Cùng loại sản phẩm</p>
        </div>
        <?php
        $p_Cat = new Product();
        $p_Cat->setCatPro($p->getCatPro());
        $ListPro_Cat = $p_Cat->loadLimit(6);
        Product::printListProduct($ListPro_Cat);
        ?>
    </div>
    <!--/CUNG LOAI SAN PHAM-->

    <!--CUNG NHA SAN XUAT-->
    <div class="latest box clearfix">
        <div class="box-top">
            <p>Cùng nhà sản xuất</p>
        </div>
        <?php
        $p_Bra = new Product();
        $c_Bra = new Category();
        $c_Bra->setBrand($c->getBrand());
        $p_Bra->setCatPro($c_Bra);
        $ListPro_Bra = $p_Bra->loadLimit(6);
        Product::printListProduct($ListPro_Bra);
        ?>
    </div>
    <!--/CUNG NHA SAN XUAT-->
    <script type="text/javascript">
        $(document).ready(function () {

            $('#product-detail button.btn-shopping-cart').on('click', function () {
                var proID = $(this).data('proid');
                $('#txtProID').val(proID);
                var orderQuantity = $('#txtOrderQuantity').val();
                $('#txtOrderQ').val(orderQuantity);
                $('#addToCart-form').submit();
            });
            $('button.btn-shopping-cart').on('click', function () {
                var proID = $(this).data('proid');
                $('#txtProID').val(proID);
                $('#addToCart-form').submit();
            });
            $('#txtOrderQuantity').keyup(function () {
                var value = $(this).val();
                if (isNaN(value)) {
                    $(this).val(1);
                }
            });
            $("#plus").click(function () {
                var orderQuantity = $('#txtOrderQuantity').val();
                $('#txtOrderQuantity').val(parseInt(orderQuantity) + 1);
            });
            $("#minus").click(function () {
                var orderQuantity = $('#txtOrderQuantity').val();
                if (orderQuantity <= 1) {
                    $('#txtOrderQuantity').val(1);
                    return;
                }
                $('#txtOrderQuantity').val(parseInt(orderQuantity) - 1);
            });
            $("#zoom_01").elevateZoom({
                gallery: 'gallery_img',
                cursor: 'pointer',
                galleryActiveClass: 'active',
                zoomType: "lens",
                lensShape: "round",
                lensSize: 200
            });

            $("#zoom_01").bind("click", function (e) {
                var ez = $('#zoom_01').data('elevateZoom');
                $.fancybox(ez.getGalleryList());
                return false;
            });

            $("#gallery_img").waterwheelCarousel({
                horizon : 40,
                separation: 50,
                edgeFadeEnabled: true,
                activeClassName : 'carousel-center'
            });

            // Style Description
            var ul = $('#pro_description ul');
            ul.css({
                "width": "100%",
                "padding": "20px 10px",
                "margin": "0",
                "font-family": "'Roboto', sans-serif",
                "background:": "none"
            });
            var li = $("#pro_description ul>li");
            li.each(function () {
                var index = $(this).index();
                if (index > 4){
                    $(this).addClass("hidden");
                }
                if (index === 4){
                    $(this).after("<li class='text-center'><hutton id='btnShowDes' class='show btn btn-primary description' >Xem</button></li>");
                }
                if (index ===  li.length ){
                    $(this).after("<li class='text-center'><hutton id='btnHideDes' class='hidden btn btn-primary description'>Ẩn</button></li>");
                }
                $(this).css({
                    "width": "100%",
                    "background": "none",
                    "padding": "0",
                    "margin": "0",
                    "border-bottom": "1px solid #DADADA",
                    "display": "table"
                });
                $(this).children().css({
                    "padding": "10px",
                    "display": "table-cell",
                    "vertical-align": "top",
                    "font-size": "13px",
                    "color": "rgb(102, 102, 102)"
                });
                $(this).find("span").each(function () {
                    $(this).css({
                        "width": "30%",
                        "font-weight": "normal",
                        "color": "#000"
                    });
                });
                $(this).find("label").each(function () {
                    $(this).css({
                        "margin": "0px",
                        "padding": "10px",
                        "font-weight": "normal",
                        "font-stretch": "normal",
                        "font-size": "16px",
                        "color": "rgb(249, 89, 89)",
                        "outline": "none",
                        "display": "block",
                        "background": "none"
                    });
                    $(this).parent().css({
                        "-webkit-box-shadow": "0px 3px 2px 1px rgba(0,0,0,0.65)",
                        "-moz-box-shadow": "0px 3px 2px 1px rgba(0,0,0,0.65)",
                        "box-shadow": "0px 3px 2px 1px rgba(0,0,0,0.65)",
                        "margin" : "10px 0",
                        "border" : "1px solid #DADADA",
                    });
                    $(this).parent().prev().css({
                       "border" : "none"
                    });
                });
            });
            $('.btn.description').click(function(){
                $('.btn.description').toggleClass("hidden show");
                li.each(function(){
                    if ($(this).index() > 4){
                        $(this).toggleClass("hidden show");
                    }
                });
            });

        });
    </script>
<?php
$page->endBody();
echo $page->render('Templates/Template.php');
