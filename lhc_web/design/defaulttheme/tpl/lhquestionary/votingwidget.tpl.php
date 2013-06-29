<div class="section-container tabs float-break" data-section="tabs">
  <section>
	<p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Questionary');?></a></p>

	<div class="content" data-section-content>
	<?php if (isset($received)) : ?>
	<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Thank you!');?></h2>
	<?php elseif (isset($already_voted)) : ?>
	<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','You have already voted, thank you!');?></h2>
	<?php else : ?>
	<form action="<?php echo erLhcoreClassDesign::baseurl('questionary/votingwidget')?><?php echo $append_mode?>" method="post" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">
	<?php if ($voting !== false) : ?>
	<h4 class="mt0 mb5"><?php echo htmlspecialchars($voting->question)?></h4>

	<?php if ($voting->question_intro != '') : ?>
	<p class="fs11 subheader"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($voting->question_intro))?></p>
	<?php endif;?>

	<?php if (isset($errors)) : ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
	<?php endif; ?>

	<?php if ($voting->is_voting == 1) : ?>
		<?php foreach ($voting->options as $option) : ?>
			<label><input type="radio" name="Option" value="<?php echo $option->id?>" /> <?php echo htmlspecialchars($option->option_name)?></label>
		<?php endforeach;?>
		<br>
		<input type="submit" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Vote');?>" name="VoteAction">
		<input type="hidden" value="1" name="VoteAction"/>
		<?php else : ?>
		<textarea name="feedBack"><?php echo htmlspecialchars($answer->answer)?></textarea>

		<input type="submit" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Send');?>" name="FeedBackAction">
		<input type="hidden" value="1" name="FeedBackAction"/>
		<?php endif;?>

		<input type="hidden" name="QuestionID" value="<?php echo $voting->id ?>" />
		<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>

	<?php else :  ?>
	<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','We do not have any requests for now.');?></p>
	<?php endif; ?>
	</form>
	<?php endif;?>
	</div>

	</section>
</div>
