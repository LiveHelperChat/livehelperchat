<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Campaigns list');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/search_panel_mailinglist.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Recipients');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr class="<?php if ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_FINISHED) : ?>text-muted<?php endif;?>">
                <td><?php echo $item->id?></td>
                <td>
                    <a class="csfr-post text-muted csfr-required" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Copy campaign');?>" href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaign')?>/(action)/copy/(id)/<?php echo $item->id?>" ><i class="material-icons mr-0">content_copy</i></a>
                    <a class="<?php if ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_FINISHED) : ?>text-muted<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('mailing/editcampaign')?>/<?php echo $item->id?>" ><?php echo htmlspecialchars($item->name)?></a>
                </td>
                <td>
                    <?php if ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_PENDING) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending');?>
                    <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_IN_PROGRESS) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress');?>
                    <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_FINISHED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Finished');?>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="<?php if ($item->status == erLhcoreClassModelMailconvMailingCampaign::STATUS_FINISHED) : ?>text-muted<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaignrecipient')?>/(campaign)/<?php echo $item->id?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','List of recipients');?></a>
                </td>
                <td>
                    <a class="text-danger csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/deletecampaign')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/newcampaign')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>