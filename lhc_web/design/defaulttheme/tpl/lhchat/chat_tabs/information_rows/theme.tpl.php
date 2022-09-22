<?php if (isset($chat->chat_variables_array['theme_id'])) : ?>
<div class="col-6 pb-1">
    <div>
        <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Theme')?> - <?php echo $chat->chat_variables_array['theme_id']?>" class="material-icons">brush</i><?php echo htmlspecialchars(erLhAbstractModelWidgetTheme::fetch($chat->chat_variables_array['theme_id']));?>
    </div>
</div>
<?php endif; ?>