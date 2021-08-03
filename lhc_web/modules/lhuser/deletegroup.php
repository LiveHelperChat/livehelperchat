<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

erLhcoreClassGroup::deleteGroup((int)$Params['user_parameters']['group_id']);

erLhcoreClassAdminChatValidatorHelper::clearUsersCache();

erLhcoreClassModule::redirect('user/grouplist');
exit;

?>