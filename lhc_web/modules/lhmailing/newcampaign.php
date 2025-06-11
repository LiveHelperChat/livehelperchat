<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newcampaign.tpl.php');

$item = new erLhcoreClassModelMailconvMailingCampaign();

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailing/campaign');
        exit;
    }

    $items = array();

    $Errors = erLhcoreClassMailconvMailingValidator::validateCampaign($item);

    if (count($Errors) == 0) {
        try {
            $item->user_id = $currentUser->getUserID();
            $item->saveThis();

            if (isset($_POST['Save_continue'])) {
                erLhcoreClassModule::redirect('mailing/campaignrecipient','/(campaign)/' . $item->id);
            } else {
                erLhcoreClassModule::redirect('mailing/campaign');
            }

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
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJSStatic('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailing/campaign'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Campaigns')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>