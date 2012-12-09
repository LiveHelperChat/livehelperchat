<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Choose what type of list you want to see');?></h1>

<ul>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>">&raquo; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Pending chats');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>">&raquo; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Active chats');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>">&raquo; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists','Closed chats');?></a></li>    
</ul>

