<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Edit the question');?> - <?php echo htmlspecialchars($question->question)?></h1>

<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="<?php if ($tab == '') : ?>active<?php endif;?>"><a href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Question');?></a></li>
		<li role="presentation" class="<?php if ($tab == 'voting') : ?>active<?php endif;?>"><a href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Voting options');?></a></li>
		<li role="presentation" class="<?php if ($tab == 'answers') : ?>active<?php endif;?>"><a href="#panel3" aria-controls="panel3" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Answers');?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="panel1">		
		<?php if (isset($errors)) : ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
		<?php endif; ?>

		<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Updated'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>

		<form action="<?php echo erLhcoreClassDesign::baseurl('questionary/edit')?>/<?php echo $question->id?>" method="post">

		    <?php include(erLhcoreClassDesign::designtpl('lhquestionary/question_form.tpl.php'));?>
		    
			<div class="btn-group" role="group" aria-label="...">
		      <input type="submit" class="btn btn-default" name="SaveAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Save');?>"/>
		      <input type="submit" class="btn btn-default" name="UpdateAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Update');?>"/>
		      <input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Cancel');?>"/>
		    </div>
		    
		</form>		
		</div>
		
		<div role="tabpanel" class="tab-pane <?php if ($tab == 'voting') : ?>active<?php endif;?>" id="panel2">
		   <?php include(erLhcoreClassDesign::designtpl('lhquestionary/voting.tpl.php'));?>
		</div>
		
		<div role="tabpanel" class="tab-pane <?php if ($tab == 'answers') : ?>active<?php endif;?>" id="panel3">
		  <?php include(erLhcoreClassDesign::designtpl('lhquestionary/answers.tpl.php'));?>
		</div>
	</div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>