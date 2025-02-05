<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

if ((int)$Params['user_parameters']['group_id'] == 1) {
    die('admin account never can be deleted!');
    exit;
}

$GroupData = erLhcoreClassModelGroup::fetch((int)$Params['user_parameters']['group_id']);

erLhcoreClassLog::logObjectChange(array(
    'object' => $GroupData,
    'msg' => array(
        'action' => 'delete_group',
        'user_id' => $currentUser->getUserID(),
        'group' => $GroupData,
    )
));

erLhcoreClassGroup::deleteGroup((int)$Params['user_parameters']['group_id']);

erLhcoreClassAdminChatValidatorHelper::clearUsersCache();

erLhcoreClassModule::redirect('user/grouplist');
exit;

?>