<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','New archive');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($step_2)) : ?>

<?php include(erLhcoreClassDesign::designtpl('lhchatarchive/process_content.tpl.php'));?>

<?php else : ?>
<form action="<?php echo erLhcoreClassDesign::baseurl('chatarchive/newarchive')?>" method="post">

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Date from');?></label>
	<input type="text" name="RangeFrom" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_from_front);?>" />

	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Date to');?></label>
	<input type="text" name="RangeTo" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_to_front);?>" />

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="Save_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Continue');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
    </ul>

</form>
<?php endif;?>