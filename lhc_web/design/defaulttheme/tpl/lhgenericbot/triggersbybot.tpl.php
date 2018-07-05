<select name="AbstractInput_<?php echo htmlspecialchars($element)?>" class="form-control" <?php if ($preview == true) : ?>onchange="renderPreview($(this))"<?php endif; ?> >
    <?php foreach (erLhcoreClassModelGenericBotGroup::getList(array('filter' => array('bot_id' => $bot->id))) as $optGroup) : ?>
    <optgroup label="<?php echo htmlspecialchars($optGroup->name)?>">
        <?php foreach (erLhcoreClassModelGenericBotTrigger::getList(array('filter' => array('group_id' => $optGroup->id))) as $trigger) : ?>
            <option <?php if ($trigger->id == $trigger_id) : ?>selected="selected"<?php endif?> value="<?php echo $trigger->id?>"><?php echo htmlspecialchars($trigger->name)?></option>
        <?php endforeach; ?>
    </optgroup>
    <?php endforeach; ?>
</select>