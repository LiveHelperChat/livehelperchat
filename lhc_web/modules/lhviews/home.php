<?php

$detect = new Mobile_Detect;

$tpl = erLhcoreClassTemplate::getInstance( 'lhviews/home.tpl.php');
$Result['body_class'] = 'h-100 dashboard-height';
$Result['hide_right_column'] = true;
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc-views.js').'"></script>';

$Result['content'] = $tpl->fetch();

?>