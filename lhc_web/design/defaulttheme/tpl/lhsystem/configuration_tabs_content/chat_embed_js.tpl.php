<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat_embed_js_pre.tpl.php'));?>
<?php if ($system_configuration_tabs_content_chat_embed_js_enabled == true && $currentUser->hasAccessTo('lhsystem','generate_js_tab')) : ?>
<div role="tabpanel" class="tab-pane" id="embed">
	<div class="row">
	    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_embed.tpl.php'));?>
	    	    
	    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/faq_embed.tpl.php'));?>
	
	    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/questionary_embed.tpl.php'));?>
	
	    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chatbox_embed.tpl.php'));?>
	    
	    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/browse_offers_embed.tpl.php'));?>
	    
	    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/embed_multiinclude.tpl.php'));?>
     </div>
</div>	
<?php endif; ?>