<?php
if(!isset($_SESSION))
{
    session_start();
}
if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="Neon Admin Panel" />
        <meta name="author" content="" />

        <title> NA Website | <?php echo $this->title; ?> </title>

        <link rel="stylesheet" href="assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">

        <link rel="stylesheet" href="assets/css/font-icons/entypo/css/entypo.css">
        <link rel="stylesheet" href="assets/css/font-icons/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
        <link rel="stylesheet" href="assets/css/bootstrap.css">
        <link rel="stylesheet" href="assets/css/neon-core.css">
        <link rel="stylesheet" href="assets/css/neon-theme.css">
        <link rel="stylesheet" href="assets/css/neon-forms.css">
        <link rel="stylesheet" href="assets/css/custom.css">

        <?php
        foreach ($this->stylesheets as $stylesheet) {
            echo '<link href="' . $stylesheet . '" rel="stylesheet" type="text/css" />' . "\n";
        }
        ?>
        <link rel="shortcut icon" type="image/png" href="../assets/images/favicon.png"/>
        <script src="assets/js/jquery-1.11.3.min.js"></script>



        <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->


    </head>
    <body class="page-body " >

        <div class="page-container horizontal-menu">
            <header class="navbar navbar-fixed-top"><!-- set fixed position by adding class "navbar-fixed-top" -->
                <div class="navbar-inner">

                    <!-- logo -->
                    <div class="navbar-brand" style="padding: 5px 20px;">
                        <a href="index.php">
                            <img src="../assets/images/logo.png" height="47" alt="" />
                        </a>
                    </div>

                    <!-- main menu -->

                    <ul class="navbar-nav" id="main-menu">
                        <?php include './Teamplates/tMainMenu.php'; ?>
                        <!-- Search Bar -->
                        <li id="search" class="search-input-collapsed">
                            <?php include './Teamplates/tSearch.php'; ?>
                        </li>
                    </ul>


                    <!-- notifications and other links -->
                    <ul class="nav navbar-right pull-right">			
                        <?php include './Teamplates/tRawLinks.php'; ?>
                    </ul>

                </div>

            </header>
            <!--Main-content-->		
            <div id="main-content">
                <!--Content-->
                <div class="container">
                    <div class="row">
                        <?php echo $this->body; ?>
                    </div>
                </div>
                <!/--Content-->
            </div>
            <!--/Main-content-->		
            <footer class="main">
                <?php include './Teamplates/tFooter.php'; ?>
            </footer>
        </div>


        <!-- Bottom Scripts -->
        <script src="assets/js/gsap/main-gsap.js"></script>
        <script src="assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/joinable.js"></script>
        <script src="assets/js/resizeable.js"></script>
        <script src="assets/js/neon-api.js"></script>

        <script src="assets/js/neon-custom.js"></script>
        <script src="assets/js/neon-demo.js"></script>
        <script src="assets/js/custom.js"></script>
        <?php
        foreach ($this->javascripts as $javascript) {
            echo '<script src="' . $javascript . '"></script>' . "\n";
        }
        ?>
        <script type="text/javascript">

            $(function () {

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
            });

        </script>
    </body>
</html>
