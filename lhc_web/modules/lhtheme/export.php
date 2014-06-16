<?php 

$theme = erLhAbstractModelWidgetTheme::fetch((int)$Params['user_parameters']['theme']);

$exportData = $theme->getState();

unset($exportData['id']);
unset($exportData['online_image']);
unset($exportData['online_image_path']);
unset($exportData['offline_image']);
unset($exportData['offline_image_path']);
unset($exportData['logo_image']);
unset($exportData['logo_image_path']);
unset($exportData['need_help_image_path']);
unset($exportData['need_help_image']);
 
if ($theme->logo_image_url != ''){
	$exportData['logo_image_data'] = base64_encode(file_get_contents($theme->logo_image_path.'/'.$theme->logo_image));	
	$parts = explode('.', $theme->logo_image);
	$exportData['logo_image_data_ext'] = array_pop($parts);
}

if ($theme->need_help_image_url != ''){
	$exportData['need_help_image_data'] = base64_encode(file_get_contents($theme->need_help_image_path.'/'.$theme->need_help_image));
	$parts = explode('.', $theme->need_help_image);
	$exportData['need_help_image_data_ext'] = array_pop($parts);
}

if ($theme->online_image_url != ''){
	$exportData['online_image_data'] = base64_encode(file_get_contents($theme->online_image_path.'/'.$theme->online_image));
	$parts = explode('.', $theme->online_image);
	$exportData['online_image_data_ext'] = array_pop($parts);
}

if ($theme->offline_image_url != ''){
	$exportData['offline_image_data'] = base64_encode(file_get_contents($theme->offline_image_path.'/'.$theme->offline_image));
	$parts = explode('.', $theme->offline_image);
	$exportData['offline_image_data_ext'] = array_pop($parts);
}

header('Content-Disposition: attachment; filename="lhc-theme-'.$theme->id.'.json"');
header('Content-Type: application/json');
echo json_encode($exportData);

exit;

?>