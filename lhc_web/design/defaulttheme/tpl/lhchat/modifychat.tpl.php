<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Modify chat');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($chat_updated) && $chat_updated == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Chat information was updated'); ?>
<script>
setTimeout(function(){
	var originValue = parent.lhinst.closeWindowOnChatCloseDelete;
	parent.lhinst.closeWindowOnChatCloseDelete = false;
	parent.lhinst.removeDialogTab('<?php echo $chat->id?>',parent.$('#tabs'),true);
	parent.lhinst.closeWindowOnChatCloseDelet = originValue;
	parent.lhinst.startChat('<?php echo $chat->id?>',parent.$('#tabs'),'<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>');
	parent.$.colorbox.close();
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">
	<div class="row">
		<div class="column small-12">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','E-mail');?></label>
			<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Recipient e-mail');?>" name="Email" value="<?php echo htmlspecialchars($chat->email);?>" />
		</div>
		<div class="column small-12">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?></label>
			<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?>" name="UserNick" value="<?php echo htmlspecialchars($chat->nick);?>" />
		</div>
	</div>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<ul class="button-group radius">
	  <li><input type="submit" class="button small" name="UpdateChat" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Update chat');?>" /></li> 
	</ul>
</form>