<?php

$file = erLhcoreClassModelChatFile::fetch($Params['user_parameters']['id']);

echo json_encode(['result' => str_replace('//' . $_SERVER['HTTP_HOST'] , '',erLhcoreClassBBCode::make_clickable('[file=' . $file->id . '_' . $file->security_hash . ($Params['user_parameters_unordered']['mode'] == 'link' ? ' linkdirect' : '_rawimg') . ']'))]);

exit;

?>