<script type="text/javascript">
	$(function() {
		var $tabs = $("#tabs").tabs({spinner: 'Loading...',cache: true,
		  add: function(event, ui) {
                $tabs.tabs('select', '#' + ui.panel.id);
            }});
            
            <?php if (is_numeric($chat_id)) : ?>
            addChat(<?php echo $chat_id;?>,'<?php echo $chat_to_load->nick;?>');
            <?php endif; ?>
                  
            /** start chats synchronizatipon **/
            chatsyncadmininterface();
            
            $('#tabs').bind('tabsshow', function(event, ui) {   
                $('#'+ui.panel.id+' .msgBlock').attr({ scrollTop: $('#'+ui.panel.id+' .msgBlock').attr("scrollHeight") }); 
            });
	});
</script>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-hover ui-state-active "><a href="#tabs-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chattabs','Pending confirm');?></a></li>
		<li class="ui-state-default ui-corner-top "><a href="#tabs-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chattabs','Active chats');?></a></li>
		<li class="ui-state-default ui-corner-top "><a href="#tabs-3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chattabs','Closed chats');?></a></li>
	</ul>
	
	<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">	
    	<div id="pending-chat-list">
    	    
        </div>       
	</div>
	
	<div id="tabs-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom">	
    	<div id="active-chat-list">
    	    
    	</div>	
	</div>
	
	<div id="tabs-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom">	
	     <div id="closed-chat-list">  
    	   
	    </div>
	</div>	
	
</div>

<script type="text/javascript">

function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    window.focus();
}
            
            
</script>