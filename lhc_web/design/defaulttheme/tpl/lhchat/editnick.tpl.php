<?php 
$modalHeaderClass = 'small-modal-header';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/editnick','Your information');
$modalSize = 'md';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/editnick','Updated!'); ?>

<script>
$('#messages .usr-tit.vis-tit').text(<?php echo json_encode($chat->nick)?>).prepend('<i class="material-icons chat-operators mi-fs15 mr-0">face</i>');
setTimeout(function(){
    $('#myModal').modal('hide');
},100);
</script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	

<?php else  : ?>
<form action="<?php echo erLhcoreClassDesign::baseurl('chat/editnick')?>/<?php echo $chat->id,'/',$chat->hash?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
	<div class="form-group">
		<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?></label> <input maxlength="50" type="text" name="UserNick" class="form-control input-sm" value="<?php echo htmlspecialchars($chat->nick)?>" />
	</div>

	<div class="row form-group">
		<div class="col-xs-6 pr5">
			<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?></label> <input type="text" name="Email" class="form-control input-sm" value="<?php echo htmlspecialchars($chat->email)?>" />
		</div>
		<div class="col-xs-6 pl5">
			<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?></label> <input type="text" maxlength="50" name="UserPhone" class="form-control input-sm" value="<?php echo htmlspecialchars($chat->phone)?>" />
		</div>
	</div>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?>" class="btn btn-default btn-sm">
		<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?>" class="btn btn-default btn-sm" onclick="$('#myModal').modal('hide')">
	</div>

</form>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>