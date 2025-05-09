<?php
$category = $opActionsParams['scope'] == 'mail' ? ['mail_open','mail_view'] : ['chat_view','chat_open'];
$filter = ['filterin' => ['category' => $category],'filter' => ['object_id' => $opActionsParams['object_id']]];
$itemsActions = erLhAbstractModelAudit::getList(array_merge_recursive($filter,array('offset' => 0, 'limit' => 1000)));
?>

<table class="table table-condensed table-small" cellpadding="0" cellspacing="0" ng-non-bindable>
    <thead>
    <tr>
        <th width="1%" nowrap="">[Record ID]</th>
        <th width="1%" nowrap="">[User ID]</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Category');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Time');?></th>
    </tr>
    </thead>
<?php foreach ($itemsActions as $itemAction) : ?>
    <tr>
        <td>
            <?php echo $itemAction->id;?>
        </td>
        <td nowrap="">
            <?php if ($itemAction->user_id > 0) : ?>
                <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $itemAction->user_id;?>">[<?php echo $itemAction->user_id;?>] <?php echo htmlspecialchars($itemAction->plain_user_name);?></a>
            <?php endif; ?>
        </td>
        <td>
            <?php echo htmlspecialchars($itemAction->category); ?>
        </td>
        <td>
            <?php echo htmlspecialchars($itemAction->time); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>


