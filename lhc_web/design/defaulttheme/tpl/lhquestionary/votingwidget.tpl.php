<div role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Questionary');?></a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="panel1">
			
			

	<?php if (isset($received)) : ?>
	<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Thank you!');?></h2>
	<?php elseif (isset($already_voted)) : ?>
	<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','You have already voted, thank you!');?></h2>
	<?php else : ?>
	<form action="<?php echo erLhcoreClassDesign::baseurl('questionary/votingwidget')?><?php echo $append_mode?>" method="post" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">
	<?php if ($voting !== false) : ?>
	<h4 class="mt0 mb5"><?php echo htmlspecialchars($voting->question)?></h4>

	<?php if ($voting->question_intro != '') : ?>
	<p><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($voting->question_intro))?></p>
	<?php endif;?>

	<?php if (isset($errors)) : ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
	<?php endif; ?>

	<?php if ($voting->is_voting == 1) : ?>
		<?php foreach ($voting->options as $option) : ?>
			<label><input type="radio" name="Option" value="<?php echo $option->id?>" />&nbsp;<?php echo htmlspecialchars($option->option_name)?></label>&nbsp;
		<?php endforeach;?>
		<br>
		<input type="submit" class="btn btn-default btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Vote');?>" name="VoteActionButton">
		<input type="hidden" value="1" name="VoteAction"/>
		<?php else : ?>
		<textarea class="form-control form-group" name="feedBack"><?php echo htmlspecialchars($answer->answer)?></textarea>

		<input type="submit" class="btn btn-default btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Send');?>" name="VoteActionButton">
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
		</div>
</div>
