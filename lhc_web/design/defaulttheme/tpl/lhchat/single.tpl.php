<script type="text/javascript">
	$(function() {
		/*var $tabs = $("#tabs").tabs({spinner: 'Loading...',cache: true,
		  add: function(event, ui) {		     
                $tabs.tabs('select', '#' + ui.panel.id); 
            }});*/
            
            <?php if (is_numeric($chat_id)) : ?>
            addChat(<?php echo $chat_id;?>,'<?php echo $chat_to_load->nick;?>');
            <?php endif; ?>
                          
            /*$('#tabs').bind('tabsshow', function(event, ui) {   
                $('#'+ui.panel.id+' .msgBlock').attr({ scrollTop: $('#'+ui.panel.id+' .msgBlock').attr("scrollHeight") }); 
            }); */         
	});
</script>
            
<?php /*?>
<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		
	</ul>		
</div>
*/ ?>

<dl class="tabs" id="tabs">
</dl>

<ul class="tabs-content" id="tabs-content">
</ul>

<script type="text/javascript">
function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    lhinst.setCloseWindowOnEvent(true);    
    window.focus();
}
          
</script>