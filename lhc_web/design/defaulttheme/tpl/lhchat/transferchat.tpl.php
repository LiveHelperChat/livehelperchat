<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged users');?></legend>
<div id="transfer-block-<?=$chat->id?>">
<? foreach (erLhcoreClassChat::getOnlineUsers(array($user_id)) as $key => $user) : ?>
    <div><label><?=$user['name']?> <?=$user['surname']?> <input type="radio" name="TransferTo<?=$chat->id?>" value="<?=$user['id']?>" <?=$key == 0 ? 'checked="checked"' : ''?>></label></div>
<? endforeach; ?>
<br />
<input type="button" onclick="lhinst.transferChat('<?=$chat->id;?>')" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
</div>
</fieldset>