<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration');?></h1>

<?php $currentUser = erLhcoreClassUser::instance(); ?>


<div class="section-container auto" data-section="auto" id="tabs">

  <section>
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System');?></a></p>
    <div class="content" data-section-content>
      <div>
      
      <div class="row">
      	<div class="columns small-6">
      	
      	 <ul class="circle small-list">
	      		<?php if ($currentUser->hasAccessTo('lhsystem','timezone')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/timezone')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Time zone settings');?></a></li>
			    <?php endif; ?>
			    
			    <?php if ($currentUser->hasAccessTo('lhsystem','performupdate')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/update')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Update information');?></a></li>
			    <?php endif; ?>
			    
			    <?php if ($currentUser->hasAccessTo('lhsystem','configuresmtp')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/smtp')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','SMTP settings');?></a></li>
			    <?php endif; ?>
		    
			    <?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>		    
				    <?php if ($currentUser->hasAccessTo('lhsystem','changetemplates')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/EmailTemplate"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','E-mail templates');?></a></li>
				    <?php endif; ?>			    
			    <?php endif;?>
			    
			    <?php if ($currentUser->hasAccessTo('lhsystem','configurelanguages') || $currentUser->hasAccessTo('lhsystem','changelanguage')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Languages configuration');?></a></li>
			    <?php endif; ?>
		    
			    <?php if ($currentUser->hasAccessTo('lhsystem','expirecache')) : ?>		
					<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/expirecache')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Clean cache');?></a></li>			
				<?php endif; ?>
			</ul>
			
      	</div>
      	<div class="columns small-6">
      	<?php if ($currentUser->hasAccessTo('lhuser','userlist') || $currentUser->hasAccessTo('lhuser','grouplist') || $currentUser->hasAccessTo('lhpermission','list')) : ?>
		  	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></h4>		    
			<ul class="circle small-list">
				    <?php if ($currentUser->hasAccessTo('lhuser','userlist')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('user/userlist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Users');?></a></li>
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
    </div>
   </section>
   
   <section>
    <p class="title" data-section-title><a href="#panel4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Embed code');?></a></p>
    <div class="content" data-section-content>
      <div>
      	<?php if ($currentUser->hasAccessTo('lhsystem','generatejs')) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Live help embed code');?></h4>
			<ul class="circle small-list">
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		<?php endif; ?>
	
		<?php if ($currentUser->hasAccessTo('lhfaq','manage_faq')) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','FAQ embed code');?></h4>
			<ul class="circle small-list">
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		<?php endif; ?>
	
		<?php if ($currentUser->hasAccessTo('lhquestionary','manage_questionary')) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Questionary embed code');?></h4>
			<ul class="circle small-list">
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		<?php endif; ?>
	
		<?php if ($currentUser->hasAccessTo('lhchatbox','manage_chatbox')) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chatbox embed code');?></h4>
			<ul class="circle small-list">
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		<?php endif; ?>
	
		<?php if ($currentUser->hasAccessTo('lhbrowseoffer','manage_bo')) : ?>
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Browse offers embed code');?></h4>
			<ul class="circle small-list">
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('browseoffer/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Embed code');?></a></li>
			</ul>
		<?php endif; ?>
  	  </div>
    </div>
  </section>
      
  <?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
  <section>
    <p class="title" data-section-title><a href="#panel3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Live help configuration');?></a></p>
    <div class="content" data-section-content>
      <div>
      	<ul class="circle small-list">
		    <?php if ($currentUser->hasAccessTo('lhdepartament','list')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('departament/departaments')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Departments');?></a></li>
		    <?php endif; ?>
		   
		    <?php if ($currentUser->hasAccessTo('lhchat','allowblockusers')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Blocked users');?></a></li>
		    <?php endif; ?>
		    
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/listchatconfig')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat configuration');?></a></li>
		    
		    <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','GEO detection configuration');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/syncandsoundesetting')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Synchronization and sound settings');?></a></li>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/startchatformsettings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Start chat form settings');?></a></li>
		    <?php endif; ?>

		    <?php if ($currentUser->hasAccessTo('lhchat','administratecannedmsg')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/cannedmsg')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Canned messages');?></a></li>
		    <?php endif; ?>

		    <?php if ($currentUser->hasAccessTo('lhabstract','use')) : ?>
		   			    
			    <?php if ($currentUser->hasAccessTo('lhchat','administrateinvitations')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ProactiveChatInvitation"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Pro active chat invitations');?></a></li>
			    <?php endif;?>
			    
			    <?php if ($currentUser->hasAccessTo('lhchat','administrateresponder')) : ?>
			    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/AutoResponder"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Auto responder');?></a></li>
			    <?php endif;?>
			    		    
		    <?php endif; ?>

		    <?php if ($currentUser->hasAccessTo('lhxmp','configurexmp')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('xmp/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','XMPP settings');?></a></li>
		    <?php endif; ?>

		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chats list');?></a></li>

		    <?php if ($currentUser->hasAccessTo('lhchatarchive','archive')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/archive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat archive');?></a></li>
		    <?php endif; ?>
		    
			<?php if ($currentUser->hasAccessTo('lhchat','viewstatistic')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/statistic')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Statistic');?></a></li>
			<?php endif; ?>
		    
			<?php if ($currentUser->hasAccessTo('lhchat','maintenance')) : ?>
				    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Maintenance');?></a></li>
			<?php endif; ?>

		    
		</ul>
  	  </div>
    </div>
  </section>
  <?php endif;?>

  
  
  <?php if ($currentUser->hasAccessTo('lhform','manage_fm')) : ?>  
  <section>
    <p class="title" data-section-title><a href="#panel5"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Forms');?></a></p>
    <div class="content" data-section-content>
      <div>
	    <ul class="circle small-list">
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Form"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','List of forms');?></a></li>
		    
		    <?php if ($currentUser->hasAccessTo('lhform','generate_js')) : ?>	
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('form/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Page embed code');?></a></li>
		    <?php endif;?>	    
		</ul>
      </div>
    </div>
   </section>
   <?php endif; ?>
    
   <?php if ($currentUser->hasAccessTo('lhfile','use') || $currentUser->hasAccessTo('lhfile','file_list')) : ?>
   <section>
    <p class="title" data-section-title><a href="#panel6"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Files');?></a></p>
    <div class="content" data-section-content>
      <div>
	    <ul class="circle small-list">
		    <?php if ($currentUser->hasAccessTo('lhfile','use')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('file/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Files upload configuration');?></a></li>
		    <?php endif; ?>
		
		    <?php if ($currentUser->hasAccessTo('lhfile','file_list')) : ?>
		    <li><a href="<?php echo erLhcoreClassDesign::baseurl('file/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of files');?></a></li>
		    <?php endif; ?>   
		</ul>
      </div>
    </div>
   </section>
   <?php endif; ?>
    
   
           
   <?php if ($currentUser->hasAccessTo('lhdocshare','manage_dc')) : ?>	
   <section>
    <p class="title" data-section-title><a href="#panel7"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Documents');?></a></p>
    <div class="content" data-section-content>
      <div>
	   	<ul class="circle small-list">
	    	<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Index');?></a></li>
		</ul>
      </div>
    </div>
   </section>
   <?php endif; ?>   
 
</div>












