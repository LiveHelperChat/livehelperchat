<?php

$templateOverride = 'lhchat/demo.tpl.php';

// Use demo pagelayout
$pagelayoutOverride = 'demo';

// Disable online tracking for demo by session cookies
$_GET['cd'] = 1;

if ($Params['user_parameters_unordered']['department'] === null) {

    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $start_data_fields = $startDataFields = (array)$startData->data;

    if (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true) {
        $department = erLhcoreClassModelDepartament::findOne(['filter' => ['disabled' => 0, 'archive' => 0]]);
        if (is_object($department)) {
            $Params['user_parameters_unordered']['department'] = [$department->id];
        }
    }
}

include 'modules/lhchat/start.php';

?>