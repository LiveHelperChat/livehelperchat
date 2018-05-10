<div class="meta-message">
    <div class="p5 ">
        <ul class="quick-replies">
            <?php foreach ($metaMessage as $item) : ?>
                <li>
                    <?php echo htmlspecialchars($item['name'])?> - <?php echo htmlspecialchars(isset($item['value_literal']) ? $item['value_literal'] : $item['value']) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages']) : ?>
        <div class="meta-auto-hide meta-message-<?php echo $messageId?>">
            <?php if (isset($metaMessageData['content']['collectable_options']['show_summary_checkbox']) && $metaMessageData['content']['collectable_options']['show_summary_checkbox'] == true) : ?>
                <div><label><input type="checkbox" value="on" onchange="($(this).is(':checked') ? $('#confirm-button-<?php echo $messageId?>').removeClass('hide') : $('#confirm-button-<?php echo $messageId?>').addClass('hide'));$('#messagesBlock').stop(true,false).animate({ scrollTop: $('#messagesBlock').prop('scrollHeight') }, 500);" /> <?php if (isset($metaMessageData['content']['collectable_options']['show_summary_checkbox_name'])) : ?><?php echo htmlspecialchars($metaMessageData['content']['collectable_options']['show_summary_checkbox_name'])?><?php endif?></label></div>
            <?php endif; ?>

            <div id="confirm-button-<?php echo $messageId?>" class="pb5 <?php if (isset($metaMessageData['content']['collectable_options']['show_summary_checkbox']) && $metaMessageData['content']['collectable_options']['show_summary_checkbox'] == true) : ?> hide<?php endif?>"><a class="btn btn-xs btn-info "  onclick="lhinst.buttonClicked('confirm',<?php echo $messageId?>)"><?php if (isset($metaMessageData['content']['collectable_options']['show_summary_confirm_name'])) : ?><?php echo htmlspecialchars($metaMessageData['content']['collectable_options']['show_summary_confirm_name'])?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Confirm')?><?php endif?></a></div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

