<?php    // Reactions to visitor messages in admin interface ?>
<?php if (

        (
            (isset($chat->chat_variables_array['theme_id']) && ($chatTheme = erLhAbstractModelWidgetTheme::fetch($chat->chat_variables_array['theme_id']))) ||
            ($chat->theme_id > 0 && ($chatTheme = erLhAbstractModelWidgetTheme::fetch($chat->theme_id)))
        )

        && $chatTheme instanceof erLhAbstractModelWidgetTheme && isset($chatTheme->bot_configuration_array['custom_tb_reactions']) && $chatTheme->bot_configuration_array['custom_tb_reactions'] != '') : ?>

        <?php

        $reactionsOutput = '';
        $reactionsSelectedOutput = '';
        $hasAnyReactionSelected = false;

        $partsReaction = explode("=",$chatTheme->bot_configuration_array['custom_tb_reactions']);
        foreach ($partsReaction as $reaction) {
            $partsReaction = explode("|",$reaction);

            $className = preg_match('/^[a-zA-Z0-9_]+$/', $partsReaction[0]) ? ' pt-0 me-0 material-icons' : '';

            if (isset($partsReaction[2]) && isset($partsReaction[1])) {
                $className .= htmlspecialchars(' reaction-id-' . $partsReaction[2] . '-' . $partsReaction[1]);
            } else {
                $partsReaction[2] = strtoupper(preg_replace("/^[0]+/","",bin2hex(mb_convert_encoding($partsReaction[0], 'UTF-32', 'UTF-8'))));
                $partsReaction[1] = 1;
                $className .= htmlspecialchars(' reaction-id-' . $partsReaction[2] . '-' . $partsReaction[1] );
            }

            $selectedReaction = isset($metaMessageData['content']['reactions']['current'][$partsReaction[2]]) && $metaMessageData['content']['reactions']['current'][$partsReaction[2]] == $partsReaction[1];

            $className .= ($selectedReaction === true && $hasReactionsSelected = true) ? ' reaction-selected' : '';

            $reactionsOutput .= "<a onclick=\"lhinst.reaction($(this))\" title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\" data-identifier=\"{$partsReaction[2]}\" data-value=\"{$partsReaction[1]}\" data-msg-id=\"{$msg['id']}\" class=\"action-image reaction-item{$className}\">{$partsReaction[0]}</a>";
        }

        if (isset($metaMessageData['content']['reactions']['current']) && isset($chatTheme->bot_configuration_array['reactions_always_visible_under']) && $chatTheme->bot_configuration_array['reactions_always_visible_under'] == true) {
            $partsReactionModal = [];
            if (isset($chatTheme->bot_configuration_array['custom_mw_reactions']) && $chatTheme->bot_configuration_array['custom_mw_reactions'] != '') {
                $partsReactionModal = explode("=",$chatTheme->bot_configuration_array['custom_mw_reactions']);
            }

            $partsReactionModal = array_unique(array_merge($partsReactionModal,explode("=",$chatTheme->bot_configuration_array['custom_tb_reactions'])));
            foreach ($partsReactionModal as $reaction) {
                $partsReaction = explode("|",$reaction);

                $classNameBasic = $className = preg_match('/^[a-zA-Z0-9_]+$/', $partsReaction[0]) ? ' pt-0 me-0 material-icons' : '';

                if (!(isset($partsReaction[2]) && isset($partsReaction[1]))) {
                    $partsReaction[2] = strtoupper(preg_replace("/^[0]+/","",bin2hex(mb_convert_encoding($partsReaction[0], 'UTF-32', 'UTF-8'))));
                    $partsReaction[1] = 1;
                }

                $selectedReaction = isset($metaMessageData['content']['reactions']['current'][$partsReaction[2]]) && $metaMessageData['content']['reactions']['current'][$partsReaction[2]] == $partsReaction[1] && $hasAnyReactionSelected = true;

                if ($selectedReaction === true) {
                    $reactionsSelectedOutput .= "<span class=\"{$classNameBasic}\">{$partsReaction[0]}</span>";
                }
            }
        }
        ?>

        <div class="reactions-holder reactions-holder-plus d-block" id="reaction-message-<?php echo $msg['id']?>">
            <div class="reactions-toolbar" id="reactions-toolbar-<?php echo $msg['id']?>" style="display: none">
                <?php echo $reactionsOutput?>
                <?php if (isset($chatTheme->bot_configuration_array['custom_mw_reactions']) && $chatTheme->bot_configuration_array['custom_mw_reactions'] != '') : ?>
                    <a class="action-image reaction-item reactions-modal-expand material-icons" data-bot-action="lhinst.moreReactions" data-id="<?php echo $msg['id']?>" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'chat/reactmodal/<?php echo $msg['id']?>'})">add</a>
                <?php endif; ?>
            </div>
            <a class="reactions-holder-plus-icon" data-bot-action="lhinst.reactionsToolbar" data-id="<?php echo $msg['id']?>" onclick="$('#reactions-toolbar-<?php echo $msg['id']?>').toggle()" title="React">😊</a>
        </div>
        <?php if (isset($chatTheme->bot_configuration_array['reactions_always_visible_under']) && $chatTheme->bot_configuration_array['reactions_always_visible_under'] == true && isset($hasAnyReactionSelected) && $hasAnyReactionSelected == true) : ?>
            <div class="reactions-holder-admin-info reactions-selected-info" id="reaction-message-info-<?php echo $msg['id']?>">
                <?php echo $reactionsSelectedOutput; ?>
            </div>
        <?php endif; ?>

<?php else : ?>
<div class="reactions-holder reactions-holder-admin <?php if (isset($metaMessageData['content']['reactions']['current'])) : ?>reactions-selected<?php endif;?>" id="reaction-message-<?php echo $msg['id']?>">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/reaction_to_visitor_body.tpl.php'));?>
</div>
<?php endif; ?>