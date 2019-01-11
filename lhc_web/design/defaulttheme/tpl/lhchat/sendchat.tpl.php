<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Send chat');
$modalSize = 'md';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div class="mb-0" style="padding:0px 0 10px 0;">
	<form id="user-action">
			<input class="form-control form-group form-control-sm" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendchat','Enter your e-mail')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendchat','Enter your e-mail')?>" name="UserEmail" value="<?php echo htmlspecialchars($chat->email)?>" />
			
			<div class="btn-group" role="group" aria-label="...">
				<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send')?>" class="btn btn-secondary btn-sm" onclick="lhinst.sendemail()">
				<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?>" class="btn btn-secondary btn-sm" onclick="$('#myModal').modal('hide')">
			</div>
	</form>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>