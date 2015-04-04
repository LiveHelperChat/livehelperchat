<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration');?></h1>

<?php $currentUser = erLhcoreClassUser::instance(); ?>


<div role="tabpanel">

	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#system" aria-controls="system" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System');?></a></li>
    
        <?php if ($currentUser->hasAccessTo('lhsystem','generate_js_tab')) : ?>
        <li role="presentation"><a href="#embed" aria-controls="embed" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Embed code');?></a></li>
        <?php endif; ?>
        
        <?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
        <li role="presentation"><a href="#chatconfiguration" aria-controls="chatconfiguration" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Live help configuration');?></a></li>
        <?php endif; ?>
        
        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs/speech.tpl.php'));?>
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="system">

			<div class="row">
				<div class="col-md-6">
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System');?></h4>
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
        			    
        			    <?php if ($currentUser->hasAccessTo('lhsystem','configurelanguages') || $currentUser->hasAccessTo('lhsystem','changelanguage')) : ?>
        			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Languages configuration');?></a></li>
        			    <?php endif; ?>
        		    
        		        <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/expirecache.tpl.php'));?>
        			   
        			</ul>

				</div>
				<div class="col-md-6">
      	<?php if ($currentUser->hasAccessTo('lhuser','userlist') || $currentUser->hasAccessTo('lhuser','grouplist') || $currentUser->hasAccessTo('lhpermission','list')) : ?>
		  	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></h4>
					<ul class="circle small-list">
				    <?php if ($currentUser->hasAccessTo('lhuser','userlist')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/userlist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></a></li>
				    <?php endif; ?>
				    
				    <?php if ($currentUser->hasAccessTo('lhuser','userautologin')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/autologinconfig')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Auto login settings');?></a></li>
				    <?php endif; ?>
				    
				    <?php if ($currentUser->hasAccessTo('lhuser','grouplist')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/grouplist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of groups');?></a></li>
				    <?php endif; ?>
				
				    <?php if ($currentUser->hasAccessTo('lhpermission','list')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('permission/roles')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of roles');?></a></li>
				    <?php endif; ?>
			</ul>		     
		 <?php endif; ?>
      	</div>
			</div>

		</div>
    
    <?php if ($currentUser->hasAccessTo('lhsystem','generate_js_tab')) : ?>
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
    
    <?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
    <div role="tabpanel" class="tab-pane" id="chatconfiguration">
		<div class="row">
			<div class="col-md-6">
				<ul>
			    <?php if ($currentUser->hasAccessTo('lhdepartament','list')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('departament/departaments')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
			    <?php endif; ?>
			   
			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/blockusers.tpl.php'));?>
			    
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/listchatconfig')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat configuration');?></a></li>
			    
			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/administrategeoconfig.tpl.php'));?>
			    
			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/geoadjustment.tpl.php'));?>
				
				<?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/syncandsoundesetting')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Synchronization and sound settings');?></a></li>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/startchatformsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Start chat form settings');?></a></li>
			    <?php endif;?>
			    		    
	 			<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/translation.tpl.php'));?>
			    
	            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/cannedmsg.tpl.php'));?>
	           
			    <?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>

			   		<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive.tpl.php'));?>	

				    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/autoresponder.tpl.php'));?>
		    
			    <?php endif; ?>
	
			    <?php if ($currentUser->hasAccessTo('lhxmp','configurexmp')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('xmp/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','XMPP settings');?></a></li>
			    <?php endif; ?>
		
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chats list');?></a></li>
	
			    <?php if ($currentUser->hasAccessTo('lhchatarchive','archive')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/archive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat archive');?></a></li>
			    <?php endif; ?>
				
				<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/statistic.tpl.php'));?>
			    
			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/maintenance.tpl.php'));?>
			    						    
			</ul>
			</div>

			<div class="col-md-6">
			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/files.tpl.php'));?>
			    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/theming.tpl.php'));?>
			</div>

		</div>


	</div>
    <?php endif;?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/speech.tpl.php'));?>
    
    </div>    
</div>