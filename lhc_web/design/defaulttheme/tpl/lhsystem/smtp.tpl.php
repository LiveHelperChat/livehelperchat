<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','SMTP settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

<label class="inline"><input type="checkbox" name="use_smtp" value="1" <?php isset($smtp_data['use_smtp']) && ($smtp_data['use_smtp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','SMTP enabled'); ?></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Login');?></label>
<input type="text" name="username" value="<?php (isset($smtp_data['username']) && $smtp_data['username'] != '') ? print $smtp_data['username'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Password');?></label>
<input type="password" name="password" value="<?php (isset($smtp_data['password']) && $smtp_data['password'] != '') ? print $smtp_data['password'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Host');?></label>
<input type="text" name="host" value="<?php (isset($smtp_data['host']) && $smtp_data['host'] != '') ? print $smtp_data['host'] : print '' ?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Port');?></label>
<input type="text" name="port" value="<?php (isset($smtp_data['port']) && $smtp_data['port'] != '') ? print $smtp_data['port'] : print '25' ?>" />

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<input type="submit" class="button small round" name="StoreSMTPSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>