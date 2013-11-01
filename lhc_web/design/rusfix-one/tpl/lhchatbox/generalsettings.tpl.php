<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Chatbox settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chatbox/generalsettings')?>" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Default new chatbox name');?></label>
<input type="text" name="DefaultName" value="<?php (isset($chatbox_data['chatbox_default_name']) && $chatbox_data['chatbox_default_name'] != '') ? print $chatbox_data['chatbox_default_name'] : print 'Chatbox' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Default operator name');?></label>
<input type="text" name="DefaultOperatorName" value="<?php (isset($chatbox_data['chatbox_default_opname']) && $chatbox_data['chatbox_default_opname'] != '') ? print $chatbox_data['chatbox_default_opname'] : print 'Manager' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Messages limit in the chatbox');?></label>
<input type="text" name="MessagesLimit" value="<?php (isset($chatbox_data['chatbox_msg_limit']) && $chatbox_data['chatbox_msg_limit'] != '') ? print $chatbox_data['chatbox_msg_limit'] : print 100 ?>" />

<label class="inline"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Auto creation by chatbox identifier is enabled'); ?> <input type="checkbox" name="AutoCreation" value="1" <?php isset($chatbox_data['chatbox_auto_enabled']) && ($chatbox_data['chatbox_auto_enabled'] == '1') ? print 'checked="checked"' : '' ?> /></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Secret hash, this is used when auto creation is disabled');?>, <a href="http://livehelperchat.com/documentation-6c.html"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','more information on how to use it');?></a></label>
<input type="text" name="SecretHash" value="<?php (isset($chatbox_data['chatbox_secret_hash']) && $chatbox_data['chatbox_secret_hash'] != '') ? print $chatbox_data['chatbox_secret_hash'] : print erLhcoreClassChat::generateHash() ?>" />

<input type="submit" class="button small round" name="StoreChatboxSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>