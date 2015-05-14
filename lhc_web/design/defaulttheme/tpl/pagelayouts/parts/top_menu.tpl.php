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
		
		<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions.tpl.php'));?>
		
		<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_online_users.tpl.php'));?>
			
		<?php endif;?>

		<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_modules_container.tpl.php.tpl.php'));?>	

        <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
    	<li class="li-icon"><a href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="icon-tools"></i></a></li>
    	<?php endif; ?> 
    	 
    	<?php $hideULSetting = true;?>
		<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
			
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>                
      </ul>
    </div>
</nav>