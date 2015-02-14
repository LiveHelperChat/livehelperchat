<?php if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
<div role="tabpanel" class="tab-pane" id="footprint-tab-chat-<?php echo $chat->id?>">
	<div class="mx170">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/footprint.tpl.php'));?>
	</div>
</div>
<?php endif;?>