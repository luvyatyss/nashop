<?php

require_once '../helper/Page.php';
$page = new Page();

$page->addCSS("assets/js/bootstrap-slider-master/bootstrap-slider.css");

//$page->addJavascript("assets/js/bootstrap-slider-master/ádbootstrap-slider.js");
$page->setTitle("Trang Chủ");

$page->startBody();
?>

<?php

$page->endBody();
echo $page->render('./Teamplates/Template.php');
