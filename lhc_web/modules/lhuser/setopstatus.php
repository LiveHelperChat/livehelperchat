<?php
$tpl = erLhcoreClassTemplate::getInstance('lhuser/setopstatus.tpl.php');

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);

if (ezcInputForm::hasPostData()) {

    $definition = array(
        'onlineStatus' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int'),
        'offlineReason' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int')
    );

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('onlineStatus') && $form->onlineStatus == 1) {
        $status = 0;
    } else {
        $status = 1;
    }

    $offlineReason = 0;
    if ($form->hasValidData('offlineReason')) {
        $offlineReason = (int)$form->offlineReason;
    }

    $reason = \LiveHelperChat\Models\LHCAbstract\OfflineReason::getReason($offlineReason);
    $isOnlineModeReason = ($reason !== false && $reason->isOnlineMode());

    if ($isOnlineModeReason) {
        // Online mode reason - operator stays online with the reason applied
        $status = 0;
    } elseif ($status == 0) {
        // A plain online status cannot carry an offline mode reason
        $offlineReason = 0;
    }

    $wasOnline = ((int)$user->hide_online === 0);

    $db = ezcDbInstance::get();

    try {
        $db->beginTransaction();

        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            throw new Exception('CSFR Token is missing');
        }

        $user->hide_online = $status;
        $user->offline_reason_id = $offlineReason;

        erLhcoreClassUser::getSession()->update($user);

        // A reason with "Disable auto assigned" temporarily excludes the operator from auto assignment
        \LiveHelperChat\Models\LHCAbstract\OfflineReason::syncAutoAssignStatus($user);

        erLhcoreClassUserDep::setHideOnlineStatus($user);

        erLhcoreClassChat::updateActiveChats($user->id);

        // Went offline OR went online. Any reason change while online starts a new online record.
        $reasonForSessionOffline = $user->hide_online == 1 ? $offlineReason : 0;
        $reasonForSessionOnline = ($user->hide_online == 0 && $isOnlineModeReason) ? $offlineReason : 0;
        $currentUser->updateLastVisit(time(), $user->hide_online == 1 ? 2 : 1, $user->id, $reasonForSessionOffline, $reasonForSessionOnline);

        if ($status == 0 && $wasOnline) {
            // Changing reason while already online - close the previous record at the new one start (no offline gap)
            erLhcoreClassModelUserOnlineSession::closePreviousRecord($user->id);
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $user, 'reason' => 'user_action'));

        $db->commit();

        $tpl->set('updated', true);

    } catch (Exception $e) {
        $tpl->set('error', $e->getMessage());
        $db->rollback();
    }
}

// Offline reasons the operator has access to (same logic as chat/loadinitialdata)
$offlineReasons = array();
if (($reasonsLimitation = $currentUser->hasAccessTo('lhuser', 'offlinereasons_operator', true)) !== false) {

    $filterReasons = ['sort' => 'pos DESC, name ASC', 'limit' => false];

    if (!empty($reasonsLimitation)) {
        // $reasonsLimitation is always a JSON string like {"id":[2,4]}
        $limitationParams = json_decode($reasonsLimitation, true);
        $reasonsLimitation = is_array($limitationParams) && isset($limitationParams['id']) ? $limitationParams['id'] : [];
        erLhcoreClassChat::validateFilterIn($reasonsLimitation);
        $filterReasons['filterin']['id'] = $reasonsLimitation;
    }

    $offlineReasons = array_values(array_map(function($r) {
        return ['id' => $r->id, 'name' => $r->name, 'icon' => $r->icon, 'desc' => $r->description, 'mode' => (int)$r->mode];
    }, \LiveHelperChat\Models\LHCAbstract\OfflineReason::getList($filterReasons)));
}

$tpl->setArray(array(
    'user' => $user,
    'offline_reasons' => $offlineReasons
));

echo $tpl->fetch();
exit();

?>