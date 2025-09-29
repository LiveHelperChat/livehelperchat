<div id="tabs" role="tabpanel">

    <ul class="nav nav-pills" role="tablist">
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#chats" aria-controls="chats" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Mails');?></a></li>
    </ul>

    <div class="tab-content ps-2">
        <div role="tabpanel" class="tab-pane active" id="chats">
            
            <?php if (isset($takes_to_long)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Your request takes to long. Please contact your administrator and send them url from your browser.');?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_info.tpl.php')); ?>
            <?php endif; ?>

            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel.tpl.php')); ?>

            <?php if (isset($items)) : ?>
                <form action="<?php echo $input->form_action,$inputAppend?>" method="post" >

                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

                    <table cellpadding="0" cellspacing="0" class="table table-sm list-links" id="mail-list-table" width="100%">
                        <thead>
                        <tr>
                            <th><input class="mb-0" type="checkbox" id="check-all-items" /></th>
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
                            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/start_row.tpl.php')); ?>
                            <td><input class="mb-0" type="checkbox" name="ConversationID[]" value="<?php echo $item->id?>" /></td>
                            <td ng-non-bindable>

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

                                <a class="me-2 mail-link" title="<?php echo htmlspecialchars($item->subject_front)?>" href="#/chat-id-mc<?php echo $item->id?>"><?php echo $item->id; ?></a>

                                <a class="user-select-none mail-link" title="<?php echo htmlspecialchars($item->subject_front)?>" href="#/chat-id-mc<?php echo $item->id?>"><?php echo htmlspecialchars($item->subject)?>&nbsp;<small><?php echo $item->total_messages?></small></a>

                                <?php if (is_array($item->subjects)) : $subjectPresent = [];?>
                                    <?php foreach ($item->subjects as $subject) : if (is_object($subject) && !in_array($subject->id, $subjectPresent)) : $subjectPresent[] = $subject->id;?>
                                        <span class="badge bg-info mx-1" ng-non-bindable <?php if ($subject->color != '') : ?>style="background-color:#<?php echo htmlspecialchars($subject->color)?>!important;" <?php endif;?> ><?php echo htmlspecialchars($subject)?></span>
                                    <?php endif; endforeach; ?>
                                <?php endif; ?>

                            </td>
                            <td ng-non-bindable>
                                <?php echo erLhcoreClassDesign::shrt(($item->from_name == $item->from_address && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email') ? \LiveHelperChat\Helpers\Anonymizer::maskEmail($item->from_name) : $item->from_name) . ' ✉︎ ' . (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email') ? $item->from_address : \LiveHelperChat\Helpers\Anonymizer::maskEmail($item->from_address)),30); ?>
                            </td>
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
                    <script>
                        $(function() {
                            function updateDeleteArchiveUI(){
                                let lengthChecked = $('input[name="ConversationID[]"]:checked').length;
                                if (lengthChecked == 0){
                                    $('#delete-archive-btn,#delete-selected-btn').prop('disabled',true);
                                } else {
                                    $('#delete-archive-btn,#delete-selected-btn').prop('disabled',false);
                                }

                                $('#delete-archive').text(lengthChecked);
                                $('#delete-selected').text(lengthChecked);
                            }
                            $('#check-all-items').change(function(){
                                if ($(this).is(':checked')){
                                    $('input[name="ConversationID[]"]').attr('checked','checked');
                                } else {
                                    $('input[name="ConversationID[]"]').removeAttr('checked');
                                }
                                updateDeleteArchiveUI();
                            });
                            $('input[name="ConversationID[]"]').change(updateDeleteArchiveUI);
                            $('#mail-list-table a.mail-link').click(function(event){
                                window.location.href = event.currentTarget.href;
                                ee.emitEvent('svelteOpenMail',[window.location.hash.split('chat-id-mc')[1],event.currentTarget.title]);
                                event.preventDefault(); // Prevent the default behavior (opening a new tab)
                            })
                        });
                    </script>

                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

                    <?php if (isset($pages)) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
                    <?php endif;?>

                    <div class="btn-group btn-group-sm" role="group" aria-label="...">

                        <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','quick_actions')) : ?>
                                <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/3?<?php echo $appendPrintExportURL?>'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">sync_alt</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick actions')?></button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($can_close === true) : ?>
                            <input type="submit" name="doClose" class="btn btn-warning" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Close selected');?>" />
                        <?php endif; ?>

                        <?php if ($can_delete === true) : ?>
                            <button type="button" name="doDelete" onclick="lhc.confirmDelete($(this))" disabled id="delete-selected-btn" class="btn btn-danger" value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete selected');?> (<span id="delete-selected">0</span>)</button>

                            <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                                <button type="button" onclick="return lhc.revealModal({'title' : 'Delete all', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/4?<?php echo $appendPrintExportURL?>'})" class="btn btn-danger btn-sm"><span class="material-icons">delete_sweep</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete all items')?> (<?php echo $pages->items_total?>)</button>
                            <?php endif; ?>

                            <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                                <button type="button" class="btn btn-danger" id="delete-archive-btn" disabled onclick="return lhc.revealModal({'title' : 'Delete and archive selected', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/5'+getCheckedElements()+'?<?php echo $appendPrintExportURL?>'})" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvconv','Delete and archive selected');?> (<span id="delete-archive">0</span>)</button>
                                <button type="button" class="btn btn-danger" onclick="return lhc.revealModal({'title' : 'Delete all archive', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/5?<?php echo $appendPrintExportURL?>'})" ><span class="material-icons">delete_sweep</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete and archive all')?> (<?php echo $pages->items_total?>)</button>
                                <script>
                                    function getCheckedElements(){
                                        var choices = [];
                                        var els = document.getElementsByName('ConversationID[]');
                                        for (var i=0;i<els.length;i++){
                                            if ( els[i].checked ) {
                                                choices.push(els[i].value);
                                            }
                                        }
                                        return choices.length > 0 ? '/(ids)/'+choices.join('/') : '';
                                    }
                                </script>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>

                </form>
            <?php endif; ?>
        </div>
    </div>

</div>
