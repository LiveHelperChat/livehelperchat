<?php if (!empty($chat->additional_data)) : ?>
    <tr>
        <td>
            <a class="btn btn-secondary btn-xs" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_cfrefresh');$('#custom-data-td-<?php echo $chat->id?>').text('...')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?>"><i class="material-icons mr-0">&#xE5D5;</i></a>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Additional data')?>
        </td>
        <td id="custom-data-td-<?php echo $chat->id?>">
            <?php if (is_array($chat->additional_data_array)) : ?>
                <ul class="circle mb-0">
                    <?php foreach ($chat->additional_data_array as $keyItem => $addItem) : if (!is_string($addItem) || (is_string($addItem) && ($addItem != ''))) : ?>
                        <li<?php if (isset($addItem['identifier'])): ?> title="<?php echo htmlspecialchars($addItem['identifier'])?>"<?php endif;?>>
                            <?php if (isset($addItem['key'])) : ?>
                                <?php echo htmlspecialchars($addItem['key'])?> - <?php echo htmlspecialchars($addItem['value'])?>
                            <?php else : ?>
                                <?php echo htmlspecialchars($keyItem)?> - <?php echo json_encode($addItem); ?>
                            <?php endif; ?>
                            <?php if (isset($addItem['h']) && $addItem['h'] == true) : ?>&nbsp;<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Hidden field')?>">visibility_off</i><?php endif;?>
                            <?php if (isset($addItem['url']) && $addItem['url'] == true) : ?>&nbsp;<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Identifier')?> - <?php echo htmlspecialchars($addItem['identifier'])?>">link</i><?php endif;?>
                        </li>
                    <?php endif; endforeach;?>
                </ul>
            <?php else : ?>
                <?php echo htmlspecialchars($chat->additional_data)?>
            <?php endif;?>
        </td>
    </tr>
<?php endif;?>