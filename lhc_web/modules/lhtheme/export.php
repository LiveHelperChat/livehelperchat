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
unset($exportData['copyright_image_path']);
unset($exportData['operator_image_path']);
unset($exportData['copyright_image']);
unset($exportData['operator_image']);

unset($exportData['minimize_image']);
unset($exportData['restore_image']);
unset($exportData['close_image']);
unset($exportData['popup_image']);

unset($exportData['minimize_image_path']);
unset($exportData['restore_image_path']);
unset($exportData['close_image_path']);
unset($exportData['popup_image_path']);



if ($theme->logo_image_url != ''){
	$exportData['logo_image_data'] = base64_encode($theme->getContentAttribute('logo_image'));	
	$parts = explode('.', $theme->logo_image);
	$exportData['logo_image_data_ext'] = array_pop($parts);
}
 
if ($theme->copyright_image_url != ''){
	$exportData['copyright_image_data'] = base64_encode($theme->getContentAttribute('copyright_image'));	
	$parts = explode('.', $theme->copyright_image);
	$exportData['copyright_image_data_ext'] = array_pop($parts);
}

if ($theme->need_help_image_url != ''){
	$exportData['need_help_image_data'] = base64_encode($theme->getContentAttribute('need_help_image'));
	$parts = explode('.', $theme->need_help_image);
	$exportData['need_help_image_data_ext'] = array_pop($parts);
}

if ($theme->online_image_url != ''){
	$exportData['online_image_data'] = base64_encode($theme->getContentAttribute('online_image'));
	$parts = explode('.', $theme->online_image);
	$exportData['online_image_data_ext'] = array_pop($parts);
}

if ($theme->offline_image_url != ''){
	$exportData['offline_image_data'] = base64_encode($theme->getContentAttribute('offline_image'));
	$parts = explode('.', $theme->offline_image);
	$exportData['offline_image_data_ext'] = array_pop($parts);
}

if ($theme->operator_image_url != ''){
	$exportData['operator_image_data'] = base64_encode($theme->getContentAttribute('operator_image'));
	$parts = explode('.', $theme->operator_image);
	$exportData['operator_image_data_ext'] = array_pop($parts);
}

if ($theme->minimize_image_url != ''){
	$exportData['minimize_image_data'] = base64_encode($theme->getContentAttribute('minimize_image'));
	$parts = explode('.', $theme->minimize_image);
	$exportData['minimize_image_data_ext'] = array_pop($parts);
}

if ($theme->restore_image_url != ''){
	$exportData['restore_image_data'] = base64_encode($theme->getContentAttribute('restore_image'));
	$parts = explode('.', $theme->restore_image);
	$exportData['restore_image_data_ext'] = array_pop($parts);
}

if ($theme->close_image_url != ''){
	$exportData['close_image_data'] = base64_encode($theme->getContentAttribute('close_image'));
	$parts = explode('.', $theme->close_image);
	$exportData['close_image_data_ext'] = array_pop($parts);
}

if ($theme->popup_image_url != ''){
	$exportData['popup_image_data'] = base64_encode($theme->getContentAttribute('popup_image'));
	$parts = explode('.', $theme->popup_image);
	$exportData['popup_image_data_ext'] = array_pop($parts);
}

header('Content-Disposition: attachment; filename="lhc-theme-'.$theme->id.'.json"');
header('Content-Type: application/json');
echo json_encode($exportData);

exit;

?>