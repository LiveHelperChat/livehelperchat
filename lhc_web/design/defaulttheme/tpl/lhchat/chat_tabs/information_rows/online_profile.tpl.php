<?php if (($online_user = $chat->online_user) !== false) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_pre.tpl.php'));?>
    <?php if ($information_tab_online_user_info_enabled == true) : ?>
        <div class="col-6 pb-1">
            <a class="text-muted" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/'+<?php echo $online_user->id?>})"><span class="material-icons">info_outline</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Online profile')?></a>
        </div>
        <div class="col-6 pb-1">
            <a class="text-muted" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/getonlineuserinfo/<?php echo $online_user->id?>/(tab)/chats'})"><span class="material-icons">info_outline</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Previous chats')?></a>
        </div>
    <?php endif;?>
<?php endif; ?>