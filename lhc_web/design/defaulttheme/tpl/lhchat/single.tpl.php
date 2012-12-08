<script type="text/javascript">
	$(function() {
		var $tabs = $("#tabs").tabs({spinner: 'Loading...',cache: true,
		  add: function(event, ui) {		     
                $tabs.tabs('select', '#' + ui.panel.id); 
            }});
            
            <? if (is_numeric($chat_id)) : ?>
            addChat(<?=$chat_id;?>,'<?=$chat_to_load->nick;?>');
            <? endif; ?>
                          
            $('#tabs').bind('tabsshow', function(event, ui) {   
                $('#'+ui.panel.id+' .msgBlock').attr({ scrollTop: $('#'+ui.panel.id+' .msgBlock').attr("scrollHeight") }); 
            });            
	});
</script>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		
	</ul>		
</div>

<script type="text/javascript">
function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    lhinst.setCloseWindowOnEvent(true);
    
    window.focus();
}
          
</script>