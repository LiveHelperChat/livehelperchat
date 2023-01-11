<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Are you sure you want to close this chat?'); $hideModalClose = true; ?>

<div class="modal-dialog modal-<?php isset($modalSize) ? print $modalSize : print 'lg'?> modal-confirm-leave mx-4">
    <div class="modal-content">

    <?php if (isset($admin_mode)) : ?>
    <div ng-non-bindable class="modal-header">
        <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>React to visitor message</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <?php endif; ?>

<div class="modal-body<?php (isset($modalBodyClass)) ? print ' '.$modalBodyClass : ''?>">

    <?php

    $metaMessage = isset($message->meta_msg_array['content']['reactions']) ? $message->meta_msg_array['content']['reactions'] : [];

    $partsReaction = explode("=",$theme->bot_configuration_array['custom_mw_reactions']);
    $reactionsOutput = '';

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

        $className .= (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] && $hasReactionsSelected = true) ? ' reaction-selected' : '';

        if ($partsReaction[0] == 'thumb_up') {
            $partsReaction[0] = '&#xf109;';
        }

        if ($partsReaction[0] == 'thumb_down') {
            $partsReaction[0] = '&#xf108;';
        }

        if (isset($admin_mode)) {
            $reactionsOutput .= "<a data-bs-dismiss=\"modal\" onclick=\"lhinst.reaction($(this));\" data-identifier=\"{$partsReaction[2]}\" data-value=\"{$partsReaction[1]}\" data-msg-id=\"{$messageId}\" title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\" class=\"action-image reaction-item{$className}\">{$partsReaction[0]}</a>";
        } else {
            $reactionsOutput .= "<a linkaction=\"true\" data-action=\"setReaction\" data-action-arg='{\"data-identifier\":\"{$partsReaction[2]}\",\"data-payload\":\"{$partsReaction[1]}\",\"data-id\":{$messageId}}' title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\" class=\"action-image reaction-item{$className}\">{$partsReaction[0]}</a>";
        }
    }

    echo $reactionsOutput;

    ?>

    <hr/>
    <?php if (!isset($admin_mode)) : ?>
    <div class="mb-0" style="padding:0px 0 10px 0;">
        <div class="row">
            <div class="col-5">
                <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?>" class="btn btn-primary btn-sm w-100" data-action="confirmClose">
            </div>
            <div class="col-2"></div>
        </div>
    </div>
    <?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>