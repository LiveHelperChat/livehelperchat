<?php if (isset($theme->bot_configuration_array['enable_react_for_vi']) && $theme->bot_configuration_array['enable_react_for_vi'] == true) : ?>

    <?php $reactionsOutput = ''; if (isset($theme->bot_configuration_array['custom_tb_reactions'])) : ?>
        <?php
            $partsReaction = explode("=",$theme->bot_configuration_array['custom_tb_reactions']);
            foreach ($partsReaction as $reaction) {
                $partsReaction = explode("|",$reaction);

                $className = preg_match('/^[a-zA-Z0-9_]+$/', $partsReaction[0]) ? ' pt-0 mr-0 material-icons' : '';

                if (isset($partsReaction[2]) && isset($partsReaction[1])) {
                    $className .= htmlspecialchars(' reaction-id-' . $partsReaction[2] . '-' . $partsReaction[1]);
                } else {
                    $partsReaction[2] = strtoupper(preg_replace("/^[0]+/","",bin2hex(mb_convert_encoding($partsReaction[0], 'UTF-32', 'UTF-8'))));
                    $partsReaction[1] = 1;
                    $className .= htmlspecialchars(' reaction-id-' . $partsReaction[2] . '-' . $partsReaction[1] );
                }

                $className .= (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] && $hasReactionsSelected = true) ? ' reaction-selected' : '';

                if ($partsReaction[0] == 'thumb_up') {
                    $partsReaction[0] = '&#xf109;';
                }

                if ($partsReaction[0] == 'thumb_down') {
                    $partsReaction[0] = '&#xf108;';
                }

                $reactionsOutput .= "<a onclick=\"lhinst.reactionsClicked()\" data-action-type=\"reactions\" data-bot-action=\"button-click\" title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\" data-identifier=\"{$partsReaction[2]}\" data-payload=\"{$partsReaction[1]}\" data-id=\"{$messageId}\" class=\"action-image reaction-item{$className}\">{$partsReaction[0]}</a>";
            }
        ?>
    <?php endif; ?>

    <?php if (isset($theme->bot_configuration_array['always_visible_reactions']) && $theme->bot_configuration_array['always_visible_reactions'] == true) : ?>
        <div class="reactions-holder d-block">
            <?php echo $reactionsOutput?>
        </div>
    <?php else : ?>
        <div class="reactions-holder reactions-holder-plus d-block">
            <div class="reactions-toolbar d-none" id="reactions-toolbar-<?php echo $messageId?>">
                <?php echo $reactionsOutput?>
                <?php if (isset($theme->bot_configuration_array['custom_mw_reactions']) && $theme->bot_configuration_array['custom_mw_reactions'] != '') : ?>
                    <a class="action-image reaction-item reactions-modal-expand material-icons" data-id="<?php echo $messageId?>" onclick="lhinst.moreReactions()">&#xf10a;</a>
                <?php endif; ?>
            </div>
            <a class="reactions-holder-plus-icon" data-bot-action="lhinst.reactionsToolbar" data-id="<?php echo $messageId?>" onclick="lhinst.reactionsToolbar()" title="React">😊</a>
        </div>
    <?php endif; ?>

<?php endif; ?>