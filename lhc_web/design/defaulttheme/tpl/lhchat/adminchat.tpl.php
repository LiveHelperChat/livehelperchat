<div style="padding:4px;padding-right:20px;">
<table width="100%">
    <tr>
        <td width="1%" nowrap>
        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Actions')?></legend>
        <img class="action-image" align="absmiddle" onclick="lhinst.removeDialogTab('<?php echo $chat->id?>',$('#tabs'),true)" src="<?php echo erLhcoreClassDesign::design('images/icons/application_delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>">
        <img class="action-image" align="absmiddle" onclick="lhinst.closeActiveChatDialog('<?php echo $chat->id?>',$('#tabs'),true)" src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>">
        <img class="action-image" align="absmiddle" onclick="lhinst.deleteChat('<?php echo $chat->id?>',$('#tabs'),true)" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>">
        <img class="action-image" align="absmiddle" onclick="lhinst.transferUserDialog('<?php echo $chat->id?>','<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/user_go.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
        </fieldset>
        </td>
        <td width="68%">
        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?></legend>
        IP - <?php echo $chat->ip?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Come from')?> - <?php echo $chat->referrer != '' ? htmlspecialchars($chat->referrer) : ''?>, ID - <?php echo $chat->id;?>,
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?> - <a href="mailto:<?php echo $chat->email?>"><?php echo $chat->email?></a>
        </fieldset>
        </td>
        <td width="28%">
        <fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?></legend>
        <?php
        $user = $chat->getChatOwner();        
        if ($user !== false) :
        ?>
        <?php echo $user->name?> <?php echo $user->surname?>
        <?php endif; ?>
        </fieldset>
        </td>        
    </tr>
</table>

<br />
<table width="100%">
<tr>
    <td id="messages-<?php echo $chat->id?>">
         <div class="msgBlock" id="messagesBlock-<?php echo $chat->id?>">
         <?php $LastMessageID = 0;?>
         <?php foreach (erLhcoreClassChat::getChatMessages($chat->id) as $msg ) : ?> 
            <?php 
            $LastMessageID = $msg['id'];            
            if ($msg['user_id'] != 0) { ?>
            	<div class="message-row"><div class="msg-date"><?php echo date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?php echo htmlspecialchars($msg['name_support']);?>:</span> <?php echo htmlspecialchars($msg['msg']);?></div>
            <?php } else { ?>
                <div class="message-row response"><div class="msg-date"><?php echo date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?php echo htmlspecialchars($chat->nick)?>:</span> <?php echo htmlspecialchars($msg['msg']);?></div>
            <?php } ?>  
         <?php endforeach; ?>
         </div>
    </td>
</tr>
<tr>
    <td>
        <div>
        <textarea rows="4" style="width:100%;" class="default-input" class="default-input-ctc" name="ChatMessage" id="CSChatMessage-<?php echo $chat->id?>" ></textarea>
        <script type="text/javascript">
        jQuery('#CSChatMessage-<?php echo $chat->id?>').bind('keydown', 'return', function (evt){
            lhinst.addmsgadmin('<?php echo $chat->id?>');
        });
        </script> 
        </div>
        <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>" class="default-button" onclick="lhinst.addmsgadmin('<?php echo $chat->id?>')" />
    </td>
</tr>
</table>
</div>
<div id="transfer-dialog-<?php echo $chat->id?>"></div>




<script type="text/javascript">
lhinst.addSynchroChat('<?php echo $chat->id;?>','<?php echo $LastMessageID?>');

// Start synchronisation
lhinst.startSyncAdmin();

 
      
</script>