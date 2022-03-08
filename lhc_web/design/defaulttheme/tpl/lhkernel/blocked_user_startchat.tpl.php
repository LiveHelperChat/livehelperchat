<div class="alert alert-danger" role="alert">
    <?php if (isset($chat_input) && erLhcoreClassModelChatBlockedUser::getCount(['filter' => ['online_user_id' => $chat_input->online_user_id, 'btype' => 7]]) > 0) : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/start_chat_blocked','At this moment you can contact us via email only. Sorry for the inconveniences.'); ?>
    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/start_chat_blocked','At this moment you can contact us via email only. Sorry for the inconveniences.'); ?>
    <?php endif; ?>
</div>