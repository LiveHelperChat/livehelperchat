<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Dispatch event');
$modalSize = 'md';
$modalBodyClass = 'p-2'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div class="row">
        <div class="col-12">
            <label>Event name</label>
            <input id="event-name" value="" placeholder="chat.addmsguser" type="text" input="replace_pattern" class="form-control form-control-sm">
            <span class="badge bg-secondary action-image template-button" title="User message was added">chat.addmsguser</span> <span class="badge bg-secondary action-image template-button" title="Chat was closed">chat.close</span> <span class="badge bg-secondary action-image template-button" title="Chat started">chat.chat_started</span> <span class="badge bg-secondary action-image template-button" title="Admin messages was added to chat">chat.web_add_msg_admin</span> <span class="badge bg-secondary action-image template-button" title="New mail ticket created">mail.conversation_started</span> <span title="Reply to mail message was received" class="badge bg-secondary action-image template-button">mail.conversation_reply</span>
        </div>

        <div class="col-12 pb-2">
            <label>Event arguments</label>
            <textarea class="form-control form-control-sm" id="test-raw-value" rows="3" placeholder="Argument name || class name || object id"></textarea>
        </div>

        <div class="col-12">
            <div class="btn-group mb-2" role="group" aria-label="Basic example">
                <button type="button" id="dispatch-event-action" class="btn btn-sm btn-secondary">Dispatch</button>
            </div>
        </div>

        <div class="col-12 pt-2">
            <div class="alert alert-info mx300 fs12" id="pattern-replace-response">Your response will appear here!</div>
        </div>
    </div>

<script>
    var templatesEvents = {
        'chat.addmsguser' : 'chat||erLhcoreClassModelChat||<id>'+"\n"+'msg||erLhcoreClassModelmsg||<id>',
        'chat.web_add_msg_admin' : 'chat||erLhcoreClassModelChat||<id>'+"\n"+'msg||erLhcoreClassModelmsg||<id>',
        'chat.close' : 'chat||erLhcoreClassModelChat||<id>',
        'chat.chat_started' : 'chat||erLhcoreClassModelChat||<id>'+"\n"+'msg||erLhcoreClassModelmsg||<id>',
        'mail.conversation_started' : 'conversation||erLhcoreClassModelMailconvConversation||<id>'+"\n"+'mail||erLhcoreClassModelMailconvMessage||<id>',
        'mail.conversation_reply' : 'conversation||erLhcoreClassModelMailconvConversation||<id>'+"\n"+'mail||erLhcoreClassModelMailconvMessage||<id>'
    };

    $('.template-button').click(function (){
        $('#event-name').val($(this).text());
        $('#test-raw-value').val(templatesEvents[$(this).text()]);
    });

    $('#dispatch-event-action').click(function(){
        $.post(WWW_DIR_JAVASCRIPT + 'webhooks/dispatch', {'event_name': $('#event-name').val(), 'args' : $('#test-raw-value').val() }, function(data){
            $('#pattern-replace-response').html(data);
        });
    });

</script>


<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>