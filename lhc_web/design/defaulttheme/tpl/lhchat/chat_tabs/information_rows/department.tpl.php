<?php if ( $chat->department !== false ) : ?>
    <tr>
        <td colspan="2" >
            <h6 class="font-weight-bold py-2"><i class="material-icons">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat')?>
                <div class="float-right">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information/thumbs.tpl.php'));?>
                    <i id="chat-id-<?php echo $chat->id?>-mds" data-chat-status="<?php echo $chat->status?>" data-chat-user="<?php echo $chat->user_id?>" class="material-icons<?php if ($chat->has_unread_op_messages == 1) : ?> chat-unread<?php else : ?> chat-active<?php endif;?>">chat</i>
                    <?php if (isset($canEditChat) && $canEditChat == true && (!isset($hideActionBlock) || $hideActionBlock == false)) : ?>
                        <span class="float-right <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?> action-image<?php endif?>" id="chat-status-text-<?php echo $chat->id?>" <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','canchangechatstatus')) : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Click to change chat status')?>" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/changestatus/<?php echo $chat->id?>'})"<?php endif;?>>
                            <i class="material-icons mr-0" title="<?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Pending chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Active chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Closed chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chatbox chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Operators chat')?><?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Bot chat')?><?php endif;?>">info_outline</i>
                        </span>
                    <?php endif; ?>
                </div>
            </h6>


            <div class="row text-muted">

                <div class="col-6 pb-2">
                    <div class="department-id" data-dep-id="<?php echo $chat->dep_id?>">
                        <i title="><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Department')?>" class="material-icons">home</i><?php if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST) : ?><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','This is offline message')?>" class="material-icons">mail</i><?php endif?><?php echo htmlspecialchars($chat->department);?>
                    </div>
                </div>

                <?php if ($canEditChat == true) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/edit_chat.tpl.php'));?>

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/close_chat.tpl.php'));?>

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/delete_chat.tpl.php'));?>

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/speech.tpl.php'));?>

                    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/cobrowse.tpl.php'));?>

                <?php endif; ?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/open_new_window.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/print.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/remove_dialog_tab.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/copy_messages.tpl.php'));?>

                <div class="col-6 pb-2">
                    <span class="material-icons">vpn_key</span><?php echo $chat->id;?> <button data-success="Copied" class="btn btn-xs btn-link text-muted py-1" data-copy="<?php echo (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] ?><?php echo erLhcoreClassDesign::baseurl('front/default')?>/#!#chat-id-<?php echo $chat->id?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons">link</i>Copy link</button>
                </div>

            </div>
        </td>
    </tr>
<?php endif;?>