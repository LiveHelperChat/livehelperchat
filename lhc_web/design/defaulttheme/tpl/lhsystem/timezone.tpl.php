<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Time zone settings');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Set application specific time zone');?></label>
<?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
<select class="form-control" name="TimeZone">
		<option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Server default time zone');?>]]</option>
	<?php foreach ($tzlist as $zone) : ?>
		<option value="<?php echo htmlspecialchars($zone)?>" <?php $timezone == $zone ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($zone)?></option>
	<?php endforeach;?>
</select>
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Date format E.g (Y-m-d)');?></label>
<input type="text" class="form-control" name="DateFormat" value="<?php echo htmlspecialchars($date_format)?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Full date format E.g (Y-m-d H:i:s)');?></label>
<input type="text" class="form-control" name="DateFullFormat" value="<?php echo htmlspecialchars($date_date_hour_format)?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Hour format E.g (H:i:s)');?></label>
<input type="text" class="form-control" name="DateHourFormat" value="<?php echo htmlspecialchars($date_hour_format)?>" />
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<input type="submit" class="btn btn-default" name="StoreTimeZoneSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update'); ?>" />

</form>