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

    		<?php if ($pendingTabEnabled == true) : ?>
			<h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats');?></a></h5>
    		<div id="right-pending-chats">
        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
            </div>
        	<hr>
        	<?php endif;?>

        	<?php if ($activeTabEnabled == true) : ?>
			<h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats');?></a></h5>
    		<div id="right-active-chats">
        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
            </div>
        	<hr>
        	<?php endif;?>

        	<?php if ($closedTabEnabled == true) : ?>
	        <h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transfered chats');?></a></h5>
    		<div id="right-transfer-chats">
        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
            </div>
        	<hr>
        	<?php endif;?>

        	<?php if ($unreadTabEnabled == true) : ?>
	        <h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Unread messages');?></a></h5>
    		<div id="right-unread-chats">
        		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Empty...');?>
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