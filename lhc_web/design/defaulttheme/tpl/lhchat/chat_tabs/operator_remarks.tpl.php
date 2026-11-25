<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_pre.tpl.php'));?>
<?php if ($operator_remarks_enabled == true) : ?>

<label class="pt-2"><span id="remarks-status-<?php echo $chat->id?>" class="material-icons pb-1 text-success">mode_edit</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat notes')?></label>

<div class="pb-2">
<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','These are saved per chat')?>" class="form-control mh150" <?php if (!isset($hideActionBlock) && $canEditChat == true) : ?>onkeyup="lhinst.saveRemarks('<?php echo $chat->id?>')"<?php else : ?>readonly<?php endif;?> id="ChatRemarks-<?php echo $chat->id?>"><?php echo htmlspecialchars($chat->remarks)?></textarea>
</div>

<?php if (($online_user = $chat->online_user) !== false) : ?>
<label><span id="remarks-status-online-<?php echo $online_user->id?>" class="material-icons pb-1 text-success">mode_edit</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor notes. Will remain the same for the same visitor chats.')?></label>
<textarea class="form-control mh150" onkeyup="lhinst.saveNotes('<?php echo $online_user->id?>')" id="OnlineRemarks-<?php echo $online_user->id?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','These are saved per visitor')?>"><?php echo htmlspecialchars($online_user->notes)?></textarea>
<?php endif; ?>

<?php endif;?>