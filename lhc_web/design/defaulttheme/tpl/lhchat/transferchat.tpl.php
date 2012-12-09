<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged users');?></legend>
<div id="transfer-block-<?php echo $chat->id?>">
<?php foreach (erLhcoreClassChat::getOnlineUsers(array($user_id)) as $key => $user) : ?>
    <div><label><?php echo $user['name']?> <?php echo $user['surname']?> <input type="radio" name="TransferTo<?php echo $chat->id?>" value="<?php echo $user['id']?>" <?php echo $key == 0 ? 'checked="checked"' : ''?>></label></div>
<?php endforeach; ?>
<br />
<input type="button" onclick="lhinst.transferChat('<?php echo $chat->id;?>')" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
</div>
</fieldset>