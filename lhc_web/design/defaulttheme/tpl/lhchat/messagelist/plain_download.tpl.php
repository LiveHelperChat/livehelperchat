<?php
echo "===============================================================================\n";
echo "                    ",erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','Chat ID')," ",$chat->id,' ',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/plain','at'),' ', date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);
echo "\n===============================================================================\n";
$remove_whisper = true;
$hideMetaRaw = true;
?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/messagelist/plain.tpl.php'));?>