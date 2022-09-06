<?php

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);

if ($search->user_id != $currentUser->getUserID()) {
    erLhcoreClassModule::redirect('/');
    exit;
}

if (isset($search->params_array['input_form']['view'])) {
    unset($search->params_array['input_form']['view']);
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($search->params_array['input_form']);

if ($search->scope == 'chat') {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/export_config.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/list') . $append);
    echo $tpl->fetch();
    exit;
} else {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('views.export', array(
        'search' => $search
    ));
}

exit;

?>