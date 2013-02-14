<?php

$role = erLhcoreClassRole::getSession()->load( 'erLhcoreClassModelRole', $Params['user_parameters']['role_id']);
erLhcoreClassRole::getSession()->delete($role);

// Delete user assigned departaments
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_rolefunction' )->where( $q->expr->eq( 'role_id', $Params['user_parameters']['role_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_grouprole' )->where( $q->expr->eq( 'role_id', $Params['user_parameters']['role_id'] ) );
$stmt = $q->prepare();
$stmt->execute();


erLhcoreClassModule::redirect('permission/roles');
exit;

?>