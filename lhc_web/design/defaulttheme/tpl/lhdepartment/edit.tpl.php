<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit department');?> - <?php echo htmlspecialchars($departament->name)?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

	<?php include(erLhcoreClassDesign::designtpl('lhdepartment/form.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
         <input type="submit" class="btn btn-default" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
         <input type="submit" class="btn btn-default" name="Update_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
         <input type="submit" class="btn btn-default" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
         <?php if ($departament->id != 1 && $currentUser->hasAccessTo('lhdepartment','delete') && $departament->can_delete == true ) : ?><input type="submit" class="btn btn-danger" name="Delete_departament" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Are you sure?');?>')" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete department');?>"/><?php endif;?>
	</div>
	
</form>
