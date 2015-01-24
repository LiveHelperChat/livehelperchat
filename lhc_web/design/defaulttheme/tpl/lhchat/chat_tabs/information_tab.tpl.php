<section>
<p class="title" data-section-title>
	<a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></a>
</p>
<div class="content overflow-x-scrollbar" data-section-content>

	<div class="section-container auto" data-section>
	  <section class="active">
	    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Information')?></a></p>
	    <div class="content" data-section-content id="main-user-info-tab-<?php echo $chat->id?>">
	      <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_info.tpl.php'));?>
	    </div>
	  </section>
	  <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks.tpl.php'));?>
		  
	  <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>
	
	  <?php if ( isset($fileData['active_admin_upload']) && $fileData['active_admin_upload'] == true && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator') ) : ?>
		  <section>
		    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Files')?></a></p>
		    <div class="content" data-section-content>
		      <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_tab_user_files.tpl.php'));?>
		    </div>
		  </section>
	  <?php endif; ?>
	   
	  <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot.tpl.php'));?>  
	</div>

	</div>
</section>