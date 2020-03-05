<h1>Online hours</h1>

<?php include(erLhcoreClassDesign::designtpl('lhstatistic/onlinehours/search_panel.tpl.php')); ?>

<table class="table" cellpadding="0" cellspacing="0" width="100%">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Username');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Start activity');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Last activity');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Duration');?></th>
</tr>
</thead>
<?php $parentItem = null;?>

<?php foreach ($items as $item) : ?>
    <?php if (isset($input->user_id) && $input->user_id > 0 && is_object($parentItem)) : ?>
        <tr>

            <td colspan="4">
            </td>
            <td colspan="1">
                <div class="text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Was offline for');?>"><b><?php echo erLhcoreClassChat::formatSeconds($parentItem->time - $item->lactivity)?></b></div>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td><?php echo htmlspecialchars($item->user_name)?></td>
        <td><?php echo htmlspecialchars($item->time_front)?></td>
        <td><?php echo htmlspecialchars($item->lactivity_front)?></td>
        <td><?php echo $item->duration_front?></td>
    </tr>
<?php $parentItem = $item; endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
<br />
