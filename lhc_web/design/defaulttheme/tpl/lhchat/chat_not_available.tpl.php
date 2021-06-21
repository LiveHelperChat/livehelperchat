<?php

$themeOffline = '';

if (is_numeric($theme)) {
    $themeObj = erLhAbstractModelWidgetTheme::fetch($theme);
    if ($themeObj instanceof erLhAbstractModelWidgetTheme) {
        $themeObj->translate();
        if (isset($themeObj->bot_configuration_array['chat_unavailable']) && $themeObj->bot_configuration_array['chat_unavailable'] != '') {
            $themeOffline = $themeObj->bot_configuration_array['chat_unavailable'];
        }
    }
} elseif (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme){
    $theme->translate();
    if (isset($theme->bot_configuration_array['chat_unavailable']) && $theme->bot_configuration_array['chat_unavailable'] != '') {
        $themeOffline = $theme->bot_configuration_array['chat_unavailable'];
    }
}

?>

<?php if ($themeOffline == '') : ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat is currently unavailable');?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please try again later.');?></p>
<?php else : ?>
    <?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($themeOffline)); ?>
<?php endif;?>