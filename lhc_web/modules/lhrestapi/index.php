<?php

$tpl = erLhcoreClassTemplate::getInstance('lhrestapi/index.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/index','Rest API')));

?>