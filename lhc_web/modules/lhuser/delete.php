<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$userId = (int)$Params['user_parameters']['user_id'];

if ($currentUser->getUserID() == $userId) {
    die('You can not delete your own account!');
    exit;
}

if ($userId == 1) {
    die('admin account never can be deleted!');
    exit;
}

$departament = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', $userId);
erLhcoreClassUser::getSession()->delete($departament);

// Transfered chats to user
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'transfer_user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// User departments
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_userdep' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_userdep_disabled' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// User views
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_abstract_saved_search' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// User groups
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_groupuser' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// User remember
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_users_remember' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// User languages
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_speech_user_language' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// User department group memberships
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_departament_group_user' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( ' lh_departament_group_user_disabled' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// Group chat member
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_group_chat_member' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// Browser notifications
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_notification_op_subscriber' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

// Reports
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_abstract_saved_report' )->where( $q->expr->eq( 'user_id', $userId ) );
$stmt = $q->prepare();
$stmt->execute();

foreach (\LiveHelperChat\Models\Departments\UserDepAlias::getList(['filter' => ['user_id' => $userId]]) as $item) {
    $item->removeThis();
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.deleted',array('userData' => $departament));

erLhcoreClassModule::redirect('user/userlist');
exit;

?>