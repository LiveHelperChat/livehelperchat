<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/departmentstats.tpl.php');

try {

    if ($Params['user_parameters_unordered']['type'] == 'group') {
        $department_group = erLhcoreClassModelDepartamentGroup::fetch($Params['user_parameters']['id']);
        $tpl->set('department_group', $department_group);
    } else {
        $department = erLhcoreClassModelDepartament::fetch($Params['user_parameters']['id']);
        $tpl->set('department', $department);
    }

} catch(Exception $e) {
    $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

echo $tpl->fetch();
exit;

?>