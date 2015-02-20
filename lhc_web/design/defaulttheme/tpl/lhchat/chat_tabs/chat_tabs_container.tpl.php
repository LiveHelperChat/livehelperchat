<div role="tabpanel">	
	<ul class="nav nav-pills" role="tablist" id="chat-tab-items-<?php echo $chat->id?>">
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_tab.tpl.php')); ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation_tab.tpl.php'));?> 
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab.tpl.php'));?>
	    <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>
	    <?php if ( isset($fileData['active_admin_upload']) && $fileData['active_admin_upload'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator') ) : ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files_tab.tpl.php'));?>	
	    <?php endif; ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_tab.tpl.php'));?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab_tab.tpl.php')); ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab_tab.tpl.php')); ?>
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info_tab.tpl.php')); ?>	  
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_chat_tab_multiinclude.tpl.php'));?>  
	</ul>
	<div class="tab-content">
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab.tpl.php')); ?>
	   <div role="tabpanel" class="tab-pane" id="main-user-info-remarks-<?php echo $chat->id?>">
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks.tpl.php'));?>
	    </div>	    
	    <?php if ( isset($fileData['active_admin_upload']) && $fileData['active_admin_upload'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator') ) : ?>
		  <div role="tabpanel" class="tab-pane" id="main-user-info-files-<?php echo $chat->id?>">		   
		      <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files.tpl.php'));?>		    
		  </div>
	   <?php endif; ?>	   
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation.tpl.php'));?>
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot.tpl.php'));?>  
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/online_user_info.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_chat_tab_content_multiinclude.tpl.php'));?>  	
	</div>
</div>