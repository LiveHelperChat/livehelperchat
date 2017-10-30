<?php

if ($Params['user_parameters_unordered']['mode'] == '' || $Params['user_parameters_unordered']['mode'] == 'dep') {
    $definition = array(
        'dep_transfer_only_explicit' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'dep_transfer_exclude_hidden' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'dep_transfer_exclude_disabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    $filter = array();
    $filter['sort'] = 'sort_priority ASC, name ASC';

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();

    if ($form->hasValidData('dep_transfer_only_explicit') && $form->dep_transfer_only_explicit == true) {
        $explicit = true;
    } else {
        $explicit = false;
    }

    if ($form->hasValidData('dep_transfer_exclude_hidden') && $form->dep_transfer_exclude_hidden == true) {
        $filter['filter']['hidden'] = 0;
    }

    if ($form->hasValidData('dep_transfer_exclude_disabled') && $form->dep_transfer_exclude_disabled == true) {
        $filter['filter']['disabled'] = 0;
    }

    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

    $tpl = erLhcoreClassTemplate::getInstance('lhchat/transfer/transferchatrefilter.tpl.php');
    $tpl->set('departments_filter', array(
        'filter' => $filter,
        'explicit' => $explicit,
        'dep_id' => $chat->dep_id,
        'chat_id' => $chat->id
    ));

    echo $tpl->fetch();
} elseif ($Params['user_parameters_unordered']['mode'] == 'user') {

    $definition = array(
        'logged_and_online' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    $form = new ezcInputForm(INPUT_POST, $definition);

    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

    $tpl = erLhcoreClassTemplate::getInstance('lhchat/transfer/transferchatrefilteruser.tpl.php');
    $userFilter = array();

    if ($form->hasValidData('logged_and_online') && $form->logged_and_online == true) {
        $userFilter['hide_online'] = 0;
    }

    erLhcoreClassUser::instance();
    $tpl->set('user_id',$currentUser->getUserID());
    $tpl->set('user_filter', $userFilter);
    $tpl->set('chat', $chat);

    echo $tpl->fetch();
}

exit;
?>