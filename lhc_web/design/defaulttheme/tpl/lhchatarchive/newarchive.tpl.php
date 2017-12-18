<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','New archive');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($step_2)) : ?>

<?php include(erLhcoreClassDesign::designtpl('lhchatarchive/process_content.tpl.php'));?>

<?php else : ?>
<form action="<?php echo erLhcoreClassDesign::baseurl('chatarchive/newarchive')?>" method="post">
    
    <div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date from');?></label>
	   <input class="form-control" type="text" name="RangeFrom" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_from_edit);?>" />
    </div>
    
	<div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Date to');?></label>
	   <input class="form-control" type="text" name="RangeTo" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','E.g');?> <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($archive->range_to_edit);?>" />
    </div>
	
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<div class="btn-group" role="group" aria-label="...">
      <input type="submit" class="btn btn-default" name="Save_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Continue');?>"/>
      <input type="submit" class="btn btn-default" name="Cancel_archive" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>
<?php endif;?>