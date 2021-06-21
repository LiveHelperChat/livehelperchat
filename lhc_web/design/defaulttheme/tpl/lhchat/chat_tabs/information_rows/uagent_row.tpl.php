<?php if ( !empty($chat->uagent) ) : ?>
<div class="col-6 pb-1">
    <i class="material-icons" title="<?php echo htmlspecialchars($chat->uagent)?>"><?php echo ($chat->device_type == 0 ? 'computer' : ($chat->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($chat->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($chat->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?>
</div>
<?php endif;?>