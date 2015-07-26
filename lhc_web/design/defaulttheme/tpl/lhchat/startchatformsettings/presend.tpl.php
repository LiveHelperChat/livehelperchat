<div id="messages" class="mb10">
    <div class="msgBlock" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php endif?> id="messagesBlock">
        <script type="text/javascript">
        
        $( "#form-start-chat" ).submit(function(){
            if ($('#messagesBlock > .message-row').size() == 0) {
            	jQuery('<div/>', {
    			    'class': 'message-row pending-storage',					   
    			    text: $('#id_Question').val()
    			}).appendTo('#messagesBlock');
			}
        });	
        </script>
   </div>
</div>