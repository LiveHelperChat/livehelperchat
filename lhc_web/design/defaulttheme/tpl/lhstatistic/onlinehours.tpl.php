<h1>Online hours</h1>

<?php include(erLhcoreClassDesign::designtpl('lhstatistic/onlinehours/search_panel.tpl.php')); ?>

<table class="table table-hover table-sm" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Start activity');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Duration');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Chats served');?></th>
</tr>
</thead>
<?php $parentItem = null;?>

<?php foreach ($items as $item) : ?>
    <?php if (isset($input->user_id) && is_array($input->user_id) && !empty($input->user_id) && is_object($parentItem)) : ?>
        <tr>
            <td colspan="4">
            </td>
            <td colspan="1">
                <div class="text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Was offline for');?>"><b><?php echo erLhcoreClassChat::formatSeconds($parentItem->time - $item->lactivity)?></b></div>
            </td>
            <td>
                <?php if ( $item->chatsOffline > 0) : ?>
                <a class="text-danger" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_ids)/<?php echo $item->user_id?>/(timeto_seconds)/<?php echo date('s',$parentItem->time)?>/(timeto_minutes)/<?php echo date('i',$parentItem->time)?>/(timeto_hours)/<?php echo date('H',$parentItem->time)?>/(timeto)/<?php echo date('Y-m-d',$parentItem->time)?>/(timefrom)/<?php echo date('Y-m-d',$item->lactivity)?>/(timefrom_hours)/<?php echo date('H',$item->lactivity)?>/(timefrom_minutes)/<?php echo date('i',$item->lactivity)?>/(timefrom_seconds)/<?php echo date('s',$item->lactivity)?>" target="_blank"><span class="material-icons">open_in_new</span> <?php echo $item->chatsOffline?></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><?php echo htmlspecialchars($item->user_name)?></td>
        <td><?php echo htmlspecialchars($item->time_front)?></td>
        <td><?php echo htmlspecialchars($item->lactivity_front)?></td>
        <td><?php echo $item->duration_front?></td>
        <td>
            <?php if ( $item->chatsOnline > 0) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_ids)/<?php echo $item->user_id?>/(timeto_seconds)/<?php echo date('s',$item->lactivity)?>/(timeto_minutes)/<?php echo date('i',$item->lactivity)?>/(timeto_hours)/<?php echo date('H',$item->lactivity)?>/(timeto)/<?php echo date('Y-m-d',$item->lactivity)?>/(timefrom)/<?php echo date('Y-m-d',$item->time)?>/(timefrom_hours)/<?php echo date('H',$item->time)?>/(timefrom_minutes)/<?php echo date('i',$item->time)?>/(timefrom_seconds)/<?php echo date('s',$item->time)?>" target="_blank"><span class="material-icons">open_in_new</span> <?php echo $item->chatsOnline?></a>
            <?php endif; ?>
        </td>
    </tr>
<?php $parentItem = $item; endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<br />
