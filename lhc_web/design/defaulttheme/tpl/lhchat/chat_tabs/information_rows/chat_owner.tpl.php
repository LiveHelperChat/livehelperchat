<tr>
    <td colspan="2">
        <h6 class="fw-bold"><i class="material-icons">account_box</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?></h6>
        <div class="row">
            <div class="col-6 pb-1">
                <div class="text-muted" id="chat-owner-<?php echo $chat->id?>" user-id="<?php echo $chat->user_id?>" title="<?php echo htmlspecialchars((string)\LiveHelperChat\Models\Departments\UserDepAlias::getAlias(array('scope' => 'as_string', 'chat' => $chat)));?>">
                    <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
                        <span class="material-icons action-image" onclick='lhc.revealModal({"iframe" : true, "title" : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Concurrent chats.')), JSON_HEX_QUOT)?>, height: "600", "url":WWW_DIR_JAVASCRIPT+"chat/chathistory/<?php echo $chat->id?>"})' title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Operator chats during this conversations.')?>">history</span><?php echo htmlspecialchars($user->name.' '.$user->surname)?>
                    <?php endif; ?>
                </div>
                <?php if ($chat->transfer_uid > 0) : ?>
                    <div title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Transferred by')?>" class="text-muted pt-1" user-id="<?php echo $chat->transfer_uid?>">
                        <?php $userTransferer = erLhcoreClassModelUser::fetch($chat->transfer_uid); if ($userTransferer !== false) : ?>
                            <span class="material-icons" >transfer_within_a_station</span><?php echo htmlspecialchars($userTransferer->name.' '.$userTransferer->surname)?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer.tpl.php'));?>
        </div>
    </td>
</tr>