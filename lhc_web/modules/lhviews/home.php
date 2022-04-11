<?php

$detect = new Mobile_Detect;

$tpl = erLhcoreClassTemplate::getInstance( 'lhviews/home.tpl.php');

if (is_numeric($Params['user_parameters']['id'])) {
    $tpl->set('default_view', (int)$Params['user_parameters']['id']);
}

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc-views.js').'"></script>';
$Result['content'] = $tpl->fetch();

?>