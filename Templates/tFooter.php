<?php
require_once './entities/Brand.php';
$ListBrand = Brand::loadAll();
?>
<div class="container">
    <div class="row">
        <div class="container" style="overflow:hidden; padding: 0" id="SliderBrand">
            <div style="width: 100% ; overflow:hidden; background-color: #fff;" class="marquee1" id="mycrawler2">
                <?php
                foreach ($ListBrand as $ItemBra) {
                    $b = new Brand();
                    $srcImage = $ItemBra->getLogoURL();
                    $srcImage = substr($srcImage, 1, strlen($srcImage));
                    echo '<a href="productsByCat.php?BraID='. $ItemBra->getBraID(). '"><img title="" alt="" style="display: inline; vertical-align: top; " src="' . $srcImage . '"></a>';
                }
                ?>
            </div>
        </div>
        <div class="clearfix col-md-12">
            <span class="pull-right">&copy;Copyright - NA Website - 2015</span>
        </div>
    </div>
</div>
<script type="text/javascript" src="./assets/js/jquery-image-slide.js"></script>
<script type="text/javascript">
    marqueeInit({
        uniqueid: 'mycrawler2',
        style: {
            'padding': '1px',
            'width': '100%',
            'height': '62px'
        },
        inc: 5, //speed - pixel increment for each iteration of this marquee's movement
        mouse: 'cursor driven', //mouseover behavior ('pause' 'cursor driven' or false)
        moveatleast: 2,
        neutral: 150,
        savedirection: true,
        random: false
    });
</script>
