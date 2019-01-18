<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/generate_js_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_generate_js_enabled == true && $currentUser->hasAccessTo('lhsystem','generate_js_tab')) : ?>
<li role="presentation"><a href="#embed" aria-controls="embed" role="tab" data-toggle="tab"><?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_titles/embed_code_title.tpl.php'));?></a></li>
<?php endif; ?>