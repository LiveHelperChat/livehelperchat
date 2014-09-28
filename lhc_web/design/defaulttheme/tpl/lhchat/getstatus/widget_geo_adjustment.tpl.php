<?php
$exitTemplate = false; 
$statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value, $input_data->vid);

if ($statusGeoAdjustment['status'] == 'offline') {
	$forceoffline = true;
	$leaveamessage = true;
} elseif ($statusGeoAdjustment['status'] == 'hidden') { 
	$errors = array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live support is not available in your country'));
	?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php $exitTemplate = true;
} ?>