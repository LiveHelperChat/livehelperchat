<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Conversations');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel.tpl.php')); ?>

<?php if (isset($items)) : ?>
<form action="<?php echo $input->form_action,$inputAppend?>" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%">
        <thead>
        <tr>
            <th width="1%"><input class="mb-0" type="checkbox" ng-model="check_all_items" /></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Subject');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Sender');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Operator');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Department');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Data');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><input ng-checked="check_all_items" class="mb-0" type="checkbox" name="ConversationID[]" value="<?php echo $item->id?>" /></td>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/view')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->subject)?> <small><?php echo $item->total_messages?></small></a>
                </td>
                <td><?php echo htmlspecialchars($item->from_name)?> &lt;<?php echo $item->from_address?>&gt;</td>
                <td><?php echo htmlspecialchars($item->user)?></td>
                <td><?php echo htmlspecialchars($item->department)?></td>
                <td>
                    <?php if ($item->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>
                    Pending
                    <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) : ?>
                    Active
                    <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) : ?>
                    Closed
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $item->udate_front;?>
                </td>
                <td>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deleteconversation')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doClose" class="btn btn-warning" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Close selected');?>" />
        <input type="submit" name="doDelete" class="btn btn-danger" onclick="return confirm(confLH.transLation.delete_confirm)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete selected');?>" />
    </div>

</form>
<?php endif; ?>