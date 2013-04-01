<?php include(erLhcoreClassDesign::designtpl('lhquestionary/embed_button.tpl.php'));?>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Edit question');?> - <?php echo htmlspecialchars($question->question)?></h1>

<dl class="tabs">
  <dd <?php if ($tab == '') : ?>class="active"<?php endif;?>><a href="#simple1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Question');?></a></dd>
  <dd <?php if ($tab == 'voting') : ?>class="active"<?php endif;?>><a href="#simple2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Voting options');?></a></dd>
  <dd <?php if ($tab == 'answers') : ?>class="active"<?php endif;?>><a href="#simple3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Answers');?></a></dd>
</dl>

<ul class="tabs-content">
	<li <?php if ($tab == '') : ?>class="active"<?php endif;?> id="simple1Tab">

		<?php if (isset($errors)) : ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
		<?php endif; ?>

		<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Updated'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>

		<form action="<?php echo erLhcoreClassDesign::baseurl('questionary/edit')?>/<?php echo $question->id?>" method="post">

		    <?php include(erLhcoreClassDesign::designtpl('lhquestionary/question_form.tpl.php'));?>

			<ul class="button-group radius">
		      <li><input type="submit" class="small button" name="SaveAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Save');?>"/></li>
		      <li><input type="submit" class="small button" name="UpdateAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Update');?>"/></li>
		      <li><input type="submit" class="small button" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Cancel');?>"/></li>
		    </ul>

		</form>
	</li>
	<li <?php if ($tab == 'voting') : ?>class="active"<?php endif;?> id="simple2Tab">
		<?php include(erLhcoreClassDesign::designtpl('lhquestionary/voting.tpl.php'));?>
	</li>
	<li <?php if ($tab == 'answers') : ?>class="active"<?php endif;?> id="simple3Tab">
		<?php include(erLhcoreClassDesign::designtpl('lhquestionary/answers.tpl.php'));?>
	</li>
</ul>