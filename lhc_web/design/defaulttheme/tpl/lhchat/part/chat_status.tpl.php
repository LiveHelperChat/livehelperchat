<div class="row">
    <div class="col-12">
        <div id="status-chat">
            <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
                <h6 class="fs12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','This chat is closed.'); ?></h6>
            <?php elseif (($user = $chat->user) !== false) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_main_pre.tpl.php')); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php')); ?>
            <?php else : ?>
                <h6 class="fs12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h6>
            <?php endif; ?>
        </div>

        <?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value == 1 && erLhcoreClassChat::canReopen($chat) ) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $chat->id?>/<?php echo $chat->hash?><?php if ( isset($chat_widget_mode) && $chat_widget_mode == true ) : ?>/(mode)/widget<?php endif; ?><?php if ( isset($chat_embed_mode) && $chat_embed_mode == true ) : ?>/(embedmode)/embed<?php endif;?>" class="btn btn-secondary" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?></a>
        <?php endif; ?>

        <?php /*if (!isset($paid_chat_params['allow_read']) || $paid_chat_params['allow_read'] == false) : ?>
    		<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && ( (isset($chat_widget_mode) && $chat_widget_mode == true && $chat->time < time()-1800)) ) : ?>
    			<input type="button" class="btn btn-secondary mb-1" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
    		<?php endif;?>
		<?php endif;*/?>

    </div>
</div>