<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="navbar navbar-default navbar-lhc">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Menu');?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>
    </div>
          
      <ul class="nav collapse navbar-collapse navbar-nav navbar-right" id="bs-example-navbar-collapse-1">   
             
        <?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
		<li class="li-icon"><a href="javascript:void(0)" onclick="javascript:lhinst.chatTabsOpen()"><i class="icon-chat"></i></a></li>			
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats list');?></a></li>
		<?php if ($currentUser->hasAccessTo('lhchat','use_onlineusers')) : ?>
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online visitors');?></a></li>
		<?php endif;?>		
		<?php endif;?>
			
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_extension_multiinclude.tpl.php.tpl.php'));?>	
			
		<?php 
			$useQuestionary = $currentUser->hasAccessTo('lhquestionary','manage_questionary');
			$useFaq = $currentUser->hasAccessTo('lhfaq','manage_faq');
			$useChatbox = $currentUser->hasAccessTo('lhchatbox','manage_chatbox');
			$useBo = $currentUser->hasAccessTo('lhbrowseoffer','manage_bo');
			$useFm = $currentUser->hasAccessTo('lhform','manage_fm');
		?>		
		<?php if ($useFm || $useBo || $useChatbox || $useFaq || $useQuestionary) : ?>
		<li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Extra modules')?> <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
             
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/questionary.tpl.php'));?>
			  			  
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/faq.tpl.php'));?>
			  
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/chatbox.tpl.php'));?>
			  	
			  <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/browseoffer.tpl.php'));?>
              
              <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/form.tpl.php'));?>		  				  
			            
            </ul>
        </li>		
		<?php endif; ?> 
		
        <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
    	<li class="li-icon"><a href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="icon-tools"></i></a></li>
    	<?php endif; ?> 
    	 
    	<?php $hideULSetting = true;?>
		<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
			
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>                
      </ul>
    </div>
</nav>