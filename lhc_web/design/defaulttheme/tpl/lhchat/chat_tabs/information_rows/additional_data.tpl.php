<?php
$windowAdditionalColumns = erLhAbstractModelChatColumn::getList(array(
        'ignore_fields' => array('position', 'conditions', 'column_identifier', 'enabled', 'popup_content', 'has_popup', 'icon_mode', 'online_enabled', 'chat_window_enabled'),
        'sort' => false,
        'filter' => array('enabled' => 1, 'chat_window_enabled' => 1)
));
if (!empty($windowAdditionalColumns) || !empty($chat->additional_data)) : ?>
    <tr>
        <td colspan="2">
            <h6 class="fw-bold"><i class="material-icons">storage</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Additional data')?></h6>
            <div class="text-muted pb-1" id="custom-data-td-<?php echo $chat->id?>">
                <ul class="circle mb-0">
                <?php if (is_array($chat->additional_data_array)) : ?>
                        <?php foreach ($chat->additional_data_array as $keyItem => $addItem) : if (!is_string($addItem) || (is_string($addItem) && ($addItem != ''))) : ?>
                            <li<?php if (isset($addItem['identifier'])): ?> title="<?php echo htmlspecialchars($addItem['identifier'])?>"<?php endif;?>>
                                <?php if (isset($addItem['h']) && $addItem['h'] == true) : ?>&nbsp;<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hidden field')?>">visibility_off</i><?php endif;?>
                                <?php if (isset($addItem['secure']) && $addItem['secure'] == true) : ?>&nbsp;<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Passed as encrypted variable')?>">enhanced_encryption</i><?php endif;?>
                                <?php if (isset($addItem['url']) && $addItem['url'] == true) : ?>&nbsp;<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Identifier')?> - <?php echo htmlspecialchars($addItem['identifier'])?>">link</i><?php endif;?>
                                <?php if (isset($addItem['key'])) : ?>
                                    <?php echo htmlspecialchars($addItem['key'])?> - <?php echo htmlspecialchars($addItem['value'])?>
                                <?php else : ?>
                                    <?php echo htmlspecialchars($keyItem)?> - <?php echo json_encode($addItem); ?>
                                <?php endif; ?>
                            </li>
                        <?php endif; endforeach;?>
                <?php elseif (!empty($chat->additional_data)) : ?>
                    <li><?php echo htmlspecialchars($chat->additional_data)?></li>
                <?php endif;?>

                <?php $chatItems = [$chat]; erLhcoreClassChat::prefillGetAttributes($chatItems, array(), array(), array('additional_columns' => $windowAdditionalColumns, 'do_not_clean' => true)); foreach ($windowAdditionalColumns as $columnAdditional) : ?>
                    <?php if (isset($windowAdditionalColumns) && !empty($windowAdditionalColumns)) : ?>
                        <?php foreach ($windowAdditionalColumns as $iconAdditional) : $columnIconData = json_decode($iconAdditional->column_icon,true); ?>
                                <?php if (isset($chat->{'cc_' . $iconAdditional->id})) : ?>
                                    <li><?php if ($iconAdditional->column_icon != '') : ?><span class="material-icons text-muted"><?php echo htmlspecialchars($iconAdditional->column_icon)?></span><?php endif; ?><?php echo htmlspecialchars($iconAdditional->column_name)?> - <?php echo htmlspecialchars($chat->{'cc_' . $iconAdditional->id})?></li>
                                <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                </ul>
            </div>
        </td>
    </tr>
<?php endif;?>
