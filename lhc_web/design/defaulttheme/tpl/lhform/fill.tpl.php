<h1><?php echo htmlspecialchars($form->name)?></h1>

<?php if (erLhcoreClassFormRenderer::isCollected()) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Information collected'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $errors = erLhcoreClassFormRenderer::getErrors();
if (!empty($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" action="<?php echo erLhcoreClassDesign::baseurl('form/fill')?>/<?php echo $form->id?>">
	<?php echo $content?>
	<div>
		<input type="submit" class="button small" value="Submit" name="SubmitForm" />
	</div>
</form>