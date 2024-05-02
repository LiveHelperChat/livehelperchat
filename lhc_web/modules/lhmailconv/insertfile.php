<?php

$file = erLhcoreClassModelChatFile::fetch($Params['user_parameters']['id']);

echo json_encode(['result' => str_replace(erLhcoreClassBBCode::getHost() , '',erLhcoreClassBBCode::make_clickable('[file=' . $file->id . '_' . $file->security_hash . ($Params['user_parameters_unordered']['mode'] == 'link' ? ' linkdirect' : '_rawimg') . ']'))],\JSON_INVALID_UTF8_IGNORE);

exit;

?>