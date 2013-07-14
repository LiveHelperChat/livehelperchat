<?php include(erLhcoreClassDesign::designtpl('lhquestionary/embed_button.tpl.php'));?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','Enter a new question');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('questionary/newquestion')?>">

<?php include(erLhcoreClassDesign::designtpl('lhquestionary/question_form.tpl.php'));?>

<ul class="button-group radius">
    <li><input type="submit" class="small button" name="SaveAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','Save');?>"/></li>
	<li><input type="submit" class="small button" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','Cancel');?>"/></li>
</ul>

</form>