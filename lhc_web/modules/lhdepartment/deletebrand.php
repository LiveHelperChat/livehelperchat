<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$brand = \LiveHelperChat\Models\Brand\Brand::fetch($Params['user_parameters']['id']);
$brand->removeThis();

erLhcoreClassModule::redirect('department/brands');
exit;

?>