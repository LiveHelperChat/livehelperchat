<h3><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($visitor->operator_message)); ?></h3>

<form action="" method="post">

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?>*</label>
<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>

<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>

<input type="submit" name="askQuestion" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Send my question');?>" class="button small radius"/>

</form>