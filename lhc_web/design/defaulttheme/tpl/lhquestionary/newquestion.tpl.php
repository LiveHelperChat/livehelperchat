<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','Enter a new question');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('questionary/newquestion')?>">

<?php include(erLhcoreClassDesign::designtpl('lhquestionary/question_form.tpl.php'));?>

<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-default" name="SaveAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','Save');?>"/>
	<input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','Cancel');?>"/>
</div>

</form>