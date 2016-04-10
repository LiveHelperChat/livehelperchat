<?php
$tpl = erLhcoreClassTemplate::getInstance('lhtheme/adminthemes.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('theme/adminthemes');
$pages->items_total = erLhAbstractModelAdminTheme::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhAbstractModelAdminTheme::getList(array(
        'offset' => $pages->low,
        'limit' => $pages->items_per_page
    ));
}

$tpl->set('items', $items);
$tpl->set('pages', $pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('theme/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index', 'Themes')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('theme/adminthemes'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list', 'Admin themes')
    )
);