<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/chat_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_chat_enabled == true && $currentUser->hasAccessTo('lhchat','use')) : ?>
<li role="presentation"><a href="#chatconfiguration" aria-controls="chatconfiguration" role="tab" data-toggle="tab"><?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_titles/live_help_configuration.tpl.php'));?></a></li>
<?php endif; ?>