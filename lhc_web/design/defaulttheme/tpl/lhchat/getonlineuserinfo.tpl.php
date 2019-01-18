<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<a href="<?php echo htmlspecialchars(trim($online_user->current_page))?>" class="no-wrap fs12"><?php echo htmlspecialchars(trim($online_user->referrer))?></a>

<div class="online-user-info">
    <div role="tabpanel">
    	<!-- Nav tabs -->
    	<ul class="nav nav-tabs" role="tablist">
    		<li role="presentation" class="active"><a href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></a></li>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab_tab_pre.tpl.php')); ?>
    		
    		<?php if ($chat_chat_tabs_footprint_tab_tab_enabled == true && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
    		<li role="presentation"><a href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Footprint')?></a></li>
    		<?php endif;?>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats_tab.tpl.php')); ?>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_pre.tpl.php')); ?>
    		
    		<?php if ($operator_screenshot_enabled == true) : ?>
    		  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/screenshot_tab.tpl.php')); ?>
    		<?php endif;?>
    		
    		    		
    		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/notes_tab.tpl.php')); ?>
    	</ul>
    
    	<!-- Tab panes -->
    	<div class="tab-content">
    		<div role="tabpanel" class="tab-pane active" id="panel1">
    		  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>
    		  
    		  <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_pre.tpl.php'));?>
    		  
    		  <?php if ($system_configuration_proactive_enabled == true) : ?>
    		  <input type="button" class="btn btn-default" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $online_user->id?>'});" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"/>
    		  <?php endif;?>
    		  
    		</div>
    		
    		<?php if ( $chat_chat_tabs_footprint_tab_tab_enabled == true && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
    		<div role="tabpanel" class="tab-pane" id="panel2">
        		<ul class="foot-print-content list-unstyled mb0" style="max-height: 170px;">
        		<?php foreach (erLhcoreClassModelChatOnlineUserFootprint::getList(array('filter' => array('online_user_id' => $online_user->id))) as $footprintItems) : ?>
        		<li>
        		<a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($footprintItems->page);?>"><?php echo $footprintItems->time_ago?> | <?php echo htmlspecialchars($footprintItems->page);?></a>
        		</li>
        		<?php endforeach;?>
        		</ul>
    		</div>
    		<?php endif;?>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats.tpl.php')); ?>
    		
    		<?php if ($operator_screenshot_enabled == true) : ?>
    		  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/screenshot.tpl.php')); ?>
    		<?php endif;?>
    		
    		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/notes.tpl.php')); ?>
    	</div>
    </div>
</div>


<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>