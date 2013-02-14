<?php

$departament = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', $Params['user_parameters']['user_id']);
erLhcoreClassUser::getSession()->delete($departament);

$q = ezcDbInstance::get()->createDeleteQuery();

// Transfered chats to user
$q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// User departaments
$q->deleteFrom( 'lh_userdep' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// User departaments
$q->deleteFrom( 'lh_groupuser' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

erLhcoreClassModule::redirect('user/userlist');
exit;

?>