<div class="meta-message">
    <div class="p-1">
        <ul class="quick-replies list-unstyled">
            <?php foreach ($metaMessage as $item) : ?>
                <li>
                    <?php if ((!isset($item['editable']) || $item['editable'] == true) && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
                    <a rel="noreferrer" class="meta-auto-hide" href="#" title="Edit this step" data-id="<?php echo $messageId?>" data-payload="<?php echo $item['step']?>" onclick="lhinst.editGenericStep(<?php echo $item['step']?>,<?php echo $messageId?>)">
                        <?php if (isset($metaMessageData['content']['collectable_options']['edit_image_url']) && !empty($metaMessageData['content']['collectable_options']['edit_image_url'])) : ?>
                            <img src="<?php echo htmlspecialchars($metaMessageData['content']['collectable_options']['edit_image_url'])?>" />
                        <?php else : ?>
                            <i class="material-icons"><?php if (isset($react) && $react == true) : ?>&#xf116;<?php else : ?>edit<?php endif; ?></i>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php echo htmlspecialchars($item['name'])?> - <?php echo htmlspecialchars(isset($item['value_literal']) ? $item['value_literal'] : $item['value']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages']) : ?>
        <div class="meta-auto-hide meta-message-<?php echo $messageId?>">
            <?php if (isset($metaMessageData['content']['collectable_options']['show_summary_checkbox']) && $metaMessageData['content']['collectable_options']['show_summary_checkbox'] == true) : ?>
                <div><label><input type="checkbox" value="on" onchange="($(this).is(':checked') ? $('#confirm-button-<?php echo $messageId?>').removeAttr('disabled') : $('#confirm-button-<?php echo $messageId?>').attr('disabled','disabled'));$('#messagesBlock').stop(true,false).animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 500);" /> <?php if (isset($metaMessageData['content']['collectable_options']['show_summary_checkbox_name'])) : ?><?php echo htmlspecialchars($metaMessageData['content']['collectable_options']['show_summary_checkbox_name'])?><?php endif?></label></div>
            <?php endif; ?>
            <div class="btn-group" role="group" aria-label="...">
                <button id="confirm-button-<?php echo $messageId?>" <?php if (isset($metaMessageData['content']['collectable_options']['show_summary_checkbox']) && $metaMessageData['content']['collectable_options']['show_summary_checkbox'] == true) : ?>disabled="disabled"<?php endif?> data-id="<?php echo $messageId?>" data-payload="confirm" type="button" class="btn btn-xs btn-info" onclick="lhinst.buttonClicked('confirm',<?php echo $messageId?>,$(this))"><?php if (isset($metaMessageData['content']['collectable_options']['show_summary_confirm_name']) && !empty($metaMessageData['content']['collectable_options']['show_summary_confirm_name'])) : ?><?php echo htmlspecialchars($metaMessageData['content']['collectable_options']['show_summary_confirm_name'])?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Confirm')?><?php endif?></button>
                <button type="button" class="btn btn-xs btn-warning" data-id="<?php echo $messageId?>" data-payload="cancel_workflow" onclick="lhinst.buttonClicked('cancel_workflow',<?php echo $messageId?>,$(this))"><?php if (isset($metaMessageData['content']['collectable_options']['show_summary_cancel_name']) && !empty($metaMessageData['content']['collectable_options']['show_summary_cancel_name'])) : ?><?php echo htmlspecialchars($metaMessageData['content']['collectable_options']['show_summary_cancel_name'])?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Cancel')?><?php endif?></button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

