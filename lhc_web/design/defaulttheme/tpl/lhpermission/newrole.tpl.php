<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New role');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('permission/newrole')?>" method="post">

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Title');?></label>
        <input class="form-control" type="text" name="Name"  value="" />
    </div>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Policy list');?></h2>

	<table class="table" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	     <th>&nbsp;</th>
	     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Module');?></th>
	     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Function');?></th>
	</tr>
	</thead>
	</table>
	<input type="submit" class="btn btn-default" name="New_policy" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New policy');?>"/>
	<br /><br />

	<div class="btn-group" role="group" aria-label="...">
	   <input type="submit" class="btn btn-default" name="Save_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Save');?>"/>
	   <input type="submit" class="btn btn-default" name="Cancel_role" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Cancel');?>"/>
	</div>
	
</form>
