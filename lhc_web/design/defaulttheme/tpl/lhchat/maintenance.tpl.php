<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Maintenance');?></h1>

<?php if (isset($closedchats)) : ?>
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Closed chats').' - '.$closedchats; ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif;?>

<?php if (isset($purgedchats)) : ?>
<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Purged chats').' - '.$purgedchats; ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif;?>

<ul class="circle small-list">
	<li><a class="csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>/(action)/closechats"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Automatic chats close, click to close old chats');?></a></li>
	<li><a class="csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('chat/maintenance')?>/(action)/purgechats"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Automatic chats purge, click to purge old chats');?></a></li>
</ul>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>