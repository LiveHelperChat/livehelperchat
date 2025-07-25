<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/cannedmsg.tpl.php'));?>

<?php if (isset($messsages_error)) : $errors = [$messsages_error];?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($messsages_copied) || isset($messsages_skipped)) : ?>
    <div role="alert" class="alert alert-success alert-dismissible fade show" ng-non-bindable>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <ul class="my-0 pl-3">
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Copied');?> - <?php echo $messsages_copied?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Skipped');?> - <?php echo $messsages_skipped?></li>
        </ul>
    </div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li role="presentation" class="nav-item"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>" class="nav-link<?php if ($tab == '' || $tab == 'cannedmsg') : ?> active<?php endif;?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Canned messages');?></a></li>
    <li role="presentation" class="nav-item"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>/(tab)/statistic" class="nav-link<?php if ($tab == 'statistic') : ?> active<?php endif;?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Statistic');?></a></li>
</ul>

<div class="tab-content">
    <?php if ($tab == '' || $tab == 'cannedmsg') : ?>
    <div role="tabpanel" class="tab-pane active" id="cannedmsg">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/search_panel.tpl.php')); ?>
        <br/>
        <form action="<?php echo $input->form_action . $inputAppend?>" method="post">
        <table class="table table-sm" cellpadding="0" cellspacing="0" ng-non-bindable>
            <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" onclick="$('.canned-item-id').prop('checked',$(this).is(':checked'))">
                </th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title/Message');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','User');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Auto send');?></th>
                <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Updated at');?></th>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_column_multiinclude.tpl.php'));?>
                <th width="1%">&nbsp;</th>
                <th width="1%">&nbsp;</th>
            </tr>
            </thead>
            <?php foreach ($items as $item) : ?>
                <tr class="<?php $item->disabled == 1 ? print 'text-muted' : ''?>">
                    <td>
                        <input class="canned-item-id" type="checkbox" name="canned_id[]" value="<?php echo $item->id?>">
                    </td>
                    <td title="<?php echo htmlspecialchars($item->unique_id)?>">
                        <?php if ($item->disabled == 1) : ?><i class="text-danger material-icons">block</i><?php endif; ?>
                        <?php echo nl2br(htmlspecialchars($item->title != '' ? $item->title : $item->msg))?>
                    </td>
                    <td>
                        <?php if ($item->department !== false) : ?><?php echo htmlspecialchars($item->department)?><?php endif;$item->department_ids_front; if (!empty($item->department_ids_front)) : $deps = implode(', ',erLhcoreClassModelDepartament::getList(['filterin' => ['id' => $item->department_ids_front]]))?>
                        <span title="<?php echo htmlspecialchars($deps);?>">
                            <?php echo erLhcoreClassDesign::shrt($deps, 50, '...', 30, ENT_QUOTES);?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item->user)?></td>
                    <td><?php echo $item->delay?></td>
                    <td><?php echo $item->position?></td>
                    <td><?php echo $item->auto_send?></td>
                    <td nowrap="nowrap" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Created at');?> - <?php echo $item->created_at_front?>"><?php echo $item->updated_at_front?></td>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_column_content_multiinclude.tpl.php'));?>
                    <td nowrap>

                        <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsgedit')?>/<?php echo $item->id?>">
                        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','administratecannedmsg')) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit message');?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','See details');?>
                        <?php endif; ?>
                        </a>

                    </td>
                    <td nowrap>
                        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','administratecannedmsg')) : ?>
                            <a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>/(action)/delete/(id)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete message');?></a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

        <?php if (isset($pages)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
        <?php endif;?>

        <div class="btn-group" role="group" aria-label="...">
            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','administratecannedmsg')) : ?>

            <a class="btn btn-sm btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('chat/newcannedmsg')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','New canned message');?></a>
            <button type="button" onclick="lhc.confirmDelete($(this))" name="DeleteSelected" class="btn btn-sm btn-danger"><span class="material-icons">delete</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete selected');?></button>
            <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                <button type="button" onclick="return lhc.revealModal({'title' : 'Delete all', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/4?<?php echo $appendPrintExportURL?>'})" class="btn btn-danger btn-sm"><span class="material-icons">delete_sweep</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete all items')?> (<?php echo $pages->items_total?>)</button>
            <?php endif; ?>

            <?php endif; ?>
            <button name="CopyAsEmailTemplates" type="submit" class="btn btn-sm btn-secondary"><span class="material-icons">content_copy</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Copy selected as e-mail templates');?></button>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

        </form>

    </div>
    <?php endif; ?>

    <?php if ($tab == 'statistic') : ?>
    <div role="tabpanel" class="tab-pane active" id="statistic">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/search_panel_statistic.tpl.php')); ?>

        <p><small>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','By default last 30 days statistic is shown.')?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/status_tracking.tpl.php')); ?>
            </small>
        </p>

        <table class="table table-sm" cellpadding="0" cellspacing="0" ng-non-bindable>
            <thead>
            <tr>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title/Message');?></th>
                <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Used');?></th>
            </tr>
            </thead>
            <?php foreach ($items as $item) : ?>
               <tr>
                   <td>
                       <a href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsgedit')?>/<?php echo $item['canned_id']?>"><?php echo htmlspecialchars(erLhcoreClassModelCannedMsg::fetch($item['canned_id']))?></a>
                   </td>
                   <td>
                       <?php echo $item['number_of_chats']?>
                   </td>
               </tr>
             <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

</div>

