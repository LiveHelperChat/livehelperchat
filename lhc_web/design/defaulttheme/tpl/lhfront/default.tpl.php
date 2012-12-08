<script type="text/javascript">
	$(function() {
		var $tabs = $("#tabs").tabs({spinner: 'Loading...',cache: true,
		  add: function(event, ui) {
                $tabs.tabs('select', '#' + ui.panel.id);
            }});
            
       $('#tabs').bind('tabsshow', function(event, ui) {   
                $('#'+ui.panel.id+' .msgBlock').attr({ scrollTop: $('#'+ui.panel.id+' .msgBlock').attr("scrollHeight") }); 
            });
	});
</script>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-hover ui-state-active "><a href="#tabs-1"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?></a></li>
		<li class="ui-state-default ui-corner-top "><a href="#tabs-2"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?></a></li>
		<li class="ui-state-default ui-corner-top "><a href="#tabs-3"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?></a></li>
  </ul>
	
	<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">	
    	<div id="pending-chat-list">
    	   
        </div>  
        <div class="all-list"><a href="<?=erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?> &raquo;</a></div>     
	</div>
	
	<div id="tabs-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom">	
    	<div id="active-chat-list">
    	    
    	</div>	
    	<div class="all-list"><a href="<?=erLhcoreClassDesign::baseurl('chat/activechats')?>"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?> &raquo;</a></div>   
	</div>
	
	<div id="tabs-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom">	
	     <div id="closed-chat-list">  
    	    
	    </div>
	    <div class="all-list"><a href="<?=erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?> &raquo;</a></div>   
	</div>	
	
</div>

<script type="text/javascript">
//lhinst.setwwwDir('<?=erLhcoreClassDesign::baseurl("/");?>');
</script>