<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

if ($currentUser->getUserID() == $Params['user_parameters']['user_id']) {
    die('You can not delete your own account!');
    exit;
}

if ((int)$Params['user_parameters']['user_id'] == 1) {
    die('admin account never can be deleted!');
    exit;
}

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

// User views
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_abstract_saved_search' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
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

// User languages
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_speech_user_language' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// User groups
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_departament_group_user' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

// Group chat member
$q = ezcDbInstance::get()->createDeleteQuery();
$q->deleteFrom( 'lh_group_chat_member' )->where( $q->expr->eq( 'user_id', $Params['user_parameters']['user_id'] ) );
$stmt = $q->prepare();
$stmt->execute();

foreach (\LiveHelperChat\Models\Departments\UserDepAlias::getList(['filter' => ['user_id' => (int)$Params['user_parameters']['user_id']]]) as $item) {
    $item->removeThis();
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.deleted',array('userData' => $departament));

erLhcoreClassModule::redirect('user/userlist');
exit;

?>