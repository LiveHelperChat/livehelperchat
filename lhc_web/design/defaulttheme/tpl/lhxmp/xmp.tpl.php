<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_send)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP message was sent succesfuly'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

<label class="inline"><input type="checkbox" name="use_xmp" value="1" <?php isset($xmp_data['use_xmp']) && ($xmp_data['use_xmp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP active'); ?></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Host');?></label>
<input type="text" name="host" placeholder="talk.google.com" value="<?php (isset($xmp_data['host']) && $xmp_data['host'] != '') ? print $xmp_data['host'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Port');?></label>
<input type="text" name="port" value="<?php (isset($xmp_data['port']) && $xmp_data['port'] != '') ? print $xmp_data['port'] : print '5222' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Login');?></label>
<input type="text" name="username" value="<?php (isset($xmp_data['username']) && $xmp_data['username'] != '') ? print $xmp_data['username'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Password');?></label>
<input type="password" name="password" value="" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Resource');?></label>
<input type="text" name="resource" placeholder="xmpphp" value="<?php (isset($xmp_data['resource']) && $xmp_data['resource'] != '') ? print $xmp_data['resource'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Server');?></label>
<input type="text" name="server" placeholder="gmail.com" value="<?php (isset($xmp_data['server']) && $xmp_data['server'] != '') ? print $xmp_data['server'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Recipients');?></label>
<input type="text" name="recipients" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Default recipients'); ?>" value="<?php (isset($xmp_data['recipients']) && $xmp_data['recipients'] != '') ? print $xmp_data['recipients'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP Message content');?></label>
<textarea name="XMPMessage" style="height:100px;"><?php echo htmlspecialchars($xmp_data['xmp_message'])?></textarea>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<ul class="button-group round">
  <li><input type="submit" class="button small" name="StoreXMPSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /></li>
  <li><input type="submit" class="button small" name="StoreXMPSettingsTest" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Test message will be send to your account e-mail'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send test message'); ?>" /></li>
</ul>

</form>