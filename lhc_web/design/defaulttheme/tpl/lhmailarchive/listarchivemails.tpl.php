<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/listarchivechats','Archived mails');?> (<?php echo htmlspecialchars($archive->range_from_front)?> - <?php echo htmlspecialchars($archive->range_to_front)?>)</h1>

<div id="tabs" role="tabpanel">

    <ul class="nav nav-pills" role="tablist">
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#chats" aria-controls="chats" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Mails');?></a></li>
    </ul>

    <div class="tab-content ps-2">
        <div role="tabpanel" class="tab-pane active" id="chats">

<?php $is_archive_mode = true; ?>
<?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel.tpl.php')); ?>

<?php if ($pages->items_total > 0) { ?>

    <table ng-non-bindable class="table table-sm list-links" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><input class="mb-0" type="checkbox" ng-model="check_all_items" /></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Subject');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Sender');?></th>
                <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/additional_chat_column.tpl.php'));?>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Priority');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Operator');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Department');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Status');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Date');?></th>
                <?php if ($can_delete === true) : ?>
                    <th width="1%"></th>
                <?php endif; ?>
            </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td>

                    <?php if ($item->opened_at > 0) : ?>
                        <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Message was seen by customer first time at');?>: <?php echo $item->opened_at_front?>">visibility</span>
                    <?php endif; ?>

                    <?php if ($item->lang != '') : ?>
                        <img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $item->lang?>.png" alt="<?php echo htmlspecialchars($item->lang)?>" title="<?php echo htmlspecialchars($item->lang)?>" />
                    <?php endif; ?>

                    <?php if ($item->undelivered == 1) : ?>
                        <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Undelivered e-mail');?>" class="text-danger material-icons">sms_failed</span>
                    <?php endif; ?>

                    <a id="preview-item-<?php echo $item->id?>" data-list-navigate="true" onclick="lhc.previewMail(<?php echo $item->id?>,this);" class="material-icons">info_outline</a>

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

                    <a class="me-2" onclick='lhinst.startMailChat(<?php echo $item->id?>,$("#tabs"),<?php echo json_encode($item->subject_front,JSON_HEX_APOS)?>)' href="#!#chat-id-mc<?php echo $item->id?>"><?php echo $item->id; ?></a>

                    <a class="user-select-none" onclick='lhinst.startMailChat(<?php echo $item->id?>,$("#tabs"),<?php echo json_encode($item->subject_front,JSON_HEX_APOS)?>)' href="#!#chat-id-mc<?php echo $item->id?>"><?php echo htmlspecialchars($item->subject)?>&nbsp;<small><?php echo $item->total_messages?></small></a>

                    <?php if (is_array($item->subjects)) : ?>
                        <?php foreach ($item->subjects as $subject) : ?>
                            <span class="badge bg-info mx-1" ng-non-bindable><?php echo htmlspecialchars($subject)?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </td>
                <td ng-non-bindable><?php echo htmlspecialchars($item->from_name)?> &lt;<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) : ?><?php echo htmlspecialchars($item->from_address)?><?php else : ?><?php echo htmlspecialchars(\LiveHelperChat\Helpers\Anonymizer::maskEmail($item->from_address))?><?php endif;?>&gt;</td>
                <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/additional_chat_column_row.tpl.php'));?>
                <td><?php echo htmlspecialchars($item->priority)?></td>
                <td ng-non-bindable><?php echo htmlspecialchars($item->user)?></td>
                <td nowrap="nowrap" ng-non-bindable>
                    <?php echo htmlspecialchars($item->department),', ',htmlspecialchars($item->mailbox_front['mail'])?>
                </td>
                <td ng-non-bindable nowrap>
                    <?php if ($item->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>
                        <i class="material-icons chat-pending">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','New');?>
                    <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) : ?>
                        <i class="material-icons chat-active">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Active');?>
                    <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) : ?>
                        <i class="material-icons chat-closed">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Closed');?>
                    <?php endif; ?>
                </td>
                <td nowrap ng-non-bindable title="<?php echo $item->udate_front_ago;?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','ago');?>">
                    <?php echo $item->udate_front;?>
                </td>
                <?php if ($can_delete === true) : ?>
                    <td ng-non-bindable>
                        <a class="text-danger csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deleteconversation')?>/<?php echo $item->id?>" ><i class="material-icons me-0">&#xE872;</i></a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php } else { ?>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Empty...');?></p>
<?php } ?>

        </div>
    </div>

</div>