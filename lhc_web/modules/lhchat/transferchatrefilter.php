<?php 

$definition = array (
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

$form = new ezcInputForm( INPUT_POST, $definition );
$Errors = array();

if ( $form->hasValidData( 'dep_transfer_only_explicit' ) && $form->dep_transfer_only_explicit == true ) {
    $explicit = true;
} else {
    $explicit = false;
}

if ( $form->hasValidData( 'dep_transfer_exclude_hidden' ) && $form->dep_transfer_exclude_hidden == true ) {
    $filter['filter']['hidden'] = 0;
}

if ( $form->hasValidData( 'dep_transfer_exclude_disabled' ) && $form->dep_transfer_exclude_disabled == true ) {
    $filter['filter']['disabled'] = 0;
}

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/transferchatrefilter.tpl.php');
$tpl->set('departments_filter',array(
    'filter' => $filter,
    'explicit' => $explicit,
    'dep_id' => $chat->dep_id,
    'chat_id' => $chat->id
));

echo $tpl->fetch();

exit;
?>