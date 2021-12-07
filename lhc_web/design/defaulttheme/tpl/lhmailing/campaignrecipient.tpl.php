<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Campaign recipient');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/search_panel_campaign_recipient.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Recipient');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Type');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($item->recipient)?>
                </td>
                <td>
                    <?php if ($item->status == erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING) : ?>
                    Pending
                    <?php else : ?>
                    Send
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->type == erLhcoreClassModelMailconvMailingCampaignRecipient::TYPE_MANUAL) : ?>
                        Manual
                    <?php else : ?>
                        Based on recipient list
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('mailing/deletecampaignrecipient')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

