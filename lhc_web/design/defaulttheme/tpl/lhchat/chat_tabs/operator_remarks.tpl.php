<div id="remarks-status-<?php echo $chat->id?>" class="icon-pencil pb10 success-color"></div>

<div>
<textarea class="form-control mh150" <?php if (!isset($hideActionBlock)) : ?>onkeyup="lhinst.saveRemarks('<?php echo $chat->id?>')"<?php else : ?>readonly<?php endif;?> id="ChatRemarks-<?php echo $chat->id?>"><?php echo htmlspecialchars($chat->remarks)?></textarea>
</div>
