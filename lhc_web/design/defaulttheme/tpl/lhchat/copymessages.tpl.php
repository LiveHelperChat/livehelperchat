<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copy messages')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<label><input type="checkbox" onchange="copyMessageContent($(this))" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Include system messages')?></label>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Messages')?></label>
    <textarea id="chat-copy-messages" rows="10" class="form-control"><?php echo htmlspecialchars($messages)?></textarea>
</div>

<script>
function copyMessageContent(inst) {
    if (inst.is(':checked')) {
        $.getJSON(WWW_DIR_JAVASCRIPT  + 'chat/copymessages/<?php echo $chat->id?>/?system=true',function(data){
            $('#chat-copy-messages').val(data.result);
        });
    } else {
        $.getJSON(WWW_DIR_JAVASCRIPT  + 'chat/copymessages/<?php echo $chat->id?>/?system=false',function(data){
            $('#chat-copy-messages').val(data.result);
        });
    }
}
</script>

<button class="btn btn-info" data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copied!')?>" onclick="lhinst.copyMessages($(this))"><i class="material-icons">&#xE14D;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copy to clipboard')?></button>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>