<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','New document');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('docshare/new')?>" method="post" enctype="multipart/form-data">

<?php include(erLhcoreClassDesign::designtpl('lhdocshare/form.tpl.php'));?>

<br>
<ul class="button-group radius">
     <li><input type="submit" class="small button" name="Save" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
     <li><input type="submit" class="small button" name="Cancel" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
</ul>

</form>