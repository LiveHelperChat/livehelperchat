<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhsystem','generatejs')) : ?>
<ul class="button-group round right-button">
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>" class="button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/embedcode')?>" class="button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
</ul>
<?php endif; ?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Choose what type of list you want to see');?></h1>

<ul class="circle small-list">
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Pending chats');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Active chats');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Closed chats');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/operatorschats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Operators chats');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Chats with unread messages');?></a></li>
</ul>

