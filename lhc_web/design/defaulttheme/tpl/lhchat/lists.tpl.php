<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Choose what type of list you want to see');?></legend>

<ul>
    <li><a href="<?=erLhcoreClassDesign::baseurl('chat/pendingchats')?>">&raquo; <?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Pending chats');?></a></li>
    <li><a href="<?=erLhcoreClassDesign::baseurl('chat/activechats')?>">&raquo; <?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Active chats');?></a></li>
    <li><a href="<?=erLhcoreClassDesign::baseurl('chat/closedchats')?>">&raquo; <?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Closed chats');?></a></li>    
</ul>

</fieldset>