<div role="tabpanel" class="tab-pane<?php if (isset($tab) && $tab == 'chats') : ?> active<?php endif; ?>" id="userchats">
    <?php if ($online_user->id > 0) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/modal_online_user_info_chats_list_override.tpl.php'));?>
    <?php else : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/no_online_user_info_chats_list_override.tpl.php'));?>
    <?php endif; ?>
</div>