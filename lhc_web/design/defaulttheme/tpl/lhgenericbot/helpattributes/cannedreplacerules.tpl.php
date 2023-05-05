<?php if ($context == 'cannedreplacerules') : ?>
    <ul>
        <li><strong>{args.chat.referrer}</strong> `contains`. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Page where chat started');?></li>
        <li><strong>{args.chat.session_referrer}</strong> `contains`. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Referer from where visitor come to site.');?></li>
        <li><strong>{args.chat.chat_variables_array.&lt;variables&gt;}</strong> = <b>New</b></li>
        <li><strong>{args.chat.dep_id}</strong> = Department ID</li>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/cannedreplacerules_multiinclude.tpl.php'));?>
    </ul>

    <div class="row">
        <div class="col-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Chat ID to test against');?></label>
            <input id="test-pattern-chat-id" type="number" input="replace_pattern" class="form-control form-control-sm">
        </div>
        <div class="col-6 pb-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Pattern');?></label>
            <input type="text" value="{args.chat.id}" id="test-pattern-replace-pattern" class="form-control form-control-sm">
        </div>
        <div class="col-12">
            <button type="button" id="test-pattern-action" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Test');?></button>
        </div>
        <div class="col-12 pt-2">
            <div class="alert alert-info" id="pattern-replace-response"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Your response will appear here!');?></div>
        </div>
    </div>

    <script>
        $('#test-pattern-action').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'test_pattern' : $('#test-pattern-replace-pattern').val() }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
    </script>

<?php endif; ?>