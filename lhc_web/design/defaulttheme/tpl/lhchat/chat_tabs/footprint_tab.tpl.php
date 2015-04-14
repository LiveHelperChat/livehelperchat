<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab_pre.tpl.php')); ?>
<?php if ($chat_chat_tabs_footprint_tab_enabled == true && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
<div role="tabpanel" class="tab-pane<?php if ($chatTabsOrderDefault == 'footprint_tab_tab') print ' active';?>" id="footprint-tab-chat-<?php echo $chat->id?>">
	<div class="mx170">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/footprint.tpl.php'));?>
	</div>
</div>
<?php endif;?>