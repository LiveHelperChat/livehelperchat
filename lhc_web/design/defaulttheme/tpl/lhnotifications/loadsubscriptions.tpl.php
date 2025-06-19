<table class="table table-sm">
    <thead>
    <tr>
        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','ID');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Device');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Registration time');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Update time');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Status');?></th>
        <th width="1%"></th>
        <th width="1%"></th>
    </tr>
    </thead>
    <?php foreach ($items as $notificationSubscriber) : ?>
        <tr>
            <td nowrap="nowrap">
                <?php echo $notificationSubscriber->id?>
            </td>
            <td><i class="material-icons"><?php echo ($notificationSubscriber->device_type == 0 ? 'computer' : ($notificationSubscriber->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($notificationSubscriber->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($notificationSubscriber->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?></td>
            <td nowrap="nowrap">
                <?php echo $notificationSubscriber->ctime_front?>
            </td>
            <td nowrap="nowrap">
                <?php echo $notificationSubscriber->utime_front?>
            </td>
            <td>
                <?php if ($notificationSubscriber->status == 0) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Active');?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','In-Active');?>
                <?php endif; ?>
            </td>
            <td><a class="btn btn-info btn-xs csfr-post csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('notifications/sendtest')?>/<?php echo $notificationSubscriber->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Test');?></a></td>
            <td><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('notifications/opdeletesubscribermy')?>/<?php echo $notificationSubscriber->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>