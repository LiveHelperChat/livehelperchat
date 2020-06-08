<?php
$tpl = erLhcoreClassTemplate::getInstance('lhuser/setopstatus.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);

if (ezcInputForm::hasPostData()) {
    $definition = array(
        'onlineStatus' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('onlineStatus') && $form->onlineStatus == 1) {
        $status = 0;
    } else {
        $status = 1;
    }

    $db = ezcDbInstance::get();

    try {
        $db->beginTransaction();

        $user->hide_online = $status;

        erLhcoreClassUser::getSession()->update($user);

        erLhcoreClassUserDep::setHideOnlineStatus($user);

        erLhcoreClassChat::updateActiveChats($user->id);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $user, 'reason' => 'user_action'));

        $db->commit();

        $tpl->set('updated', true);

    } catch (Exception $e) {
        $tpl->set('error', $e->getMessage());
        $db->rollback();
    }
}

$tpl->setArray(array(
    'user' => $user
));

echo $tpl->fetch();
exit();

?>