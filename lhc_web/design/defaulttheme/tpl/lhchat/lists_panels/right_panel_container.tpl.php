<div class="<?php if (!isset($hideCard)) : ?>card<?php endif;?> panel-lhc"">

    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/basic_chat_enabled.tpl.php'));?>

    <?php
    if (!isset($dashboardOrder)) {
        $dashboardOrder = json_decode(erLhcoreClassModelUserSetting::getSetting('dwo',''),true);

        if ($dashboardOrder === null) {
            if ($dashboardOrder == '') {
                $dashboardOrder = json_decode(erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value,true);
            }
        }
    }
    $widgetsAvailable = erLhcoreClassChat::array_flatten($dashboardOrder); ?>

    <?php if ($basicChatEnabled == true) : ?>
        <?php $rightPanelMode = true; $customCardNoDuration = true;$customCardTitleClass = "fs14";$customCardNoId = true; ?>
        <?php if (in_array('my_chats',$widgetsAvailable)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/my_chats_panel.tpl.php'));?>
        <?php endif;?>

        <?php if (in_array('pending_chats',$widgetsAvailable)) : ?>
    	    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/pending_panel.tpl.php'));?>
	    <?php endif;?>
        <?php unset($rightPanelMode); unset($customCardNoDuration); unset($customCardTitleClass);unset($customCardNoId); ?>
	<?php endif;?>

    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/right_panel_post_pending_multiinclude.tpl.php'));?>

    <?php if ($basicChatEnabled == true) : ?>

    <?php $rightPanelMode = true; $customCardNoDuration = true;$customCardTitleClass = "fs14";$customCardNoId = true; ?>

    <?php if (in_array('active_chats',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/active_panel.tpl.php'));?>
    <?php endif; ?>

    <?php if (in_array('unread_chats',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/unread_panel.tpl.php'));?>
    <?php endif; ?>

    <?php if (in_array('my_mails',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/my_mails.tpl.php'));?>
    <?php endif; ?>

    <?php if (in_array('pmails',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/pmails.tpl.php'));?>
    <?php endif; ?>

    <?php if (in_array('amails',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/amails.tpl.php'));?>
    <?php endif; ?>

    <?php if (in_array('malarms',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/malarms.tpl.php'));?>
    <?php endif; ?>

    <?php if (in_array('bot_chats',$widgetsAvailable)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bot_chats.tpl.php'));?>
    <?php endif; ?>

        <?php unset($rightPanelMode); unset($customCardNoDuration); unset($customCardTitleClass);unset($customCardNoId); ?>
    
    <?php endif;?>
</div> 