<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_content_chat_enabled == true && $currentUser->hasAccessTo('lhchat','use')) : ?>
<div role="tabpanel" class="tab-pane" id="chatconfiguration">
    <div class="row">
        <div class="col-md-6">
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/archive.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/blocking.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/look_and_feel.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/integration.tpl.php'));?>
        </div>
        <div class="col-md-6">
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/automation.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/files.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/bot.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/voice_video.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/notifications.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/group_chat.tpl.php'));?>
        </div>
    </div>
</div>
<?php endif;?>
    
    
