<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Test output');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

            <div class="input-group input-group-sm">
                <span class="input-group-text" id="basic-addon1"><span class="material-icons me-0">vpn_key</span></span>
                <input type="text" class="form-control form-control-sm" id="test-chat-id" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat ID');?>" value="">
                <button type="button" id="check-against-chat" class="btn btn-sm btn-secondary" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Test');?></button>
            </div>

            <div id="output-test" class="ps-1 pt-1"></div>

            <script>
            $('#check-against-chat').click(function(){
                var chatId = $('#test-chat-id').val();
                $.postJSON('<?php echo erLhcoreClassDesign::baseurl('genericbot/usecases')?>/trigger/<?php echo $trigger_id;?>/(arg1)/<?php echo htmlspecialchars($action_id, ENT_QUOTES)?>', {chat_id: chatId}, function(data){
                    $('#output-test').html(data.output);
                }).fail(function(){
                    $('#output-test').html('<div class="alert alert-danger">Request failed</div>');
                });
            });
            </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>