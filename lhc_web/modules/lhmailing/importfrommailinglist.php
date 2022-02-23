<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/importfrommailinglist.tpl.php');

$campaign = erLhcoreClassModelMailconvMailingCampaign::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    $definition = array(
        'ml' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    $statistic = ['skipped' => 0, 'already_exists' => 0, 'imported' => 0, 'unassigned' => 0];

    if ($form->hasValidData( 'ml' ) && !empty($form->ml)) {
        foreach ($form->ml as $ml) {
            foreach (erLhcoreClassModelMailconvMailingListRecipient::getList(['limit' => false, 'filter' => ['mailing_list_id' => $ml]]) as $mailingRecipient) {

                if (isset($_POST['export_action']) && $_POST['export_action'] == 'unassign') {
                    if ($mailingRecipient->mailing_recipient instanceof erLhcoreClassModelMailconvMailingRecipient) {
                        foreach (erLhcoreClassModelMailconvMailingCampaignRecipient::getList(['filter' => ['campaign_id' => $campaign->id, 'email' => $mailingRecipient->mailing_recipient->email]]) as $campaignRecipient) {
                            $campaignRecipient->removeThis();
                            $statistic['unassigned']++;
                        }
                    }
                    continue;
                }

                if (!($mailingRecipient->mailing_recipient instanceof erLhcoreClassModelMailconvMailingRecipient) || $mailingRecipient->mailing_recipient->disabled == 1) {
                    $statistic['skipped']++;
                    continue;
                }

                if (erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['campaign_id' => $campaign->id, 'email' => $mailingRecipient->mailing_recipient->email]]) == 1) {
                    $statistic['already_exists']++;
                    continue;
                }

                $campaignRecipient = new erLhcoreClassModelMailconvMailingCampaignRecipient();
                $campaignRecipient->campaign_id = $campaign->id;
                $campaignRecipient->recipient_id = $mailingRecipient->mailing_recipient_id;
                $campaignRecipient->email = $mailingRecipient->mailing_recipient->email;
                $campaignRecipient->saveThis();

                $statistic['imported']++;
            }
        }

        $tpl->set('statistic', $statistic);
        $tpl->set('updated', true);

    } else {
        $tpl->set('errors', ['Please choose at-least one mailing list']);
    }

}

$tpl->set('item', $campaign);
$tpl->set('action_url', erLhcoreClassDesign::baseurl('mailing/importfrommailinglist') . '/' . $campaign->id);

echo $tpl->fetch();
exit;
