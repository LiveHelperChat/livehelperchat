<?php if (!empty($chat->additional_data)) : ?>
    <tr>
        <td colspan="2">

            <h6 class="fw-bold"><i class="material-icons">storage</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Additional data')?></h6>

            <div class="text-muted pb-1" id="custom-data-td-<?php echo $chat->id?>">
                <?php if (is_array($chat->additional_data_array)) : ?>
                    <ul class="circle mb-0">
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
                    </ul>
                <?php else : ?>
                    <?php echo htmlspecialchars($chat->additional_data)?>
                <?php endif;?>
            </div>

        </td>
    </tr>
<?php endif;?>