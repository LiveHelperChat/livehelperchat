<div style="padding:4px;padding-right:4px;">
<h2 id="status-chat"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending confirm')?></h2>
<table width="100%">
<tr>
    <td id="messages">
        <div class="msgBlock" id="messagesBlock"><? foreach (erLhcoreClassChat::getChatMessages($chat_id) as $msg ) : ?>         
            <? if ($msg['user_id'] == 0) { ?>
            	<div class="message-row"><div class="msg-date"><?=date('Y-m-d H:i',$msg['time']);?></div><span class="usr-tit"><?=$chat->nick;?>:</span> <?=htmlspecialchars($msg['msg']);?></div>
            <? } else { ?>
                <div class="message-row response"><div class="msg-date"><?=date('Y-m-d H:i',$msg['time']);?></div><span class="usr-tit"><?=$msg['name_support']?>:</span> <?=htmlspecialchars($msg['msg']);?></div>
            <? } ?>  
         <? endforeach; ?></div>
    </td>
</tr>
<tr>
    <td>
        <div>
            <textarea rows="4" style="width:100%;" class="default-input"  name="ChatMessage" id="CSChatMessage" ></textarea>
            <script type="text/javascript">
            jQuery('#CSChatMessage').bind('keydown', 'return', function (evt){
                lhinst.addmsguser();
            });
            </script> 
        </div>
        
        <input type="button" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Send')?>" onclick="lhinst.addmsguser()" />
    </td>
</tr>
</table>
</div>
<script type="text/javascript">
    lhinst.setChatID('<?=$chat_id?>');
    lhinst.setChatHash('<?=$hash?>');
    
    // Start user chat synchronization
    lhinst.chatsyncuserpending();
    lhinst.syncusercall();
    
    $(window).bind('unload', function(){        
        lhinst.userclosedchat();
    }); 
    
</script>