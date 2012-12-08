<?php


$tpl = new erLhcoreClassTemplate( 'lhdepartament/departaments.tpl.php');


$Result['content'] = $tpl->fetch();

$Result['path'] = array(

array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),

array('url' => erLhcoreClassDesign::baseurl('department/departments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')))

?>