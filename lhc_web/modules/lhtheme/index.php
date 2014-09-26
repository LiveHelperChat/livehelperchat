<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/index.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')));

?>