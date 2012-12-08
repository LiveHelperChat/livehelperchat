<div style="padding:4px;padding-right:20px;">
<table width="100%">
    <tr>
        <td width="1%" nowrap>
        <fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Actions')?></legend>
        <img class="action-image" align="absmiddle" onclick="lhinst.removeDialogTab('<?=$chat->id?>',$('#tabs'),true)" src="<?=erLhcoreClassDesign::design('images/icons/application_delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close dialog')?>">
        <img class="action-image" align="absmiddle" onclick="lhinst.closeActiveChatDialog('<?=$chat->id?>',$('#tabs'),true)" src="<?=erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Close chat')?>">
        <img class="action-image" align="absmiddle" onclick="lhinst.deleteChat('<?=$chat->id?>',$('#tabs'),true)" src="<?=erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Delete chat')?>">
        <img class="action-image" align="absmiddle" onclick="lhinst.transferUserDialog('<?=$chat->id?>','<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>')" src="<?=erLhcoreClassDesign::design('images/icons/user_go.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transfer chat')?>">
        </fieldset>
        </td>
        <td width="68%">
        <fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?></legend>
        IP - <?=$chat->ip?>, <?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Come from')?> - <?=$chat->referrer != '' ? htmlspecialchars($chat->referrer) : ''?>, ID - <?=$chat->id;?>,
        <?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','E-mail')?> - <a href="mailto:<?=$chat->email?>"><?=$chat->email?></a>
        </fieldset>
        </td>
        <td width="28%">
        <fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?></legend>
        <?
        $user = $chat->getChatOwner();        
        if ($user !== false) :
        ?>
        <?=$user->name?> <?=$user->surname?>
        <? endif; ?>
        </fieldset>
        </td>        
    </tr>
</table>

<br />
<table width="100%">
<tr>
    <td id="messages-<?=$chat->id?>">
         <div class="msgBlock" id="messagesBlock-<?=$chat->id?>">
         <? $LastMessageID = 0;?>
         <? foreach (erLhcoreClassChat::getChatMessages($chat->id) as $msg ) : ?> 
            <? 
            $LastMessageID = $msg['id'];            
            if ($msg['user_id'] != 0) { ?>
            	<div class="message-row"><div class="msg-date"><?=date('Y-m-d H:i',$msg['time']);?></div><span class="usr-tit"><?=htmlspecialchars($msg['name_support']);?>:</span> <?=htmlspecialchars($msg['msg']);?></div>
            <? } else { ?>
                <div class="message-row response"><div class="msg-date"><?=date('Y-m-d H:i',$msg['time']);?></div><span class="usr-tit"><?=htmlspecialchars($chat->nick)?>:</span> <?=htmlspecialchars($msg['msg']);?></div>
            <? } ?>  
         <? endforeach; ?>
         </div>
    </td>
</tr>
<tr>
    <td>
        <div>
        <textarea rows="4" style="width:100%;" class="default-input" class="default-input-ctc" name="ChatMessage" id="CSChatMessage-<?=$chat->id?>" ></textarea>
        <script type="text/javascript">
        jQuery('#CSChatMessage-<?=$chat->id?>').bind('keydown', 'return', function (evt){
            lhinst.addmsgadmin('<?=$chat->id?>');
        });
        </script> 
        </div>
        <input type="button" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send')?>" class="default-button" onclick="lhinst.addmsgadmin('<?=$chat->id?>')" />
    </td>
</tr>
</table>
</div>
<div id="transfer-dialog-<?=$chat->id?>"></div>




<script type="text/javascript">
lhinst.addSynchroChat('<?=$chat->id;?>','<?=$LastMessageID?>');

// Start synchronisation
lhinst.startSyncAdmin();

 
      
</script>