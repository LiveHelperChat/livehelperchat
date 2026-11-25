<select name="AbstractInput_<?php echo htmlspecialchars($element)?>" class="form-control form-control-sm" <?php if ($preview == true) : ?>onchange="renderPreview($(this))"<?php endif; ?> >
    <?php foreach (erLhcoreClassModelGenericBotGroup::getList(array('sort' => '`name` ASC','filter' => array('bot_id' => $bot->id))) as $optGroup) : ?>
    <optgroup label="<?php echo htmlspecialchars($optGroup->name)?>">
        <?php foreach (erLhcoreClassModelGenericBotTrigger::getList(array('sort' => '`name` ASC', 'filter' => array('group_id' => $optGroup->id))) as $trigger) : ?>
            <option <?php if ($asarg === true && $trigger->as_argument !== 1) : ?>disabled<?php endif;?> <?php if ($trigger->id == $trigger_id) : ?>selected="selected"<?php endif?> value="<?php echo $trigger->id?>">
                <?php echo htmlspecialchars($trigger->name)?><?php if ($asarg === true && $trigger->as_argument !== 1) : ?> <b>[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Requires - `Can be passed as argument` option to be checked.')?>]</b><?php endif; ?>
            </option>
        <?php endforeach; ?>
    </optgroup>
    <?php endforeach; ?>
</select>