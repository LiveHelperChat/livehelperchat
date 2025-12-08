<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Pending imports');?></h1>

<div class="row mb-2">
    <div class="col-12">
        <a class="btn btn-secondary btn-sm" href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'mailconv/manualimport'});return false;"><i class="material-icons">add</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Create manual import');?></a>
    </div>
</div>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailbox ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','UID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Attempt');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Created at');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Updated at');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td><?php echo $item->mailbox_id?></td>
                <td><?php echo $item->uid?></td>
                <td><?php 
                    if ($item->status == 0) {
                        echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Pending');
                    } elseif ($item->status == 1) {
                        echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Ignore');
                    }
                ?></td>
                <td><?php echo $item->attempt?></td>
                <td><?php echo date('Y-m-d H:i:s',$item->created_at)?></td>
                <td><?php echo date('Y-m-d H:i:s',$item->updated_at)?></td>
                <td nowrap>
                    <a class="btn btn-secondary btn-xs" href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'mailconv/manualimport/(id)/<?php echo $item->id?>'});return false;"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit');?></a>
                    <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/manualimport')?>/(id)/<?php echo $item->id?>/(action)/delete"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete');?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>
