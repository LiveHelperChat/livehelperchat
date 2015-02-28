<?php 

$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/getpermissionsummary.tpl.php');

$user = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', (int)$Params['user_parameters']['user_id'] );

if ($user->id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','see_permissions_users'))
{
    $tpl->set('user',$user);
    $tpl->set('permissions',erLhcoreClassRole::accessArrayByUserID($user->id));
    
    echo $tpl->fetch();
}

exit;

?>