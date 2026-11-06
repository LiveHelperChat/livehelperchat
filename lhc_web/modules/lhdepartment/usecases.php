<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdepartment/usecases.tpl.php');

$department = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);

if ($department instanceof erLhcoreClassModelDepartament) {
    $tpl->set('items', \LiveHelperChat\Helpers\Department\UseCases::getUseCases($department));
    $tpl->set('department', $department);
} else {
    $tpl->set('items', array());
    $tpl->set('department', null);
}

echo $tpl->fetch();

?>
