<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="navbar navbar-default navbar-lhc">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" >
        <span class="sr-only"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Menu');?></span>
        <i class="material-icons mr-0">menu</i>
      </button>
    
      <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>
      
      <button type="button" class="navbar-toggle navbar-toggle-visible pull-left" ng-click="lhc.toggleList('lmtoggle')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Expand or collapse left menu');?>">
        <span class="sr-only"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Menu');?></span>
        <i class="material-icons mr-0">menu</i>
      </button>
    </div>
      <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">    
          <ul class="nav navbar-nav navbar-inline">   
        	 <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>    	 
             <?php if ($parts_top_menu_chat_actions_enabled == true && $currentUser->hasAccessTo('lhchat','allowchattabs')) : ?>
                <li class="li-icon"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs');?>" href="javascript:void(0)" onclick="javascript:lhinst.chatTabsOpen()"><i class="material-icons">chat</i></a></li>		
             <?php endif;?>
    
        	 <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
               <li class="li-icon"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Configuration');?>" href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="material-icons">settings_applications</i></a></li>
             <?php endif; ?> 
                           
        	 <?php $hideULSetting = true;?>
    		 <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
          </ul>
          <ul class="nav navbar-nav">  
              <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?> 
          </ul>
      </div>
    </div>
</nav>