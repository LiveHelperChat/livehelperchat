<?php

$tpl = erLhcoreClassTemplate::getInstance('lhform/index.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','Form')));

?>