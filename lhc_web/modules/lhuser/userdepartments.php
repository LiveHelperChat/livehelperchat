<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/department/userdepartments.tpl.php');
$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);

if ($user instanceof erLhcoreClassModelUser)
{
    $allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');
    $tpl->set('editdepartaments',$allowEditDepartaments);
    $tpl->set('user', $user);
    $tpl->set('selfedit', $Params['user_parameters_unordered']['editor'] == 'self');
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    $tpl->set('auto_close_dialog',true);
    $tpl->set('chat_id',(int)$Params['user_parameters']['dep_id']);
    echo $tpl->fetch();
    exit;
}

exit;

?>