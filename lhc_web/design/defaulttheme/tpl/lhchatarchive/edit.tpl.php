<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Edit archive');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Archive updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chatarchive/edit')?>/<?php echo $archive->id?>" method="post">
    
    <div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date from');?></label>
	   <input class="form-control" type="text" name="RangeFrom" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_from_edit);?>" />
	</div>
	
	<div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date to');?></label>
	   <input class="form-control" type="text" name="RangeTo" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_to_edit);?>" />
    </div>
	
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <input type="submit" class="btn btn-danger pull-right" name="Delete_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>"/>

	<div class="btn-group" role="group" aria-label="...">
      <input type="submit" class="btn btn-default" name="Save_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
      <input type="submit" class="btn btn-default" name="Save_and_continue_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save and continue');?>"/>
      <input type="submit" class="btn btn-default" name="Cancel_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>