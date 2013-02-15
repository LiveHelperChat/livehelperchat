<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','New group');?></h1> 

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/newgroup')?>" method="post">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Title');?></label>
<input type="text" name="Name"  value="" />

<ul class="button-group radius">
    <li><input type="submit" class="small button" name="Save_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save');?>"/></li>
    <li><input type="submit" class="small button" name="Save_group_and_assign_user" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save and assign user');?>"/></li>
</ul>			
</form>
