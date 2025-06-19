<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Campaign recipient');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailing/parts/search_panel_campaign_recipient.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Recipient');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send at');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Type');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Seen');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Ticket');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td>
                    <button data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Copied');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Click to copy');?>" class="mx-0 btn btn-xs btn-link text-muted py-1" data-copy="<?php echo htmlspecialchars($item->recipient)?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons mr-0">content_copy</i></button>
                    <?php if ($item->type == erLhcoreClassModelMailconvMailingCampaignRecipient::TYPE_MANUAL) : ?>
                        <button class="p-0 m-0 btn btn-sm btn-link" href="#" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/newcampaignrecipient')?>/<?php echo $campaign->id?>/<?php echo $item->id?>'})"><?php echo htmlspecialchars($item->recipient)?></button>
                    <?php else : ?>
                        <button class="m-0 p-0 btn btn-sm btn-link" href="#" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url': WWW_DIR_JAVASCRIPT + '/mailing/editmailingrecipient/<?php echo $item->recipient_id?>'})"><?php echo htmlspecialchars($item->recipient)?></button>
                    <?php endif; ?>&nbsp;

                    <?php if ($item->mailbox_front != '') : ?>
                        <small class="pl-2 text-muted"><span class="material-icons">mail_outline</span><?php echo htmlspecialchars($item->mailbox_front)?></small>
                    <?php endif; ?>

                    <a class="csfr-post csfr-required text-muted border rounded px-1" href="<?php echo erLhcoreClassDesign::baseurl('mailing/sendtestemail')?>/<?php echo $item->id?>" data-trans="delete_confirm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send test e-mail');?></a>
                </td>
                <td>
                    <?php if ($item->send_at > 0) : ?><?php echo $item->send_at_front?><?php endif;?>
                </td>
                <td>
                    <?php if ($item->status == erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending');?>...
                    <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaignRecipient::IN_PROGRESS) : ?>
                    <span class="text-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress');?>...</span>
                    <?php elseif ($item->status == erLhcoreClassModelMailconvMailingCampaignRecipient::FAILED) : ?>
                    <span class="text-danger action-image" onclick="return lhc.revealModal({'title' : 'Details', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/detailssend')?>/<?php echo $item->id?>'})"><i class="material-icons">sms_failed</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Failed');?>...</span>
                    <?php else : ?>
                    <span class="text-success action-image" onclick="return lhc.revealModal({'title' : 'Details', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('mailing/detailssend')?>/<?php echo $item->id?>'})"><i class="material-icons">done</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send');?>...</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->type == erLhcoreClassModelMailconvMailingCampaignRecipient::TYPE_MANUAL) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Manual');?>
                    <?php else : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Based on recipient list');?>
                    <?php endif; ?>
                </td>
                <td>
                    <span title="<?php if ($item->opened_at == 0) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail was not opened yet!') ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail was opened first time at') ?> <?php echo$item->opened_at_front?><?php endif;?>" class="material-icons<?php $item->opened_at == 0 ? print ' text-muted' : print ' text-success'?>">visibility</span>
                </td>
                <td>
                    <?php if ($item->conversation_id > 0) : ?>
                        <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('front/default')?>/(mid)/<?php echo $item->conversation_id?>/#!#chat-id-mc<?php echo $item->conversation_id?>"><span class="material-icons">open_in_new</span><?php echo $item->conversation_id?></a>
                    <?php else : ?>
                        <span class="text-muted"><span class="material-icons">autorenew</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending');?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="text-danger csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailing/deletecampaignrecipient')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

