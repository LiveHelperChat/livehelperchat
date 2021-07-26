<?php

$keyword = rawurldecode($_GET['q']);

$session = erLhcoreClassMailconv::getSession();
$q = $session->createFindQuery('erLhcoreClassModelMailconvResponseTemplate');

$filter = array();
$items = array();

// Extension did not changed any filters, use default
//$filter[] = $q->expr->lOr($q->expr->eq('department_id', $q->bindValue($department_id)), $q->expr->lAnd($q->expr->eq('department_id', $q->bindValue(0)), $q->expr->eq('user_id', $q->bindValue(0))), $q->expr->eq('user_id', $q->bindValue($user_id)));

if ($keyword != '') {
    $filter[] = $q->expr->lOr(
        $q->expr->like('name', $q->bindValue('%' . $keyword . '%')),
        $q->expr->like('template_plain', $q->bindValue('%' . $keyword . '%'))
    );
}

$q->where($filter);

$q->limit(10, 0);
$q->orderBy('name ASC');
$items = $session->find($q);


$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/searchtemplate.tpl.php');
$tpl->set('items', $items);

echo $tpl->fetch();
exit;

?>