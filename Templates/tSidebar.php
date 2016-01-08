<!--CATALOGUE PRODUCT-->
<div class="cat-product box">
    <div class="box-top text-center">
        <a>Danh mục sản phẩm</a>
    </div>
    <?php
    require_once '../entities/Device.php';
    require_once '../entities/Category.php';
    $ListDevice = Device::loadAll();
    foreach ($ListDevice as $ItemDev) {
        ?>
        <div class="<?php echo $ItemDev->getDeviceName(); ?>">
            <a class="cat-product-item" href="productsByCat.php?DeviceID=<?php echo $ItemDev->getDeviceID(); ?>">
                <?php echo $ItemDev->getDeviceName(); ?>
            </a>

            <div class="list-group">
                <?php
                $c = new Category();
                $c->setDevice($ItemDev);
                $ListCat = $c->loadLimit();
                foreach ($ListCat as $ItemCat) {
                    ?>
                    <a href="productsByCat.php?CatID=<?php echo $ItemCat->getCatID(); ?>" class="list-group-item">
                        <i class="fa fa-circle"></i><?php echo $ItemCat->getCatName(); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
<!--/CATALOGUE PRODUCT-->
<!--CATALOGUE Manufacturer-->
<div class="cat-manufacturer box">
    <div class="box-top text-center">
        <a>Danh mục nhà sản xuất</a>
    </div>
    <div class="list-group">
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
</div>
<!--/CATALOGUE Manufacturer-->
<script>
    $(function () {
        var pgurl = window.location.href.split('/').pop();
        $("#sidebar a").each(function () {
            if ($(this).attr("href") == pgurl) {
                $(this).addClass("active");
            }
        });
    });
</script>