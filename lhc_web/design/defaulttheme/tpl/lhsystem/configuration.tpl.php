<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration');?></h1>

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
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/chat_embed_js.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/speech.tpl.php'));?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_tabs_content/tab_content_multiinclude.tpl.php'));?>
     
    </div>
</div>