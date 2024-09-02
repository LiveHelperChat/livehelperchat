<?php
$reactionsOutput = '';
if (isset($metaMessage['content'])) :
$reactions = explode("\n",trim($metaMessage['content']));
$hasReactionsSelected = false;
foreach ($reactions as $reaction) {
    $partsReaction = explode("|",$reaction);
    $className = strpos($partsReaction[0],'&#') === false ? ' pt-1 me-0 material-icons' : '';
    $className .= (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] && $hasReactionsSelected = true) ? ' reaction-selected' : '';
    if (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] ){
        $reactionsOutput .= "<span title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\"  class=\"reaction-item{$className}\">{$partsReaction[0]}</span>";
    }
}
endif; ?>

<?php if (isset($metaMessage['current_emoji']) && is_array($metaMessage['current_emoji'])) : ?>
    <?php foreach ($metaMessage['current_emoji'] as $reactionItem => $reactionValue) : ?>
        <?php $reactionsOutput .= '<span class="reaction-item pt-0 me-0 reaction-selected">'. $reactionItem . '</span>';?>
    <?php endforeach;?>
<?php endif; ?>

<?php if (isset($reactionsOutput) && !empty($reactionsOutput)) : ?>
<div class="reactions-holder">
    <?php echo $reactionsOutput?>
</div>
<?php  endif; ?>