<?php

$departament = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', $Params['user_parameters']['user_id']);
erLhcoreClassUser::getSession()->delete($departament);

// Transfered chats to user
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'transfer_user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// User departaments
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_userdep' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// User groups
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_groupuser' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// User remember
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_users_remember' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

erLhcoreClassModule::redirect('user/userlist');
exit;

?>