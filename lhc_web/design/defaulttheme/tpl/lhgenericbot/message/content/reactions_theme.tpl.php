<?php if (isset($theme->bot_configuration_array['enable_react_for_vi']) && $theme->bot_configuration_array['enable_react_for_vi'] == true && isset($theme->bot_configuration_array['custom_tb_reactions']) && $theme->bot_configuration_array['custom_tb_reactions'] != '') : ?>

    <?php
    $reactionsOutput = '';
    $reactionsSelectedOutput = '';
    $hasAnyReactionSelected = false;

    $partsReaction = explode("=",$theme->bot_configuration_array['custom_tb_reactions']);
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

        $selectedReaction = isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1];

        $className .= ($selectedReaction === true && $hasReactionsSelected = true) ? ' reaction-selected' : '';

        if ($partsReaction[0] == 'thumb_up') {
            $partsReaction[0] = '&#xf109;';
        }

        if ($partsReaction[0] == 'thumb_down') {
            $partsReaction[0] = '&#xf108;';
        }

        $reactionsOutput .= "<a onclick=\"lhinst.reactionsClicked()\" data-action-type=\"reactions\" data-bot-action=\"button-click\" title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\" data-identifier=\"{$partsReaction[2]}\" data-payload=\"{$partsReaction[1]}\" data-id=\"{$messageId}\" class=\"action-image reaction-item{$className}\">{$partsReaction[0]}</a>";
    }

    if (isset($metaMessage['current']) && isset($theme->bot_configuration_array['reactions_always_visible_under']) && $theme->bot_configuration_array['reactions_always_visible_under'] == true) {
        $partsReactionModal = [];
        if (isset($theme->bot_configuration_array['custom_mw_reactions']) && $theme->bot_configuration_array['custom_mw_reactions'] != '') {
            $partsReactionModal = explode("=",$theme->bot_configuration_array['custom_mw_reactions']);
        }

        $partsReactionModal = array_unique(array_merge($partsReactionModal,explode("=",$theme->bot_configuration_array['custom_tb_reactions'])));
        foreach ($partsReactionModal as $reaction) {
            $partsReaction = explode("|",$reaction);

            $classNameBasic = $className = preg_match('/^[a-zA-Z0-9_]+$/', $partsReaction[0]) ? ' pt-0 me-0 material-icons' : '';

            if (!(isset($partsReaction[2]) && isset($partsReaction[1]))) {
                $partsReaction[2] = strtoupper(preg_replace("/^[0]+/","",bin2hex(mb_convert_encoding($partsReaction[0], 'UTF-32', 'UTF-8'))));
                $partsReaction[1] = 1;
            }

            $selectedReaction = isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] && $hasAnyReactionSelected = true;

            if ($partsReaction[0] == 'thumb_up') {
                $partsReaction[0] = '&#xf109;';
            }

            if ($partsReaction[0] == 'thumb_down') {
                $partsReaction[0] = '&#xf108;';
            }

            if ($selectedReaction === true) {
                $reactionsSelectedOutput .= "<span class=\"{$classNameBasic}\">{$partsReaction[0]}</span>";
            }
        }
    }
?>

    <?php if (isset($theme->bot_configuration_array['always_visible_reactions']) && $theme->bot_configuration_array['always_visible_reactions'] == true) : ?>
        <div class="reactions-holder d-block">
            <?php echo $reactionsOutput?>
        </div>
    <?php else : ?>
        <div class="reactions-holder reactions-holder-plus d-block">
            <div class="reactions-toolbar" id="reactions-toolbar-<?php echo $messageId?>">
                <?php echo $reactionsOutput?>
                <?php if (isset($theme->bot_configuration_array['custom_mw_reactions']) && $theme->bot_configuration_array['custom_mw_reactions'] != '') : ?>
                    <a class="action-image reaction-item reactions-modal-expand material-icons" data-bot-action="lhinst.moreReactions" data-id="<?php echo $messageId?>" onclick="lhinst.moreReactions()">&#xf10a;</a>
                <?php endif; ?>
            </div>
            <a class="reactions-holder-plus-icon" data-bot-action="lhinst.reactionsToolbar" data-id="<?php echo $messageId?>" onclick="lhinst.reactionsToolbar()" title="React">😊</a>
        </div>
        <?php if (isset($theme->bot_configuration_array['reactions_always_visible_under']) && $theme->bot_configuration_array['reactions_always_visible_under'] == true && isset($hasAnyReactionSelected) && $hasAnyReactionSelected == true) : ?>
            <div class="reactions-selected-info">
                <?php echo $reactionsSelectedOutput; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>