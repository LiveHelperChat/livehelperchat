<?php if (is_array($metaMessageData)) : ?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'accept_action') : // Chat was accepted ?>
            <span class="material-icons text-success">login</span>
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