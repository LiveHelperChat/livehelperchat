<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/cannedmsgedit.tpl.php'));?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/confirm_global.tpl.php'));?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsgedit')?>/<?php echo $canned_message->id?>" method="post" onsubmit="return confirmSave()">

    <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsgform.tpl.php'));?>
    
    <div class="btn-group" role="group" aria-label="...">
	  <input type="submit" class="btn btn-secondary" name="Save_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
      <input type="submit" class="btn btn-secondary" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
      <input type="submit" class="btn btn-secondary" name="Cancel_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
	</div>

</form>
