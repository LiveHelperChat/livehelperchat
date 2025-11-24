<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Edit');?></h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailing/editcampaign')?>/<?php echo $item->id?>" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <ul class="nav nav-tabs mb-3" role="tablist" data-remember="true">
        <li role="presentation" class="nav-item"><a href="#settings" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="settings" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Main');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_statistic') : ?> active<?php endif;?>" href="#statistic" aria-controls="options" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Statistic');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="settings">
            <?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/form_campaign.tpl.php'));?>
        </div>
        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_statistic') : ?>active<?php endif;?>" id="statistic">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Owner');?> - <?php echo htmlspecialchars((string)$item->user)?></p>
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Statistic');?></p>
            <ul>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Total recipients');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>"><?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['campaign_id' => $item->id]])?></a></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Total recipients pending');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>/(status)/<?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING?>"><?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['status' => erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING,'campaign_id' => $item->id]])?></a></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Total recipients send');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>/(status)/<?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::SEND?>"><?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['status' => erLhcoreClassModelMailconvMailingCampaignRecipient::SEND,'campaign_id' => $item->id]])?></a></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Total recipients failed');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>/(status)/<?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::FAILED?>"><?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['status' => erLhcoreClassModelMailconvMailingCampaignRecipient::FAILED,'campaign_id' => $item->id]])?></a></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Total recipients in progress');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>/(status)/<?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::IN_PROGRESS?>"><?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['status' => erLhcoreClassModelMailconvMailingCampaignRecipient::IN_PROGRESS,'campaign_id' => $item->id]])?></a></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Number of recipients who opened an e-mail');?> - <a href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>/(opened)/1"><?php echo erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['campaign_id' => $item->id], 'filtergt' => ['opened_at' => 0]])?></a></li>
            </ul>
        </div>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-sm btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <input type="submit" class="btn btn-sm btn-secondary" name="Update_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
        <input type="submit" class="btn btn-sm btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    </div>

</form>