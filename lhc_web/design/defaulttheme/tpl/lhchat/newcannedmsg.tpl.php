<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message');?></h1> 

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/newcannedmsg')?>" method="post">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?></label>
    <textarea name="Message"><?php echo htmlspecialchars($msg->msg);?></textarea>
    
    <ul class="button-group radius">
    <li><input type="submit" class="small button" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
	<li><input type="submit" class="small button" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
	</ul>
	
</form>
