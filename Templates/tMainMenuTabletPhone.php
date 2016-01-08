<div class="container" id="mainMenuTabletPhone">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php"><img src="assets/images/logo.png"></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">

            <li class="active">
                <a href="index.php">Trang chủ</a>
            </li>
            <?php
            require_once '../entities/Device.php';
            require_once '../entities/Category.php';
            $ListDevice = Device::loadAll();
            foreach ($ListDevice as $ItemDev) {
                ?>
                <li class="<?php echo $ItemDev->getDeviceName(); ?>">

                    <a class="cat-product-item dropdown-toggle" type="button"
                       id="dropdown<?php echo $ItemDev->getDeviceID(); ?>" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="true">
                        <?php echo $ItemDev->getDeviceName(); ?>
                    </a>

                    <div class="list-group dropdown-menu"
                         aria-labelledby="dropdown<?php echo $ItemDev->getDeviceID(); ?>">
                        <?php
                        $c = new Category();
                        $c->setDevice($ItemDev);
                        $ListCat = $c->loadLimit();
                        foreach ($ListCat as $ItemCat) {
                            ?>
                            <a href="productsByCat.php?CatID=<?php echo $ItemCat->getCatID(); ?>"
                               class="list-group-item">
                                <i class="fa fa-circle"></i><?php echo $ItemCat->getCatName(); ?>
                            </a>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>

            <li>
                <a class="cat-product-item  dropdown-toggle" type="button" id="dropdownBrand" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="true">
                    Nhà sản xuất
                </a>

                <div class="list-group dropdown-menu" aria-labelledby="dropdownBrand">
                    <?php
                    require_once '../entities/Brand.php';
                    $ListBrand = Brand::loadAll();
                    foreach ($ListBrand as $ItemBrand) {
                        ?>
                        <a href="productsByCat.php?BraID=<?php echo $ItemBrand->getBraID(); ?>" class="list-group-item">
                            <i class="fa fa-circle"></i><?php echo $ItemBrand->getBraName(); ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
            <li><a href="#">Tìm kiếm</a></li>
            <li><a href="#">Liên hệ</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
        </ul>
    </div><!-- /.navbar-collapse -->
</div><!-- /.container-->
<script>
    $(function () {
        var pgurl = window.location.href.split('/').pop();
        $("#main-header li>a").each(function () {
            if ($(this).attr("href") == pgurl) {
                $(this).parent().addClass("active");
            }
        });
    });

</script>
