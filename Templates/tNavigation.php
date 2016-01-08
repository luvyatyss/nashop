<div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php" ">
        <img src="assets/images/logo.png">
        </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li>
                <a href="index.php">Trang chủ</a>
            </li>
            <?php
            require_once '../entities/Device.php';
            require_once '../entities/Category.php';
            $ListDevice = Device::loadAll();
            foreach ($ListDevice as $ItemDev) {
                ?>
                <li class="<?php echo $ItemDev->getDeviceName(); ?>">

                    <a href="productsByCat.php?DeviceID=<?php echo $ItemDev->getDeviceID(); ?>">
                        <?php echo $ItemDev->getDeviceName(); ?>
                    </a>
                </li>
            <?php } ?>
            <li><a href="#">Liên hệ</a></li>
            <li><a href="search.php">Tìm kiếm</a></li>
            <!--
            <li id="search" class="root-level search-input-collapsed">
                <form method="get" action="search.php">
                    <input name="q" class="search-input" placeholder="Từ khóa..." type="text">
                    <button type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </li>
            -->

        </ul>
        <ul class="nav navbar-nav navbar-right">
        </ul>
    </div><!-- /.navbar-collapse -->
</div><!-- /.container-->

<script>

    $(function () {
        /*       var  search				  = $('li#search'),
         search_input		  = search.find('.search-input'),
         search_submit		  = search.find('form');
         if(search.hasClass('search-input-collapsed'))
         {
         search_submit.submit(function(ev)
         {
         if(search.hasClass('search-input-collapsed'))
         {
         ev.preventDefault();
         search.removeClass('search-input-collapsed');
         search_input.focus();
         return false;
         }
         });
         search_input.on('blur', function(ev)
         {
         search.addClass('search-input-collapsed');
         });
         }*/
        var pgurl = window.location.href.split('/').pop();
        $("#main-header li>a").each(function () {
            if ($(this).attr("href") == pgurl) {
                $(this).parent().addClass("active");
            }
        });
    });

</script>