<?php if ( !empty($chat->uagent) ) : ?>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Device')?></td>
        <td><i class="material-icons" title="<?php echo htmlspecialchars($chat->uagent)?>">&#x<?php echo ($chat->device_type == 0 ? 'f1c5' : ($chat->device_type == 1 ? 'f11c' : 'f4f6')) ?>;</i><?php echo ($chat->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($chat->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?></td>
    </tr>
<?php endif;?>