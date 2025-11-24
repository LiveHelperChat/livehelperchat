<?php if ($search->scope == 'mail') : ?>
    <?php if (!$list_mode) : ?>
        <div role="tabpanel" id="tabs" ng-cloak>
        <ul class="nav nav-pills" role="tablist" data-remember="true">
            <li role="presentation" class="nav-item"><a class="nav-link active" href="#chatlist" aria-controls="chatlist" role="tab" data-bs-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat list');?>">
                    <?php echo htmlspecialchars($search->name)?> </a>
            </li>
        </ul>
        <div class="tab-content mt-0" ng-cloak>
        <div role="tabpanel" class="tab-pane form-group active" id="chatlist">
        <div id="view-content-list">
    <?php endif; ?>

    <?php $dateFilterAttr = 'udate';?>
    <?php include(erLhcoreClassDesign::designtpl('lhviews/date_filter.tpl.php')); ?>

    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%">
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Subject');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Sender');?></th>
            <th width="15%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Operator');?></th>
            <th width="10%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Department');?></th>
            <th width="10%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Status');?></th>
            <th width="5%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Date');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/start_row.tpl.php')); ?>
            <td>

                <a onclick="lhc.previewMail(<?php echo $item->id?>);" class="material-icons">info_outline</a>

                <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($item->subject)?>" onclick="lhinst.startMailNewWindow(<?php echo $item->id?>,$(this).attr('data-title'))" >open_in_new</a>

                <?php if ($item->follow_up_id > 0) : ?>
                    <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Follow up e-mail');?>">follow_the_signs</span>
                <?php endif; ?>

                <?php if ($item->start_type == erLhcoreClassModelMailconvConversation::START_OUT) : ?>
                    <i class="material-icons">call_made</i>
                <?php else : ?>
                    <i class="material-icons">call_received</i>
                <?php endif; ?>

                <?php if ($item->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) : ?>
                    <span class="material-icons">attach_file</span><span class="material-icons">image</span>
                <?php elseif ($item->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) : ?>
                    <span class="material-icons">attach_file</span>
                <?php elseif ($item->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) : ?>
                    <span class="material-icons">image</span>
                <?php endif; ?>

                <span class="mr-2"><?php echo $item->id; ?></span>
            <?php if ($can_delete === true) : ?>
                          <a class="csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deleteconversation')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
            <?php endif; ?>
                <a class="user-select-none" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/view')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->subject)?>&nbsp;<small><?php echo $item->total_messages?></small></a>

                <?php if (is_array($item->subjects)) : ?>
                    <?php foreach ($item->subjects as $subject) : ?>
                        <span class="badge bg-info mx-1" ng-non-bindable <?php if ($subject->color != '') : ?>style="background-color:#<?php echo htmlspecialchars($subject->color)?>!important;" <?php endif;?> ><?php echo htmlspecialchars($subject)?></span>
                    <?php endforeach; ?>
                <?php endif; ?>

            </td>
            <td><?php echo htmlspecialchars($item->from_name)?> &lt;<?php echo $item->from_address?>&gt;</td>
            <td nowrap="nowrap"><?php echo htmlspecialchars($item->user)?></td>
            <td nowrap="nowrap">
                <?php echo htmlspecialchars($item->department),', ',htmlspecialchars($item->mailbox_front['mail'])?>
            </td>
            <td nowrap="">
                <?php if ($item->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>
                    <i class="material-icons chat-pending">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','New');?>
                <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) : ?>
                    <i class="material-icons chat-active">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Active');?>
                <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) : ?>
                    <i class="material-icons chat-closed">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Closed');?>
                <?php endif; ?>
            </td>
            <td width="1%" nowrap="nowrap" title="<?php echo $item->udate_front;?>">
                <?php echo $item->last_mail_front;?>  
            </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (!$list_mode) : ?>
        </div>
        </div>
        </div>
        </div>
    <?php endif; ?>

<?php else : ?>

<?php endif; ?>
