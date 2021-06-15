<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Sessions');?></h1>

<?php if (isset($items)) : ?>

    <table cellpadding="0" cellspacing="0" ng-non-bindable class="table table-sm" width="100%">
        <thead>
        <tr>
            <th width="1%">ID</th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Device type');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','User');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Created on');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Updated on');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td>
                    <i class="material-icons <?php if ($item->error == 0) : ?>chat-active<?php else : ?>chat-closed<?php endif;?>"><?php if ($item->error == 0) : ?>thumb_up_alt<?php else : ?>thumb_down_alt<?php endif; ?></i>
                    <?php if ($item->device_type == 1) : ?>
                        Android
                    <?php elseif ($item->device_type == 2) : ?>
                        iPhone
                    <?php else : ?>
                        Unknown
                    <?php endif;?>
                </td>
                <td><?php echo $item->user_id?></td>
                <td><?php echo $item->created_on_front?></td>
                <td><?php echo $item->updated_on_front?></td>
                <td nowrap>
                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                        <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('mobile/editsession')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE254;</i></a>
                        <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('mobile/deletesession')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif;?>