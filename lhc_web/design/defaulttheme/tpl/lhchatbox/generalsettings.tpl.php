<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chatbox configuration');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<label class="inline"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Auto creation by chat enabled'); ?> <input type="checkbox" name="AutoCreation" value="1" <?php isset($chatbox_data['chatbox_auto_enabled']) && ($chatbox_data['chatbox_auto_enabled'] == '1') ? print 'checked="checked"' : '' ?> /></label>

<label>Secret hash, used then auto creation is disabled</label>
<input type="text" name="SecretHash" value="<?php (isset($chatbox_data['chatbox_secret_hash']) && $chatbox_data['chatbox_secret_hash'] != '') ? print $chatbox_data['chatbox_secret_hash'] : print erLhcoreClassChat::generateHash() ?>" />

<input type="submit" class="button small round" name="StoreChatboxSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Save'); ?>" />

</form>