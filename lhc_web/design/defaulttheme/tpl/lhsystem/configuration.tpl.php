<h1><?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_titles/configuration_title.tpl.php'));?></h1>

<?php $currentUser = erLhcoreClassUser::instance(); ?>

<div role="tabpanel">

	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#system" aria-controls="system" role="tab" data-toggle="tab"><?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_titles/system_title.tpl.php'));?></a></li>
        
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/generate_js.tpl.php'));?>
        
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/chat.tpl.php'));?>
         
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/speech.tpl.php'));?>
        
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/tab_multiinclude.tpl.php'));?>
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="system">

			<div class="row">
				<div class="col-md-6">

					<h4><?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_titles/system_title.tpl.php'));?></h4>

					<ul>
        	      		<?php if ($currentUser->hasAccessTo('lhsystem','timezone')) : ?>
        			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/timezone')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Time zone settings');?></a></li>
        			    <?php endif; ?>
        			    
        			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/performupdate.tpl.php'));?>
        			    
        			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/configuresmtp.tpl.php'));?>
        		    
        			    <?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>		    
        				    <?php if ($currentUser->hasAccessTo('lhsystem','changetemplates')) : ?>
        				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/EmailTemplate"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','E-mail templates');?></a></li>
        				    <?php endif; ?>			    
        			    <?php endif;?>
        		        
        		        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/languages.tpl.php'));?>
        		        
        		        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/expirecache.tpl.php'));?>
        			</ul>
				</div>
				
				<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/users_section.tpl.php'));?>
      	         
			</div>

		</div>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat_embed_js.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/speech.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/tab_content_multiinclude.tpl.php'));?>
     
    </div>
</div>