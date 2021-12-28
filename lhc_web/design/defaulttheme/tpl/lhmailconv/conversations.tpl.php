<div id="tabs" role="tabpanel">

    <ul class="nav nav-pills" role="tablist">
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#chats" aria-controls="chats" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Mails');?></a></li>
    </ul>

    <div class="tab-content pl-2">
        <div role="tabpanel" class="tab-pane active" id="chats">
            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel.tpl.php')); ?>

            <?php if (isset($items)) : ?>
                <form action="<?php echo $input->form_action,$inputAppend?>" method="post" >

                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

                    <table cellpadding="0" cellspacing="0" class="table table-sm list-links" width="100%">
                        <thead>
                        <tr>
                            <th><input class="mb-0" type="checkbox" ng-model="check_all_items" /></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Subject');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Sender');?></th>
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
                            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/start_row.tpl.php')); ?>
                            <td><input ng-checked="check_all_items" class="mb-0" type="checkbox" name="ConversationID[]" value="<?php echo $item->id?>" /></td>
                            <td ng-non-bindable>

                                <?php if ($item->lang != '') : ?>
                                    <img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $item->lang?>.png" alt="<?php echo htmlspecialchars($item->lang)?>" title="<?php echo htmlspecialchars($item->lang)?>" />
                                <?php endif; ?>

                                <?php if ($item->undelivered == 1) : ?>
                                    <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Undelivered e-mail');?>" class="text-danger material-icons">sms_failed</span>
                                <?php endif; ?>

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

                                <span class="mr-2"><?php echo $item->id; ?></span><a class="user-select-none" onclick='lhinst.startMailChat(<?php echo $item->id?>,$("#tabs"),<?php echo json_encode($item->subject_front,JSON_HEX_APOS)?>)' href="#!#chat-id-mc<?php echo $item->id?>"><?php echo htmlspecialchars($item->subject)?>&nbsp;<small><?php echo $item->total_messages?></small></a>
                            </td>
                            <td ng-non-bindable><?php echo htmlspecialchars($item->from_name)?> &lt;<?php echo $item->from_address?>&gt;</td>
                            <td><?php echo htmlspecialchars($item->priority)?></td>
                            <td ng-non-bindable><?php echo htmlspecialchars($item->user)?></td>
                            <td nowrap="nowrap" ng-non-bindable>
                                <?php echo htmlspecialchars($item->department),', ',htmlspecialchars($item->mailbox_front['mail'])?>
                            </td>
                            <td ng-non-bindable>
                                <?php if ($item->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>
                                    <i class="material-icons chat-pending">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','New');?>
                                <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) : ?>
                                    <i class="material-icons chat-active">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Active');?>
                                <?php elseif ($item->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) : ?>
                                    <i class="material-icons chat-closed">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Closed');?>
                                <?php endif; ?>
                            </td>
                            <td ng-non-bindable title="<?php echo $item->udate_front_ago;?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','ago');?>">
                                <?php echo $item->udate_front;?>
                            </td>
                            <?php if ($can_delete === true) : ?>
                                <td ng-non-bindable>
                                    <div class="btn-group" role="group" aria-label="..." style="width:60px;">
                                        <a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/deleteconversation')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                                    </div>
                                </td>
                            <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

                    <?php if (isset($pages)) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
                    <?php endif;?>

                    <div class="btn-group" role="group" aria-label="...">

                        <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','quick_actions')) : ?>
                                <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/3?<?php echo $appendPrintExportURL?>'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">sync_alt</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions')?></button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($can_close === true) : ?>
                            <input type="submit" name="doClose" class="btn btn-warning" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Close selected');?>" />
                        <?php endif; ?>

                        <?php if ($can_delete === true) : ?>
                            <input type="submit" name="doDelete" class="btn btn-danger" onclick="return confirm(confLH.transLation.delete_confirm)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Delete selected');?>" />
                        <?php endif; ?>
                    </div>

                </form>
            <?php endif; ?>
        </div>
    </div>

</div>
