<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','New group');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/newgroup')?>" method="post">

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Title');?></label>
    <input type="text" name="Name" class="form-control" value="" />
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-default" name="Save_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save');?>"/>
    <input type="submit" class="btn btn-default" name="Save_group_and_assign_user" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save and assign the user');?>"/>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

</form>
