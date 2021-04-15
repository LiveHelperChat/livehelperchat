<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchatsettings/eventindex.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')
    ),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Events tracking')));


?>