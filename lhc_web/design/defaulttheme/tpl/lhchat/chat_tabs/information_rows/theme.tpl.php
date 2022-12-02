<?php if ($chat->theme_id > 0 || isset($chat->chat_variables_array['theme_id'])) : ?>
<div class="col-6 pb-1">
    <div>
        <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Theme')?> - <?php echo $chat->theme_id > 0 ? $chat->theme_id : $chat->chat_variables_array['theme_id']?>" class="material-icons">brush</i><?php echo htmlspecialchars(erLhAbstractModelWidgetTheme::fetch($chat->theme_id > 0 ? $chat->theme_id : $chat->chat_variables_array['theme_id']));?>
    </div>
</div>
<?php endif; ?>