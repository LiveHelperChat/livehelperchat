<div role="tabpanel" class="tab-pane pl-2 <?php if ($chatTabsOrderDefault == 'private_chat_tab') print ' active';?>" id="private-chat-tab-<?php echo $chat->id?>">
    <?php if (isset($archive)) : ?>
        <?php
            try {
                $archiveSupportChat = erLhcoreClassModelGroupChatArchive::findOne(array('filter' => array('chat_id' => $chat->id)));
            } catch (Exception $e) {
                echo "Archive not found.";
            }
        ?>
        <?php if (isset($archiveSupportChat) && $archiveSupportChat instanceof erLhcoreClassModelGroupChatArchive) : ?>
            <?php $supportChatMembers = erLhcoreClassModelGroupChatMemberArchive::getList(array('filter' => array('group_id' => $archiveSupportChat->id))); ?>
            <div class="pb-1 border-bottom">
                <?php foreach ($supportChatMembers as $supportChatMember) : ?>
                    <button class="btn btn-sm fs12 btn-outline-secondary mb-1 mr-1">[<?php echo $supportChatMember->user_id?>] <?php echo htmlspecialchars($supportChatMember->n_off_full)?></button>
                <?php endforeach; ?>
            </div>
            <div class="mx550">
                <?php
                    $LastMessageID = 0;
                    $messages = json_decode(json_encode(erLhcoreClassModelGroupMsgArchive::getList(array('filter' => array('chat_id' => $archiveSupportChat->id)))),true);
                    $current_user_id = erLhcoreClassUser::instance()->getUserID();
                ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
            </div>

        <?php endif; ?>
    <?php else : ?>
    <div id="private-chat-tab-root-<?php echo $chat->id?>"></div>
    <?php endif; ?>
</div>