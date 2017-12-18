<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Modify chat');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($chat_updated) && $chat_updated == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Chat information was updated'); ?>
<script>
parent.lhinst.reloadTab('<?php echo $chat->id?>',parent.$('#tabs'),'<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>');
setTimeout(function() {
	parent.$('#myModal').modal('hide');
    parent.$('#CSChatMessage-<?php echo $chat->id?>').focus();
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','E-mail');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Recipient e-mail');?>" name="Email" value="<?php echo htmlspecialchars($chat->email);?>" />
	</div>
	
	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?>" name="UserNick" value="<?php echo htmlspecialchars($chat->nick);?>" />
	</div>
	
	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Phone');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Phone');?>" name="UserPhone" value="<?php echo htmlspecialchars($chat->phone);?>" />
	</div>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
	
	<input type="submit" class="btn btn-default" name="UpdateChat" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Update chat');?>" />
</form>