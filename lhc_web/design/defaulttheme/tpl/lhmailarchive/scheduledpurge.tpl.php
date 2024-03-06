<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Scheduled archive and deletion');?></h1>

<table ng-non-bindable class="table table-sm" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','User ID');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archive ID');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Status');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Created At');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Updated At');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Started At');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Finished At');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Filter');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Pending records to process');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Last ID');?></th>
    </tr>
    </thead>
    <?php foreach ($items as $item) : ?>
        <tr>
            <td><?php echo $item->id?></td>
            <td><?php echo $item->user_id?></td>
            <td><?php echo $item->archive_id?></td>
            <td>
                <?php if ($item->status == \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_PENDING) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Pending');?>
                <?php elseif ($item->status == \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::STATUS_IN_PROGRESS) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','In progress');?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Finished');?>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $item->created_at_front;?>
            </td>
            <td>
                <?php echo $item->updated_at_front;?>
            </td>
            <td>
                <?php echo $item->started_at_front;?>
            </td>
            <td>
                <?php echo $item->finished_at_front;?>
            </td>
            <td>
                <a title="<?php echo htmlspecialchars($item->filter)?>" class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?><?php echo $item->filter_input_url?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','List mails');?></a>
            </td>
            <td>
                <?php echo $item->records_count?>, <?php echo $item->records_count_progress?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','of them in progress');?>
            </td>
            <td>
                <?php echo $item->last_id?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
