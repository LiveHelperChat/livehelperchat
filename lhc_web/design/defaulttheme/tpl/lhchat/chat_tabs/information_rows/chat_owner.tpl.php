<tr>
    <td colspan="2">
        <h6 class="font-weight-bold py-2"><i class="material-icons">account_box</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat owner')?>
        </h6>

        <div class="row">
            <div class="col-6">
                <div class="text-muted" id="chat-owner-<?php echo $chat->id?>" user-id="<?php echo $chat->user_id?>">
                    <?php $user = $chat->getChatOwner();  if ($user !== false) : ?>
                        <?php echo htmlspecialchars($user->name.' '.$user->surname)?>
                    <?php endif; ?>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/transfer.tpl.php'));?>
        </div>

    </td>

</tr>