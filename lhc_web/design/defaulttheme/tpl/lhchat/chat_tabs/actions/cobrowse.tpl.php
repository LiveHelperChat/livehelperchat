<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/cobrowse_pre.tpl.php'));?>	
<?php if ($chat_chat_tabs_actions_cobrowse_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhcobrowse', 'browse')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" href="#" onclick="return lhinst.startCoBrowse('<?php echo $chat->id?>')">
        <span class="material-icons">visibility</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screen sharing')?>
    </a>
</div>
<?php endif;?>