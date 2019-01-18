<?php if (!isset($embed_mode)) : ?>
<h1><?php echo htmlspecialchars($form->name)?></h1>
<?php endif; ?>

<?php if (erLhcoreClassFormRenderer::isCollected()) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','Information collected'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
	<p><?php echo $form->post_content?></p>	
	<a class="btn btn-default" href="<?php if (isset($action_url)) : ?><?php echo $action_url?><?php else : ?><?php echo erLhcoreClassDesign::baseurl('form/fill')?><?php endif;?>/<?php echo $form->id?>?new"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','Return');?></a>
<?php else : ?>

<?php $errors = erLhcoreClassFormRenderer::getErrors();
if (!empty($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" action="<?php if (isset($action_url)) : ?><?php echo $action_url?><?php else : ?><?php echo erLhcoreClassDesign::baseurl('form/fill')?><?php endif;?>/<?php echo $form->id?>">
	<?php echo $content?>
	<div>
		<input type="submit" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','Submit');?>" name="SubmitForm" />
	</div>
</form>

<?php endif; ?>