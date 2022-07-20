<?php
$reactions = explode("\n",trim($metaMessage['content']));
$reactionsOutput = '';
$hasReactionsSelected = false;
foreach ($reactions as $reaction) {
    $partsReaction = explode("|",$reaction);

    $className = strpos($partsReaction[0],'&#') === false ? ' pt-1 mr-0 material-icons' : '';
    $className .= (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] && $hasReactionsSelected = true) ? ' reaction-selected' : '';
    $className .= htmlspecialchars(' reaction-id-' . $partsReaction[2] . '-' . $partsReaction[1]);

    if ($partsReaction[0] == 'thumb_up') {
        $partsReaction[0] = '&#xf109;';
    }
    
    if ($partsReaction[0] == 'thumb_down') {
        $partsReaction[0] = '&#xf108;';
    }

    $reactionsOutput .= "<a onclick=\"lhinst.reactionsClicked()\" data-action-type=\"reactions\" data-bot-action=\"button-click\" title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\" data-identifier=\"{$partsReaction[2]}\" data-payload=\"{$partsReaction[1]}\" data-id=\"{$messageId}\" class=\"action-image reaction-item{$className}\">{$partsReaction[0]}</a>";
}
?>

<div class="reactions-holder <?php if ($hasReactionsSelected == true) : ?>reactions-selected<?php endif;?> <?php if (isset($metaMessageData['content']['attr_options']['reactions_visible']) && $metaMessageData['content']['attr_options']['reactions_visible'] == true) : ?>d-block<?php endif;?>">
    <?php echo $reactionsOutput?>
</div>