<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('departament/new')?>" method="post">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" name="Name"  value="<?php echo htmlspecialchars($departament->name);?>" />

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','E-mail');?></label>
    <input type="text" name="Email"  value="<?php echo htmlspecialchars($departament->email);?>" />

    <ul class="button-group radius">
    <li><input type="submit" class="small button" name="Save_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
	<li><input type="submit" class="small button" name="Cancel_departament" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
	</ul>

</form>
