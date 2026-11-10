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

            <p class="pt-2 mb-1"><small class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Leave Chat ID empty to test against a temporary chat. Values below are used only for that temporary chat.');?></small></p>

            <div class="row">
                <div class="col-6">
                    <div class="form-group mb-1">
                        <label class="mb-0"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Department ID');?></small></label>
                        <input type="text" class="form-control form-control-sm" id="test-dep-id" placeholder="0" value="">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-1">
                        <label class="mb-0"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat locale');?></small></label>
                        <input type="text" class="form-control form-control-sm" id="test-chat-locale" placeholder="lt-LT" value="">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group mb-1">
                        <label class="mb-0"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat variables (JSON)');?></small></label>
                        <textarea rows="4" class="form-control form-control-sm" id="test-chat-variables" placeholder='{"my_variable":"value"}'></textarea>
                    </div>
                </div>
            </div>

            <div id="output-test" class="ps-1 pt-1"></div>

            <script>
            $('#check-against-chat').click(function(){
                var params = {
                    chat_id: $('#test-chat-id').val(),
                    dep_id: $('#test-dep-id').val(),
                    chat_locale: $('#test-chat-locale').val(),
                    chat_variables: $('#test-chat-variables').val()
                };

                $.postJSON('<?php echo erLhcoreClassDesign::baseurl('genericbot/usecases')?>/trigger/<?php echo $trigger_id;?>/(arg1)/<?php echo htmlspecialchars($action_id, ENT_QUOTES)?>', params, function(data){
                    $('#output-test').html(data.output);
                }).fail(function(){
                    $('#output-test').html('<div class="alert alert-danger">Request failed</div>');
                });
            });
            </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>