<?php

$keyword = rawurldecode($_GET['q']);

$session = erLhcoreClassMailconv::getSession();
$q = $session->createFindQuery('erLhcoreClassModelMailconvResponseTemplate');

$filter = array();
$items = array();

// No chosen department means all
// Or the one

if ((int) $Params['user_parameters']['id'] > 0) {
    $filter[] = $q->expr->lOr(
        $q->expr->eq('dep_id', $q->bindValue(0)),
        'id IN (SELECT template_id FROM lhc_mailconv_response_template_dep WHERE dep_id = ' . (int) $Params['user_parameters']['id'] . ')'
    );
}

if ($keyword != '') {
    $filter[] = $q->expr->lOr(
        $q->expr->like('name', $q->bindValue('%' . $keyword . '%')),
        $q->expr->like('template_plain', $q->bindValue('%' . $keyword . '%')),
        $q->expr->like('template', $q->bindValue('%' . $keyword . '%'))
    );
}

$filter[] = $q->expr->eq('disabled', 0);

$limitation = erLhcoreClassChat::getDepartmentLimitation('lhc_mailconv_response_template_dep', ['check_list_scope' => 'mails']);

if ($limitation !== false) {
    if ($limitation !== true) {
        $filter[] = $q->expr->lOr(
            $q->expr->eq('dep_id', $q->bindValue(0)),
            'id IN (SELECT template_id FROM lhc_mailconv_response_template_dep WHERE ' . $limitation . ')'
        );
    }
} else {
    $filter[] = '1 = -1';
}

if (count($filter) > 0) {
    $q->where($filter);
}

$q->limit(50, 0);
$q->orderBy('name ASC');
$items = $session->find($q);

$searchArray = [
    '{{operator}}',
    '{operator}',
    '{{department}}',
    '{department}'
];

$replaceArray = [
    $currentUser->getUserData()->name_official,
    $currentUser->getUserData()->name_official,
    ((int) $Params['user_parameters']['id'] > 0 ? (string)erLhcoreClassModelDepartament::fetch($Params['user_parameters']['id'], true) : ''),
    ((int) $Params['user_parameters']['id'] > 0 ? (string)erLhcoreClassModelDepartament::fetch($Params['user_parameters']['id'], true) : '')
];

$conv = isset($_GET['c']) && is_numeric($_GET['c']) ? erLhcoreClassModelMailconvConversation::fetch((int)$_GET['c']) : null;
$message = isset($_GET['m']) && is_numeric($_GET['m']) ? erLhcoreClassModelMailconvMessage::fetch((int)$_GET['m']) : null;

foreach ($items as $item) {
    $item->template = str_replace($searchArray,$replaceArray, $item->template);
    $item->template_plain = str_replace($searchArray,$replaceArray, $item->template_plain);

    if ($conv instanceof erLhcoreClassModelMailconvConversation) {
        $item->template = erLhcoreClassGenericBotWorkflow::translateMessage($item->template, array('chat' => $conv, 'args' => ['current_user' => $currentUser->getUserData(), 'mail' => $conv, 'msg' => $message, 'chat' => $conv]));
        $item->template_plain = erLhcoreClassGenericBotWorkflow::translateMessage($item->template_plain, array('chat' => $conv, 'args' => ['current_user' => $currentUser->getUserData(), 'mail' => $conv, 'msg' => $message, 'chat' => $conv]));
    } else {
        $conv = new erLhcoreClassModelMailconvConversation();
        if ((int) $Params['user_parameters']['id'] > 0) {
            $conv->dep_id = (int) $Params['user_parameters']['id'];
        }
        $item->template = erLhcoreClassGenericBotWorkflow::translateMessage($item->template, array('chat' => $conv, 'args' => ['mail' => $conv, 'current_user' => $currentUser->getUserData()]));
        $item->template_plain = erLhcoreClassGenericBotWorkflow::translateMessage($item->template_plain, array('chat' => $conv, 'args' => ['mail' => $conv, 'current_user' => $currentUser->getUserData()]));
    }
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.replace_variables', array(
    'items' => & $items,
    'dep_id' => $Params['user_parameters']['id']
));

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/searchtemplate.tpl.php');
$tpl->set('items', $items);

echo $tpl->fetch();
exit;

?>