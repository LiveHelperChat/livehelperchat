<div role="tabpanel">
	<ul class="nav nav-pills" role="tablist" id="chat-tab-items-<?php echo $chat->id?>">	
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/tabs_order.tpl.php')); ?>
		    
	    <?php 
	    /**
	     * We cannot use some key => tpl here because we want template compilator to compile everything to single tpl file
	     * */	    
	    foreach ($chatTabsOrder as $tabItem) : ?>
	       <?php if ($tabItem == 'information_tab_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_tab.tpl.php')); ?>
	       <?php elseif ($tabItem == 'chat_translation_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation_tab.tpl.php'));?> 
	       <?php elseif ($tabItem == 'operator_remarks_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab.tpl.php'));?>
	       <?php elseif ($tabItem == 'information_tab_user_files_tab') : ?>
	       <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data?>
	    <?php if ( isset($fileData['active_admin_upload']) && $fileData['active_admin_upload'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator') ) : ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files_tab.tpl.php'));?>	
	    <?php endif; ?>
	       <?php elseif ($tabItem == 'operator_screenshot_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_tab.tpl.php'));?>
	       <?php elseif ($tabItem == 'footprint_tab_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab_tab.tpl.php')); ?>
	       <?php elseif ($tabItem == 'map_tab_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab_tab.tpl.php')); ?>
	       <?php elseif ($tabItem == 'online_user_info_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_tab.tpl.php')); ?>	
	       <?php elseif ($tabItem == 'extension_chat_tab_multiinclude') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_chat_tab_multiinclude.tpl.php'));?>  
	       <?php endif;?>
	    <?php endforeach; ?> 
	</ul>
	<div class="tab-content">
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab.tpl.php')); ?>	   
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab_content.tpl.php')); ?>
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files_tab_content.tpl.php'));?>	
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation.tpl.php'));?>
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot.tpl.php'));?>  
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_chat_tab_content_multiinclude.tpl.php'));?>  	
	</div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_post_chat_tabs_conatiner_multiinclude.tpl.php'));?>  	