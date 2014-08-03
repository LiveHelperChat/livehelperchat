<?php 

$statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',true);

if ($statusGeoAdjustment['status'] == 'offline') {
	$isOnlineHelp = false;
	$disable_pro_active = true;
	$disableByGeoAdjustment = true;
} elseif ($statusGeoAdjustment['status'] == 'hidden') {
	exit;
}

?>