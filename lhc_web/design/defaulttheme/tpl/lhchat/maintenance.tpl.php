<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/maintenance.tpl.php'));?>

<?php if (isset($closedchats)) : ?>
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Closed chats').' - '.$closedchats; ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif;?>

<?php if (isset($purgedchats)) : ?>
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Purged chats').' - '.$purgedchats; ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif;?>

<?php if (isset($updatedduration)) : ?>
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Chats duration was updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif;?>
 
<ul class="circle small-list">
	<li><a class="csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>/(action)/closechats"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Automatic chats close, click to close old chats');?></a></li>
	<li><a class="csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>/(action)/purgechats"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Automatic chats purge, click to purge old chats');?></a></li>
	<li><a class="csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>/(action)/updateduration"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Update chats duration by using new algorithm');?></a></li>
</ul>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>