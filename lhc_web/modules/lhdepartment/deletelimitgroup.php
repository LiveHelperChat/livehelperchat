<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$group = erLhcoreClassModelDepartamentLimitGroup::fetch($Params['user_parameters']['id']);
$group->removeThis();

erLhcoreClassAdminChatValidatorHelper::clearUsersCache();

erLhcoreClassModule::redirect('department/limitgroup');
exit;

?>