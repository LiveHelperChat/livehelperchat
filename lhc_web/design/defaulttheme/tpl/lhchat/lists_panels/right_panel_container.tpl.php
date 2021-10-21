<div class="<?php if (!isset($hideCard)) : ?>card<?php endif;?> panel-lhc"">
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/basic_chat_enabled.tpl.php'));?>
    
    <?php if ($basicChatEnabled == true) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/my_chats_panel.tpl.php'));?>
    	
    	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/pending_panel.tpl.php'));?>

	<?php endif;?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/right_panel_post_pending_multiinclude.tpl.php'));?>

    <?php if ($basicChatEnabled == true) : ?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/active_panel.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/unread_panel.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/closed_panel.tpl.php'));?>

    <?php $rightPanelMode = true; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bot_chats.tpl.php'));?>
    <?php unset($rightPanelMode); ?>
    
    <?php endif;?>
</div> 