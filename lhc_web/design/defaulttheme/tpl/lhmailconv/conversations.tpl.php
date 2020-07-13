<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Ccnversations');?></h1>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%">
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Subject');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Sender');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Operator');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Department');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Status');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo htmlspecialchars($item->subject)?> <?php echo htmlspecialchars($item->ctime_front)?></td>
                <td><?php echo htmlspecialchars($item->sender)?></td>
                <td><?php echo htmlspecialchars($item->user)?></td>
                <td><?php echo htmlspecialchars($item->department)?></td>
                <td><?php echo htmlspecialchars($item->status)?></td>
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
<?php endif; ?>