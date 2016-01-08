<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}

if (!isset($_SESSION["Cart"])) {
    $_SESSION["Cart"] = array();
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/layout.css">
    <link rel="stylesheet" type="text/css" href="assets/css/stylesheet.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto|Oswald|Open+Sans+Condensed:300&subset=latin,vietnamese'
          rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" type="image/png" href="assets/images/favicon.png"/>
    <title> NA Website | <?php echo $this->title; ?> </title>
    <?php
    foreach ($this->stylesheets as $stylesheet) {
        echo '<link href="' . $stylesheet . '" rel="stylesheet" type="text/css" />' . "\n";
    }
    ?>
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <script src="assets/js/jquery.validate.min.js"></script>
</head>
<body>
<div id="page">
    <header>
        <div id="top-header">
            <?php include './Templates/tTopHeader.php'; ?>
        </div>
        <!--Main-Header-->
        <nav id="main-header" class="navbar navbar-default">

        </nav>
        <!--/Main-Header-->
    </header>

    <!--CONTENT-->
    <div id="content">
        <div class="container">
            <div class="row">
                <!--SIDE-BAR-->
                <aside class="col-md-3" id="sidebar">

                </aside>
                <!--/SIDE-BAR-->
                <!--MAIN-CONTENT-->
                <div id="main-content" class="col-md-9 col-lg-9">
                    <div class="row">
                        <?php echo $this->body; ?>
                    </div>
                </div>
                <!--/MAIN-CONTENT-->
            </div>
        </div>
    </div>
    <!--/CONTENT-->

    <!--FOOTER-->
    <a href="#" class="scrollToTop">Top</a>
    <footer>
        <?php include './Templates/tFooter.php'; ?>
    </footer>
    <!--/FOOTER-->
</div>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/scrollToTop.js"></script>
<?php
foreach ($this->javascripts as $javascript) {
    echo '<script src="' . $javascript . '" ></script>' . "\n";
}
?>
<script>
    $(function () {

        var $window = $(window).on('resize', function () {
            if ($(this).width() <= 1010) {
                $('#main-header').load('./Templates/tMainMenuTabletPhone.php');
                $('#sidebar').text("");
            }
            else {
                $('#main-header').load('./Templates/tNavigation.php');
                $('#sidebar').load('./Templates/tSidebar.php');
            }
        }).trigger('resize'); //on page load


        function reposition() {
            var modal = $(this),
                dialog = modal.find('.modal-dialog');
            modal.css('display', 'block');

            // Dividing by two centers the modal exactly, but dividing by three
            // or four works better for larger screens.
            dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
        }

        // Reposition when a modal is shown
        $('.modal').on('show.bs.modal', reposition);
        // Reposition when the window is resized
        $(window).on('resize', function () {
            $('.modal:visible').each(reposition);
        });
//end position Modal


    });
</script>
</body>
</html>