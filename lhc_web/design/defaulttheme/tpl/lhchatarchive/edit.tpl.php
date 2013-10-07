<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Edit archive');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Archive updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chatarchive/edit')?>/<?php echo $archive->id?>" method="post">

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date from');?></label>
	<input type="text" name="RangeFrom" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_from_front);?>" />

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date to');?></label>
	<input type="text" name="RangeTo" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_to_front);?>" />

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="small alert button right radius" name="Delete_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>"/>

	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="Save_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
      <li><input type="submit" class="small button" name="Save_and_continue_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save and continue');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
    </ul>

</form>