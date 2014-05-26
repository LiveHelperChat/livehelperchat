<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdocshare/index.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','Documents sharer')));

?>