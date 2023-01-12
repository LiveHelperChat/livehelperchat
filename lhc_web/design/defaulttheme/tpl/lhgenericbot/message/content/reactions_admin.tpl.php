<?php
if (isset($metaMessage['content'])) :
$reactions = explode("\n",trim($metaMessage['content']));
$reactionsOutput = '';
$hasReactionsSelected = false;
foreach ($reactions as $reaction) {
    $partsReaction = explode("|",$reaction);
    $className = strpos($partsReaction[0],'&#') === false ? ' pt-1 me-0 material-icons' : '';
    $className .= (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] && $hasReactionsSelected = true) ? ' reaction-selected' : '';
    if (isset($metaMessage['current'][$partsReaction[2]]) && $metaMessage['current'][$partsReaction[2]] == $partsReaction[1] ){
        $reactionsOutput .= "<span title=\"" . htmlspecialchars(isset($partsReaction[3]) ? $partsReaction[3] : '') . "\"  class=\"reaction-item{$className}\">{$partsReaction[0]}</span>";
    }
}
?>
<?php if (!empty($reactionsOutput)) : ?>
<div class="reactions-holder">
    <?php echo $reactionsOutput?>
</div>
<?php endif; endif; ?>