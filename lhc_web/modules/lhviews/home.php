<?php

$detect = new Mobile_Detect;

$tpl = erLhcoreClassTemplate::getInstance( 'lhviews/home.tpl.php');
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc-views.js').'"></script>';
$Result['content'] = $tpl->fetch();

?>