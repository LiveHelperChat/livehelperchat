<div role="tabpanel">	
	<ul class="nav nav-pills" role="tablist">
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_tab.tpl.php')); ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab_tab.tpl.php')); ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab_tab.tpl.php')); ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_tab.tpl.php')); ?>	    
	</ul>
	<div class="tab-content">
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info.tpl.php')); ?>	
	</div>
</div>