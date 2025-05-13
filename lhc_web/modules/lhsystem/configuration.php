<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/configuration.tpl.php');


$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')));
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/system.configuration.js').'"></script>';

?>