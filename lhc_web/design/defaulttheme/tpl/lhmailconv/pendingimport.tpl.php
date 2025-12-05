<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Pending imports');?></h1>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailbox ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','UID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Attempt');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Last failure');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Created at');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Updated at');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td><?php echo $item->mailbox_id?></td>
                <td><?php echo $item->uid?></td>
                <td><?php echo $item->status?></td>
                <td><?php echo $item->attempt?></td>
                <td><?php echo htmlspecialchars($item->last_failure)?></td>
                <td><?php echo date('Y-m-d H:i:s',$item->created_at)?></td>
                <td><?php echo date('Y-m-d H:i:s',$item->updated_at)?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>
