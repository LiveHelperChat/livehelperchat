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
                <?php if ($item->records_count == 0) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Finished');?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Finished collecting');?>
                <?php endif; ?>
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
            <a title="<?php echo htmlspecialchars($item->filter)?>" class="btn btn-secondary btn-xs" href="<?php echo $item->filter_input_url?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','List mails');?></a>
        </td>
        <td>
            <?php echo $item->records_count?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','found');?>, <?php echo $item->records_count_progress?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','of them in progress');?>, <?php echo $item->processed_records?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','processed');?>
        </td>
        <td>
            <?php echo $item->last_id?>
        </td>
    </tr>
<?php endforeach; ?>