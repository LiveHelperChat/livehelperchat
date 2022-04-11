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
    erLhcoreClassModule::redirect('chat/list', '/(view)/'.$search->id.$append);
} else {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('views.editview', array(
        'search' => $search
    ));
}

exit;

?>