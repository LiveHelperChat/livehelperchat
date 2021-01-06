<?php if ( isset($orderInformation['country_code']['enabled']) && $orderInformation['country_code']['enabled'] == true && !empty($chat->country_code) ) : ?>
    <div class="col-6 pb-1">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab_pre.tpl.php'));?>
        <?php if ($information_tab_map_tab_enabled == true) : ?>
            <?php if ($chat->lat != 0 && $chat->lon) : ?>
                <a class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Location on map')?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/singleaction/<?php echo $chat->id?>/map'})"><span class="material-icons">place</span></a>
            <?php endif;?>
        <?php endif;?>
        <img src="<?php echo erLhcoreClassDesign::design('images/flags')?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php echo htmlspecialchars($chat->chat_locale)?>
    </div>
<?php endif;?>