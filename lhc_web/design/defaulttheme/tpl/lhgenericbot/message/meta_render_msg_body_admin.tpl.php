<?php
/*
  * Render visitor reactions to admin messages
  * Rendered in back office
  * */
if (isset($msg['user_id']) && $msg['user_id'] != 0) : $reactionsOperator = '';?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'reactions') : ?>
            <?php if (isset($metaMessageData['content']['reactions']['current']) && is_array($metaMessageData['content']['reactions']['current'])) : ?>
                <?php foreach ($metaMessageData['content']['reactions']['current'] as $reactionItem => $reactionValue) : ?>
                    <?php if ($reactionItem == 'thumb') : ?>
                        <?php if ($reactionValue == 1) : ?>
                            <?php $reactionsOperator .= '<span title="Thumbs up" class="pt-0 me-0 material-icons reaction-selected">thumb_up</span>';?>
                        <?php else : ?>
                            <?php $reactionsOperator .= '<span title="Thumbs down" class="pt-0 me-0 material-icons reaction-selected">thumb_down</span>';?>
                        <?php endif;?>
                    <?php else : ?>
                        <?php $reactionsOperator .= '<span class="reaction-item pt-0 me-0 reaction-selected">&#x'. $reactionItem . ';</span>';?>
                    <?php endif; ?>
                <?php endforeach;?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach;endif; ?>

    <?php if (!empty($reactionsOperator)) : ?>
        <div class=" reactions-holder-visitor reactions-selected-info d-block">
            <?php echo $reactionsOperator?>
        </div>
    <?php endif; ?>

<?php else : ?>

<?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
    <?php if ($type == 'reactions') : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/reactions_admin.tpl.php'));?>
    <?php endif; ?>
<?php endforeach; endif;  ?>

<?php endif; ?>
