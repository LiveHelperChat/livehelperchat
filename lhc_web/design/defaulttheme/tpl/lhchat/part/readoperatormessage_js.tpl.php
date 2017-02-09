<script>
<?php if ($hasExtraField == false && isset($start_data_fields['message_auto_start']) && $start_data_fields['message_auto_start'] == true && isset($start_data_fields['message_auto_start_key_press']) && $start_data_fields['message_auto_start_key_press'] == true) : ?>
$('#id_Question').on('keydown', function (e) {
	if ($( "#ReadOperatorMessage").attr("key-up-started") != 1) {
    	$( "#ReadOperatorMessage").attr("key-up-started",1);
    	$( "#ReadOperatorMessage").submit();	
	}
});
<?php endif;?>

var formSubmitted = false;
jQuery('#id_Question').bind('keydown', 'return', function (evt){
	if (formSubmitted == false) {
		$( "#ReadOperatorMessage" ).submit();
		<?php if (!isset($start_data_fields['message_auto_start']) || $start_data_fields['message_auto_start'] == false) : ?>
		formSubmitted = true;
		jQuery('#id_Question').attr('readonly','readonly');		
		<?php endif;?>
	};
	return false;
});
<?php if ($playsound == true) : ?>
$(function() {lhinst.playInvitationSound();});
<?php endif; ?>
</script>