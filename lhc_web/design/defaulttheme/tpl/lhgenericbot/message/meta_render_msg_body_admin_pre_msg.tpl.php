<?php if (is_array($metaMessageData)) : ?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'accept_action') : // Chat was accepted ?>

            <?php if (isset($metaMessage['puser_id']) && $metaMessage['puser_id'] > 0) : ?>
                <span class="material-icons text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was assigned to chat opener event it had other agent assigned at that moment')?> - [<?php echo htmlspecialchars($metaMessage['puser_id'])?>]" >account_circle_off</span>
            <?php endif; ?>

            <span class="material-icons text-success" <?php if (isset($metaMessage['ol']) && is_array($metaMessage['ol']) && !empty($metaMessage['ol'])) : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Opened chat by')?> - <?php echo htmlspecialchars(implode(', ',$metaMessage['ol']))?>"<?php endif;?> >login</span>
        <?php elseif ($type == 'transfer_action_user') : // Chat was transferred to other user?>
            <span class="material-icons text-warning">logout</span>
        <?php elseif ($type == 'transfer_action_dep') : // Chat was transfered to departmnet?>
            <span class="material-icons text-info">home</span>
        <?php elseif ($type == 'change_owner_action') : // Chat owner was changed?>
            <span class="material-icons text-info">swap_horiz</span>
        <?php elseif ($type == 'change_dep_action') : // Chat department was changed?>
            <span class="material-icons text-info">location_away</span>
        <?php endif; ?>
    <?php endforeach; endif; ?>
<?php endif; ?>