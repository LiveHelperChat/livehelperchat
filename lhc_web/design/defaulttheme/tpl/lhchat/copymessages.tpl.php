<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copy messages')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<div class="row">
    <div class="col-4">
        <label><input type="checkbox" id="id-copy-messages-system" onchange="copyMessageContent()" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Include system messages')?></label>
    </div>
    <div class="col-4">
        <label><input type="checkbox" id="id-copy-messages-meta" onchange="copyMessageContent()" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Include meta messages')?></label>
    </div>
    <div class="col-4">
        <label><input type="checkbox" <?php if (isset($_GET['bot']) && $_GET['bot'] == 'true') : ?>checked="checked"<?php endif;?> id="id-copy-messages-bot" onchange="copyMessageContent()" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Include bot messages')?></label>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Messages')?></label>
    <textarea id="chat-copy-messages" rows="10" class="form-control"><?php echo htmlspecialchars($messages)?></textarea>
</div>

<script>
function copyMessageContent() {

    var args = {'system': 'false', 'meta': 'false'};

    if ($('#id-copy-messages-system').is(':checked')) {
        args['system'] = 'true';
    };

    if ($('#id-copy-messages-meta').is(':checked')) {
        args['meta'] = 'true';
    };

    if ($('#id-copy-messages-bot').is(':checked')) {
        args['bot'] = 'true';
    };

    $.getJSON(WWW_DIR_JAVASCRIPT  + 'chat/copymessages/<?php echo $chat->id?>', args, function(data){
        $('#chat-copy-messages').val(data.result);
    });

}
</script>

<button class="btn btn-info" data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copied!')?>" onclick="lhinst.copyMessages($(this))"><i class="material-icons">&#xE14D;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Copy to clipboard')?></button>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>