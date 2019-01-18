<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/list','Subscribers list')?></h1>

<?php if (isset($items)) : ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Chat ID');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Department');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Theme');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Device');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','IP');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Registration time');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('notification/list','Update time');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td nowrap="nowrap">
                    <?php echo htmlspecialchars($item->chat_id)?>
                </td>
                <td nowrap="nowrap">
                    <?php echo htmlspecialchars($item->department)?>
                </td>
                <td nowrap="nowrap">
                    <?php echo htmlspecialchars($item->theme)?>
                </td>
                <td nowrap="nowrap">
                    <i class="material-icons" title="<?php echo htmlspecialchars($item->uagent)?>"><?php echo ($item->device_type == 0 ? 'computer' : ($item->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($item->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($item->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?>
                </td>
                <td nowrap="nowrap">
                    <?php echo $item->ip?>
                </td>
                <td nowrap="nowrap">
                    <?php echo $item->ctime_front?>
                </td>
                <td nowrap="nowrap">
                    <?php echo $item->utime_front?>
                </td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('notifications/editsubscriber')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('notifications/deletesubscriber')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>