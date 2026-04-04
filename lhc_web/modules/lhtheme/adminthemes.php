<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/adminthemes.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('theme/adminthemes');
$pages->items_total = erLhAbstractModelAdminTheme::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = [];
if ($pages->items_total > 0) {
    $items = erLhAbstractModelAdminTheme::getList([
        'offset' => $pages->low,
        'limit' => $pages->items_per_page
    ]);
}

$tpl->set('items', $items);
$tpl->set('pages', $pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit', 'System configuration')],
    [
        'url' => erLhcoreClassDesign::baseurl('theme/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index', 'Themes')
    ],
    [
        'url' => erLhcoreClassDesign::baseurl('theme/adminthemes'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list', 'Admin themes')
    ]
];
