<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_pre.tpl.php'));?>
<?php if ($operator_remarks_enabled == true) : ?>
<div id="remarks-status-<?php echo $chat->id?>" class="material-icons pb-1 text-success">&#xf6bb;</div>
<div>
<textarea class="form-control mh150" <?php if (!isset($hideActionBlock) && $canEditChat == true) : ?>onkeyup="lhinst.saveRemarks('<?php echo $chat->id?>')"<?php else : ?>readonly<?php endif;?> id="ChatRemarks-<?php echo $chat->id?>"><?php echo htmlspecialchars($chat->remarks)?></textarea>
</div>
<?php endif;?>