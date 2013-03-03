<div class="row status-row">
    <div class="columns three">
            <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Actions')?></h5>
            <p>
            <img class="action-image" align="absmiddle" onclick="lhinst.removeDialogTab('<?php echo $chat->id?>',$('#tabs'),true)" src="<?php echo erLhcoreClassDesign::design('images/icons/application_delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>">
            <img class="action-image" align="absmiddle" onclick="lhinst.closeActiveChatDialog('<?php echo $chat->id?>',$('#tabs'),true)" src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>">
            <img class="action-image" align="absmiddle" onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>">
            <img class="action-image" align="absmiddle" onclick="lhinst.transferUserDialog('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/user_go.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
            <img class="action-image" align="absmiddle" onclick="lhinst.blockUser('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure?')?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/user_delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Block user')?>">
            </p>       
          
    </div>
    <div class="columns six">
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?></h5>
    <p>
    <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" /><?php endif; ?> | IP - <?php echo $chat->ip?> | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Come from')?> - <?php echo $chat->referrer != '' ? htmlspecialchars($chat->referrer) : ''?> | ID - <?php echo $chat->id;?> | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?> - <a href="mailto:<?php echo $chat->email?>"><?php echo $chat->email?></a>
    </p>
    </div>
    
    <div class="columns three">
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?></h5>
    <p>
            <?php
            $user = $chat->getChatOwner();        
            if ($user !== false) :
            ?>
            <?php echo htmlspecialchars($user->name)?> <?php echo htmlspecialchars($user->surname)?>
            <?php endif; ?></p>
            </fieldset>
    </div>
</div>
  


<div class="msgBlock" id="messagesBlock-<?php echo $chat->id?>">
<?php $LastMessageID = 0;?>
<?php foreach (erLhcoreClassChat::getChatMessages($chat->id) as $msg ) : ?> 
<?php 
$LastMessageID = $msg['id'];            
if ($msg['user_id'] != 0) { ?>
	<div class="message-row"><div class="msg-date"><?php echo date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?php echo htmlspecialchars($msg['name_support']);?>:&nbsp;</span><?php echo htmlspecialchars($msg['msg']);?></div>
<?php } else { ?>
    <div class="message-row response"><div class="msg-date"><?php echo date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?php echo htmlspecialchars($chat->nick)?>:&nbsp;</span><?php echo htmlspecialchars($msg['msg']);?></div>
<?php } ?>  
<?php endforeach; ?>
</div>
<br />

     
<textarea rows="4" name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>" ></textarea>
<script type="text/javascript">
jQuery('#CSChatMessage-<?php echo $chat->id?>').bind('keydown', 'return', function (evt){
    lhinst.addmsgadmin('<?php echo $chat->id?>');
});
</script>  

<div class="row">
    <div class="columns four"><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>" class="small button round" onclick="lhinst.addmsgadmin('<?php echo $chat->id?>')" /></div>
    <div class="columns eight">
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'CannedMessage',
                    'on_change'      => "$('#CSChatMessage-".$chat->id."').text(($(this).val() > 0 ? $(this).find(':selected').text() : ''))",
                    'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select canned message'),
                    'display_name'   => 'msg',
                    'list_function'  => 'erLhcoreClassModelCannedMsg::getList'
            )); ?>
    </div>
</div>

<script type="text/javascript">
lhinst.addSynchroChat('<?php echo $chat->id;?>','<?php echo $LastMessageID?>');

// Start synchronisation
lhinst.startSyncAdmin();    
</script>