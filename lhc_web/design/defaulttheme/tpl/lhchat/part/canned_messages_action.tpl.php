<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_action_pre.tpl.php')); ?>
<?php if ($chat_part_canned_messages_action_enabled == true) : ?>
<div class="row" id="canned-messages-chat-container-<?php echo $chat->id?>">
	<?php /*<div class="col-7">
        <select class="form-control form-control-sm" name="CannedMessage-<?php echo $chat->id?>" id="id_CannedMessage-<?php echo $chat->id?>">
        	<?php $canned_options = erLhcoreClassModelCannedMsg::groupItems(erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id, erLhcoreClassUser::instance()->getUserID()), $chat, erLhcoreClassUser::instance()->getUserData(true)); ?>
        	<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_options.tpl.php')); ?>
        </select>
    </div>
	<div class="col-3">
        <input type="text" class="form-control form-control-sm" id="id_CannedMessageSearch-<?php echo $chat->id?>" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Type to search')?>"/>
	</div>
	<div class="col-2 sub-action-chat" id="sub-action-chat-<?php echo $chat->id?>">
        <div class="row d-flex">
            <div class="col pl-0 pr-2">
                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Fill textarea with canned message')?>" href="#" onclick="$('#CSChatMessage-<?php echo $chat->id?>').val(($('#id_CannedMessage-<?php echo $chat->id?>').val() > 0) ? $('#id_CannedMessage-<?php echo $chat->id?>').find(':selected').attr('data-msg') : '');return false;" class="btn btn-secondary w-100 btn-sm fill-editor-canned"><i class="material-icons mr-0">mode_edit</i></a>
            </div>
            <div class="col pl-0 pr-2">
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/send_delayed_canned_action.tpl.php')); ?>
            </div>
        </div>
	</div>*/ ?>
</div>
<?php endif;?>