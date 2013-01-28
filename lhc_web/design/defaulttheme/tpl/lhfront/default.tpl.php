<dl class="tabs" id="tabs">
  <dd class="active"><a href="#simple1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?></a></dd>
  <dd><a href="#simple2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?></a></dd>
  <dd><a href="#simple3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?></a></dd>
</dl>
<ul class="tabs-content" id="tabs-content">
  <li class="active" id="simple1Tab">
  	<div id="pending-chat-list"></div>
  	<br/>
  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
  </li>
  <li id="simple2Tab">
  	<div id="active-chat-list"></div>    
    <br/>
 	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
  </li>
  <li id="simple3Tab">  
  	<div id="closed-chat-list"></div>
  	<br/>
  	<a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
  </li>
</ul>
