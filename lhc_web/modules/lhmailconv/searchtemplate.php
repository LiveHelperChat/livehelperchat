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

if (count($filter) > 0) {
    $q->where($filter);
}

$q->limit(10, 0);
$q->orderBy('name ASC');
$items = $session->find($q);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.replace_variables', array(
    'items' => & $items,
    'dep_id' => $Params['user_parameters']['id']
));

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/searchtemplate.tpl.php');
$tpl->set('items', $items);

echo $tpl->fetch();
exit;

?>