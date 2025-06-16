<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailbox');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/search_panel_mailbox.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailbox');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Import progress');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Import priority');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Active');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php if ($item->failed == 1) : ?>
                        <i class="material-icons text-danger">warning</i>
                    <?php endif; ?>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>" ><?php echo htmlspecialchars($item->mail)?></a>
                </td>
                <td>
                    <?php if ($item->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS) : ?>
                        <?php echo $item->sync_started_ago;?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress');?>
                    <?php else : ?>
                        <?php echo erLhcoreClassChat::formatSeconds($item->last_sync_time - $item->sync_started)?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Finished');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $item->import_priority?>
                </td>
                <td>
                    <?php if ($item->active == 1) : ?>
                        <i title="Ok" class="material-icons chat-active">&#xE5CA;</i>
                    <?php else : ?>
                        <i title="Blocked" class="material-icons chat-closed">&#xE14B;</i>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE254;</i></a>
                        <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deletemailbox')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE872;</i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

<div class="btn-group btn-group-sm">
    <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/newmailbox')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
    <a class="btn btn-warning csfr-required csfr-post" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/mailbox')?>/(resetstatus)/reset<?php echo $inputAppend?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Reset status');?></a>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>