<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','New admin theme');?></h1>

<form action="" method="post" autocomplete="off">

    <?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/form.tpl.php'));?>
	
	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" class="btn btn-default" name="SaveSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
	</div>
	
</form>