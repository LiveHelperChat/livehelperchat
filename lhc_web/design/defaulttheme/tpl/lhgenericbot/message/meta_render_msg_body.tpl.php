<?php
/*
 * Render reactions to visitor messages in the widget
 * */
$metaMessage = [];
if (isset($msg['user_id']) && $msg['user_id'] == 0) : $reactionsOperator = '';?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'reactions') : ?>
            <?php if (isset($metaMessageData['content']['reactions']['current']) && is_array($metaMessageData['content']['reactions']['current'])) : ?>
                <?php foreach ($metaMessageData['content']['reactions']['current'] as $reactionItem => $reactionValue) : ?>
                    <?php if ($reactionItem == 'thumb') : ?>
                        <?php if ($reactionValue == 1) : ?>
                            <?php $reactionsOperator .= '<span title="' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Thumbs up') . '" class="reaction-item me-0 material-icons reaction-selected">&#xf109;</span>';?>
                        <?php else : ?>
                            <?php $reactionsOperator .= '<span title="' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Thumbs down') . '" class="reaction-item me-0 material-icons reaction-selected">&#xf108;</span>';?>
                        <?php endif;?>
                    <?php else : ?>
                        <?php $reactionsOperator .= '<span class="me-0 reaction-selected">&#x'. $reactionItem . ';</span>';?>
                    <?php endif; ?>
                <?php endforeach;?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach;endif; ?>
    <?php if (!empty($reactionsOperator)) : ?>
    <div class="reactions-selected-info">
        <?php echo $reactionsOperator?>
    </div>
    <?php endif; ?>
<?php else : // Visitor reactions to operator/bot messages ?>
<?php if (isset($visitorReactionsRendered)) {unset($visitorReactionsRendered);}; if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
    <?php if ($type == 'reactions' && isset($metaMessage['content'])) : $visitorReactionsRendered = true;?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/reactions.tpl.php'));?>
    <?php endif; ?>
<?php endforeach; endif;  ?>
<?php if (!isset($visitorReactionsRendered) && isset($theme) && is_object($theme) && isset($paramsMessageRender['sender']) && $paramsMessageRender['sender'] > 0) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/reactions_theme.tpl.php'));?>
<?php endif; ?>

<?php endif; ?>

