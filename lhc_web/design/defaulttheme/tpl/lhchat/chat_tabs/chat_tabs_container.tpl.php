<div role="tabpanel">
	<ul class="nav nav-underline mb-1 border-bottom nav-small nav-fill chat-tab-sub-items" role="tablist" id="chat-tab-items-<?php echo $chat->id?>">
	    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/tabs_order.tpl.php')); ?>
		    
	    <?php
	    /**
	     * We cannot use some key => tpl here because we want template compilator to compile everything to single tpl file
	     * */	    
	    foreach ($chatTabsOrder as $tabItem) : ?>
	       <?php if ($tabItem == 'information_tab_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_tab.tpl.php')); ?>
	       <?php elseif ($tabItem == 'private_chat_tab') : ?>
           <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/private_chat_tab.tpl.php'));?>
	       <?php elseif ($tabItem == 'operator_remarks_tab') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab.tpl.php'));?>
	       <?php elseif ($tabItem == 'extension_chat_tab_multiinclude') : ?>
	       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_chat_tab_multiinclude.tpl.php'));?>  
	       <?php endif;?>
	    <?php endforeach; ?> 
	</ul>
	<div class="tab-content">
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab.tpl.php')); ?>
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/private_chat.tpl.php')); ?>
	   <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab_content.tpl.php')); ?>
       <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_chat_tab_content_multiinclude.tpl.php'));?>
	</div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/extension_post_chat_tabs_conatiner_multiinclude.tpl.php'));?>