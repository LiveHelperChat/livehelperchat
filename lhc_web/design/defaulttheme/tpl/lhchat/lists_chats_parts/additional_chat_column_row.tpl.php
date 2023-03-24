<?php if (isset($additional_chat_columns) && !empty($additional_chat_columns)) : ?>
    <?php foreach ($additional_chat_columns as $iconAdditional) : $columnIconData = json_decode($iconAdditional->column_icon,true); ?>
        <td style="white-space: nowrap">
        <?php if (isset($chat->{'cc_' . $iconAdditional->id})) : ?>
            <?php echo htmlspecialchars($chat->{'cc_' . $iconAdditional->id})?>
        <?php endif; ?>
        </td>
    <?php endforeach; ?>
<?php endif; ?>