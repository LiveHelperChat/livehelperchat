<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Time zone settings');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Set application specific time zone');?></label>
<?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
<select name="TimeZone">
		<option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Server default time zone');?>]]</option>
	<?php foreach ($tzlist as $zone) : ?>
		<option value="<?php echo htmlspecialchars($zone)?>" <?php $timezone == $zone ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($zone)?></option>
	<?php endforeach;?>
</select>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Date format E.g (Y-m-d)');?></label>
<input type="text" name="DateFormat" value="<?php echo htmlspecialchars($date_format)?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Full date format E.g (Y-m-d H:i:s)');?></label>
<input type="text" name="DateFullFormat" value="<?php echo htmlspecialchars($date_date_hour_format)?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Hour format E.g (H:i:s)');?></label>
<input type="text" name="DateHourFormat" value="<?php echo htmlspecialchars($date_hour_format)?>" />

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<input type="submit" class="radius button small" name="StoreTimeZoneSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update'); ?>" />

</form>