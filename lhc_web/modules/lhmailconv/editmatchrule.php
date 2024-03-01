<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/editmatchrule.tpl.php');

$item =  erLhcoreClassModelMailconvMatchRule::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailconv/matchingrules');
        exit;
    }

    if (isset($_POST['Cancel_page'])) {
        erLhcoreClassModule::redirect('mailconv/matchingrules');
        exit ;
    }

    $Errors = erLhcoreClassMailconvValidator::validateMatchRule($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            if (isset($_POST['Update_page'])) {
                $tpl->set('updated',true);
            } else {
                erLhcoreClassModule::redirect('mailconv/matchingrules');
                exit;
            }

        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->setArray(array(
    'item' => $item,
));

$Result['require_angular'] = true;
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.webhooks.js').'"></script>';
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
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Edit')
    )
);

?>