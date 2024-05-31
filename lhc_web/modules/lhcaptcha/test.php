<?php

//erTranslationClassLhTranslation::$htmlEscape = false;

echo 'START_TAG' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Thumbs down') . 'END',
 'TAG_START' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Thumbs up') . 'END';

$tpl = new erLhcoreClassTemplate( 'lhcaptcha/test.tpl.php');
echo $tpl->fetch();

exit;

?>