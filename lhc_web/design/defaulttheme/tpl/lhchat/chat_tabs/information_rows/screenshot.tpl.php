<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_pre.tpl.php')); ?>
<?php if ($operator_screenshot_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','take_screenshot')) : ?>
    <div class="col-6 pb-1">
        <a class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screenshot')?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/singleaction/<?php echo $chat->id?>/screenshot'})"><span class="material-icons">photo_camera</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screenshot')?></a>
    </div>
<?php endif;?>