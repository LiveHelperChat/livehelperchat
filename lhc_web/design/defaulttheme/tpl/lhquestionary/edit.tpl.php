<?php include(erLhcoreClassDesign::designtpl('lhquestionary/embed_button.tpl.php'));?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Edit the question');?> - <?php echo htmlspecialchars($question->question)?></h1>

<div class="section-container auto" data-section>
  <section <?php if ($tab == '') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Question');?></a></p>
    <div class="content" data-section-content>

    <div>
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
	</div>

    </div>
  </section>
  <section <?php if ($tab == 'voting') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Voting options');?></a></p>
    <div class="content" data-section-content>
      <?php include(erLhcoreClassDesign::designtpl('lhquestionary/voting.tpl.php'));?>
    </div>
  </section>
  <section <?php if ($tab == 'answers') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Answers');?></a></p>
    <div class="content" data-section-content>
      <?php include(erLhcoreClassDesign::designtpl('lhquestionary/answers.tpl.php'));?>
    </div>
  </section>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>