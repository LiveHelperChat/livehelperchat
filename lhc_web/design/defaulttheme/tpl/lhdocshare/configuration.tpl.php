<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/configuration','Configuration');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/configuration','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/configuration','Path to libreoffice'); ?></label>
<input type="text" name="LibreOfficePath" value="<?php isset($docsharer_data['libre_office_path']) ? print $docsharer_data['libre_office_path'] : print '/usr/bin/libreoffice' ?>">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/configuration','Supported extensions')?></label>
<input type="text" name="SupportedExtensions" value="<?php isset($docsharer_data['supported_extension']) ? print $docsharer_data['supported_extension'] : print 'ppt,pptx,doc,odp,epub,mobi,docx,xlsx,txt,xls,xlsx,pdf,rtf,odt' ?>">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/configuration','Maximum file size in MB')?></label>
<input type="text" name="MaxFileSize" value="<?php isset($docsharer_data['max_file_size']) ? print $docsharer_data['max_file_size'] : print '2' ?>">

<label><input <?php isset($docsharer_data['background_process']) && $docsharer_data['background_process'] == true ? print 'checked="checked"' : print '' ?> type="checkbox" name="BackgroundProcess" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Make conversion in the background, requires running cronjob')?></label>

<input type="submit" class="button small radius" name="StoreConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>