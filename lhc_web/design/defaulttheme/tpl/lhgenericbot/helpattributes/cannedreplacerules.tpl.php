<?php if ($context == 'cannedreplacerules') : ?>
    <ul class="mx300">
        <li><strong>{args.chat.referrer}</strong> `contains`. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Page where chat started');?></li>
        <li><strong>{args.chat.session_referrer}</strong> `contains`. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Referer from where visitor come to site.');?></li>
        <li><strong>{args.chat.chat_variables_array.&lt;variables&gt;}</strong> = <b>New</b></li>
        <li><strong>{args.chat.dep_id}</strong> = Department ID</li>
        <li><strong>{args.chat.department_role.role}</strong> = Department role <b>vip</b></li>
        <li><strong>{condition.&lt;condition_identifier&gt;}</strong> = Condition identifier <b>valid</b> OR <b>not_valid</b></li>
        <li><strong>{args.chat.chat_variables_array.playerClass}</strong> condition <strong>Text Like</strong> E.g <strong>vip_1,vip_2</strong> <a target="_blank" href="https://doc.livehelperchat.com/docs/bot/triggers#custom-text-matching">exact match.</a></li>
        <li><strong>{args.chat.chat_variables_array.playerClass}</strong> condition <strong>Contains</strong> E.g <strong>vip_</strong> would match <b>vip_1,vip_2</b> </li>
        <li><strong>{args.chat.plain_user_name}</strong> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Operator nick');?> </li>
        <li><strong>{args.chat.user.name_support}</strong> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Operator nick');?> </li>
        <li><strong>{args.chat.online_user.previous_chat.chat_variables_array.vip}</strong> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Previous chat additional chat variable');?> </li>
        <?php if (isset($_GET['canned'])) : ?>
            <li><strong>{nick}</strong> = <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Visitor nick');?></li>
            <li><strong>{email}</strong> = <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Visitor e-mail');?></li>
            <li><strong>{phone}</strong> = <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Phone');?></li>
            <li><strong>{operator}</strong> = <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Operator nick');?></li>
        <?php endif; ?>
        <?php foreach (erLhcoreClassModelCannedMsgReplace::getList(array(
            'sort' => 'repetitiveness DESC', // Default translation will be the last one if more than one same identifier is found
            'limit' => false)) as $repacelabeVariable): ?>
            <li><strong>{<?php echo htmlspecialchars($repacelabeVariable->identifier)?>}</strong> = <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'canned message replaceable variables');?></li>
        <?php endforeach; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/cannedreplacerules_multiinclude.tpl.php'));?>
    </ul>

    <?php if (isset($_GET['rest_api'])) : ?>
        <p class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If you are using those vars in Rest API calls make sure you use double brackets E.g');?> <b>{{args.chat.id}}</b></p>
    <?php endif; ?>

    <div class="row">
        <div class="col-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Chat ID or Message ID to explore');?></label>
            <input id="test-pattern-chat-id" type="number" input="replace_pattern" class="form-control form-control-sm">
        </div>
        <div class="col-6 pb-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Attribute to extract');?></label>
            <input type="text" placeholder="{args.chat.id}" value="{args.chat.id}" id="test-pattern-replace-pattern" class="form-control form-control-sm">
        </div>

        <div class="col-6 pb-2">
            <select class="form-control form-control-sm" id="test-pattern-comparator">
                <option value="" selected="">--Choose--</option>
                <option value="gt">&gt;</option>
                <option value="gte">&gt;=</option>
                <option value="lt">&lt;</option>
                <option value="lte">&lt;=</option>
                <option value="eq">=</option>
                <option value="neq">!=</option>
                <option value="like">Text like</option>
                <option value="notlike">Text not like</option>
                <option value="contains">Contains</option>
            </select>
        </div>
        <div class="col-6 pb-2">
            <input type="text" placeholder="Text pattern E.g *car*,*bus*" value="" id="test-text-pattern" class="form-control form-control-sm">
        </div>

        <div class="col-12">
            <div class="btn-group mb-2" role="group" aria-label="Basic example">
                <button type="button" id="test-pattern-action" class="btn btn-sm btn-secondary"><span class="material-icons">regular_expression</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Extract chat attribute');?></button>
                <button type="button" id="extract-pattern-action" class="btn btn-sm btn-secondary"><span class="material-icons">zoom_in</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Explore all possible chat attributes');?></button>
                <button type="button" id="test-text-pattern-action" class="btn btn-sm btn-secondary"><span class="material-icons">code</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Test chat text pattern');?></button>
            </div>

            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" id="test-pattern-action-mail" class="btn btn-sm btn-secondary"><span class="material-icons">regular_expression</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Extract mail attribute');?></button>
                <button type="button" id="extract-pattern-action-mail" class="btn btn-sm btn-secondary"><span class="material-icons">zoom_in</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Explore all possible mail attributes');?></button>
                <button type="button" id="test-text-pattern-action-mail" class="btn btn-sm btn-secondary"><span class="material-icons">code</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Test mail text pattern');?></button>
            </div>

        </div>
        <div class="col-12 pt-2">
            <div class="alert alert-info mx300 fs12" id="pattern-replace-response"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Your response will appear here!');?></div>
        </div>
    </div>

    <script>
        $('#test-pattern-action').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'test_pattern' : $('#test-pattern-replace-pattern').val() }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
        $('#extract-pattern-action').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'extract_action':true }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
        $('#test-text-pattern-action').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'comparator' : $('#test-pattern-comparator').val(), 'test_pattern' : $('#test-pattern-replace-pattern').val(), 'text_pattern' : $('#test-text-pattern').val()  }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
        $('#test-pattern-action-mail').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'mail': true,'test_pattern' : $('#test-pattern-replace-pattern').val() }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
        $('#extract-pattern-action-mail').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'mail': true, 'extract_action':true }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
        $('#test-text-pattern-action-mail').click(function(){
            $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-pattern-chat-id').val(), {'mail': true, 'comparator' : $('#test-pattern-comparator').val(), 'test_pattern' : $('#test-pattern-replace-pattern').val(), 'text_pattern' : $('#test-text-pattern').val()  }, function(data){
                $('#pattern-replace-response').html(data);
            });
        });
    </script>

<?php endif; ?>