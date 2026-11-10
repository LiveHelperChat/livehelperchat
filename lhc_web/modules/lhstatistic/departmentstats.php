<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/departmentstats.tpl.php');

try {
    $tab = isset($Params['user_parameters_unordered']['tab']) ? (string)$Params['user_parameters_unordered']['tab'] : '';
    $action = isset($Params['user_parameters_unordered']['action']) ? (string)$Params['user_parameters_unordered']['action'] : '';

    $tpl->set('tab', in_array($tab, array('op', 'autoassign'), true) ? $tab : 'chats');
    if ($Params['user_parameters_unordered']['type'] == 'group') {
        $department_group = erLhcoreClassModelDepartamentGroup::fetch($Params['user_parameters']['id']);
        $tpl->set('department_group', $department_group);
    } else {
        $department = erLhcoreClassModelDepartament::fetch($Params['user_parameters']['id']);

        // `statistic/departmentstats/(action)/autoassign` renders only the auto assignment queue,
        // it is polled by the `Next auto assignment` tab instead of reloading the whole modal
        if ($action === 'autoassign' && $department instanceof erLhcoreClassModelDepartament) {
            $tpl = erLhcoreClassTemplate::getInstance('lhstatistic/autoassign_queue.tpl.php');
        }

        $tpl->set('department', $department);
    }

} catch(Exception $e) {
    $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

echo $tpl->fetch();
exit;

?>