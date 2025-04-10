<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/list','Operators subscribers list')?></h1>

<?php if (isset($items)) : ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','ID');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','User');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Device');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Registration time');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Update time');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Status');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td nowrap="nowrap">
                    <?php echo $item->id?>
                </td>
                <td nowrap="nowrap">
                    [<?php echo $item->user_id?>] <?php echo htmlspecialchars($item->n_official)?>
                </td>
                <td nowrap="nowrap">
                    <i class="material-icons" title="<?php echo htmlspecialchars($item->uagent)?>"><?php echo ($item->device_type == 0 ? 'computer' : ($item->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($item->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($item->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?>
                </td>
                <td nowrap="nowrap">
                    <?php echo $item->ctime_front?>
                </td>
                <td nowrap="nowrap">
                    <?php echo $item->utime_front?>
                </td>
                <td>
                    <?php if ($item->status == 0) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Active');?>
                    <?php else : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','In-Active');?>
                    <?php endif; ?>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('notifications/editsubscriberop')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('notifications/opdeletesubscriber')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>