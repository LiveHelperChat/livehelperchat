<div id="messages" class="mb10">
    <div class="msgBlock" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php endif?> id="messagesBlock">
        <script type="text/javascript">  
        var visitorTitle =  <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Me'),ENT_QUOTES))?>;     
        $( "#form-start-chat" ).submit(function(){
            if ($('#messagesBlock > .message-row').size() == 0 && $(this).attr('key-up-started') != 1) {
            	jQuery('<div/>', {
    			    'class': 'message-row response',					   
    			    text: $('#id_Question').val()
    			}).appendTo('#messagesBlock').prepend('<span class="usr-tit vis-tit">'+<?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Me'),ENT_QUOTES))?>+'</span>');
			}
        });	
        </script>
   </div>
</div>