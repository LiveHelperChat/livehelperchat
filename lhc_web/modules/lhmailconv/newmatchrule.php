<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/newmatchrule.tpl.php');

$item = new erLhcoreClassModelMailconvMatchRule();

if (isset($_POST['Cancel_page'])) {
    erLhcoreClassModule::redirect('mailconv/matchingrules');
    exit ;
}

if (ezcInputForm::hasPostData()) {

    $items = array();

    $Errors = erLhcoreClassMailconvValidator::validateMatchRule($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            erLhcoreClassModule::redirect('mailconv/matchingrules');
            exit;
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }

    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/matchingrules'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Matching rules')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>