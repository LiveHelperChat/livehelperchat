<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/newcannedmsg.tpl.php'));?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/newcannedmsg')?>" method="post">
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsgform.tpl.php'));?>
	
	<div class="btn-group" role="group" aria-label="...">
	   <input type="submit" class="btn btn-default" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
	   <input type="submit" class="btn btn-default" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
	</div>

</form>
