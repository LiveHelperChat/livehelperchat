<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send a message to the user') ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Message was sent to the user'); ?>

<script>
<?php if (isset($start_chat)) : ?>
$('#myModal').modal('hide');
lhinst.startChat("<?php echo $chat->id?>",$('#tabs'),<?php echo json_encode((string)$chat->nick,JSON_HEX_APOS)?>);
<?php else : ?>
setTimeout(function() {
    $('#myModal').modal('hide');
},2000);
<?php endif; ?>
</script>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','If the message was already sent, this will mark it as not delivered and the user will be shown the chat message again.');?></p>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $visitor->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/sendnotice_content.tpl.php'));?>
</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>