<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator') . ' - ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/startchatwithoperator')?>/<?php echo $user->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

    <div class="form-group">
        <textarea class="form-control" name="Message" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the operator');?>"><?php echo htmlspecialchars($msg->msg) ?></textarea>
    </div>
    
    <input type="hidden" value="SendMessage" name="SendMessage" />
    
    <div class="btn-group" role="group" aria-label="...">
		<input type="submit" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator');?>" class="btn btn-default btn-sm">
		<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?>" class="btn btn-default btn-sm" onclick="$('#myModal').modal('hide')">
	</div>
	
</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>