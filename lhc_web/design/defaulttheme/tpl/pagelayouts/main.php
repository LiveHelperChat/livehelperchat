<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
	<head>
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
	</head>
<body>

<div class="content-row">
<div class="row">
<div class="columns large-12">

<div class="row">
    <div class="columns large-6">
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>
    </div>
    <div class="columns large-6">
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
        <div class="float-break">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
        </div>
    </div>
</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/path.tpl.php'));?>

<div class="row">

    <div class="columns large-8">
    	<?php echo $Result['content']; ?>
    </div>

    <?php
    $pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
    $activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
    $closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
    $unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
    ?>
    <div class="columns large-4" id="right-column-page">


			<div class="section-container auto" data-section="auto">
			  <section>
			    <p class="title" data-section-title><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transferred to you directly');?>" href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred to you');?><span class="tru-cnt"></span></a></p>
			    <div class="content" data-section-content>
			      <div id="right-transfer-chats">
		        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
		            </div>
			    </div>
			  </section>
			  <section>
			    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred to your department');?><span class="trd-cnt"></span></a></p>
			    <div class="content" data-section-content>
			      <div id="right-transfer-departments">
		        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
		            </div>
			    </div>
			  </section>
			</div>

    		<?php if ($pendingTabEnabled == true) : ?>
			<h5><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats');?></a></h5>
    		<div id="right-pending-chats">
        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
            </div>
        	<hr>
        	<?php endif;?>

        	<?php if ($activeTabEnabled == true) : ?>
			<h5><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats');?></a></h5>
    		<div id="right-active-chats">
        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
            </div>
        	<hr>
        	<?php endif;?>

			<?php if ($unreadTabEnabled == true) : ?>
	        <h5><a href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Unread messages');?></a></h5>
    		<div id="right-unread-chats">
        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
            </div>
            <hr>
			<?php endif;?>

        	<?php if ($closedTabEnabled == true) : ?>
	        <h5><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Closed chats');?></a></h5>
    		<div id="right-closed-chats">
        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
            </div>
        	<?php endif;?>


    </div>

</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>

</div>
</div>
</div>
<script type="text/javascript">chatsyncadmininterface();</script>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>