<?php



$tpl = new erLhcoreClassTemplate( 'lhuser/userlist.tpl.php');


$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','System configuration')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Users'))
)
?>