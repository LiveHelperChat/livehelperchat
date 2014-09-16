<?php if ($leaveamessage == true && (int)erLhcoreClassModelChatConfig::fetch('suggest_leave_msg')->current_value == 1) : ?>
<div id="offline-modal" class="reveal-modal" data-reveal>
  <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department is offline');?></h3>
  <ul class="button-group radius">
  	<li><a href="#" class="button tiny success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Go to leave a message form');?>" onclick="return lhinst.switchToOfflineForm();"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message');?></a></li>
  	<li><a href="#" class="button tiny secondary" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Cancel and choose another department');?>" onclick="closeRevealForm();return lhinst.closeReveal('#offline-modal');"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?></a></li>
  </ul>
  <a class="close-reveal-modal" onclick="closeRevealForm();">&#215;</a>
</div>
<script>
function closeRevealForm() {
	$('#id_DepartamentID').find('option').removeAttr('selected');
	$('#id_DepartamentID').find("[data-attr-online='true']").attr('selected','selected');
};
$('#id_DepartamentID').change(function() {	
	if ( $(this).find('option:selected').attr('data-attr-online') == 'false' ) {		
		$('#offline-modal').foundation('reveal','open',{closeOnBackgroundClick: false});
	};
});
$(document).ready(function() {
	if ( $('#id_DepartamentID').find('option:selected').attr('data-attr-online') == 'false' ) {
		$('#offline-modal').foundation('reveal','open',{closeOnBackgroundClick: false});
	};
});
</script>
<?php endif;?>