<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>

</head>
<body>

<div class="content-row">

<div class="row">
<div class="columns twelve">

<div class="row">
    <div class="columns six">
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>
    </div>
    <div class="columns six">
        <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
        <div class="float-break">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
        </div>
    </div>
</div>


<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/path.tpl.php'));?>

<div class="row">

    <div class="columns eight">
    <?php echo $Result['content']; ?>
    </div>

    <?php
    $pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
    $activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
    $closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
    $unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
    ?>
    <div class="columns four" id="right-column-page">

    		<dl class="tabs">
			    <dd class="active"><a href="#simpleAssignYou1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transfered to you directly');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transfered to you');?></a></dd>
			    <dd><a href="#simpleAssignYou2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transfered to your department');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transfered to your department');?></a></dd>
			</dl>

			<ul class="tabs-content">
			  <li id="simpleAssignYou1Tab" class="active">
			  		<div id="right-transfer-chats">
		        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
		            </div>
			  </li>
			  <li id="simpleAssignYou2Tab" >
			  		<div id="right-transfer-departments">
		        		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?></p>
		            </div>
			  </li>
			</ul>

        	<hr>
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
</body>
</html>