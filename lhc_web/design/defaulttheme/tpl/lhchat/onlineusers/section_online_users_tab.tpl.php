<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users_tab_pre.tpl.php')); ?>
<?php if ($chat_onlineusers_section_online_users_tab_enabled == true && $currentUser->hasAccessTo('lhchat', 'use_onlineusers') == true) : ?>
<li role="presentation" class="nav-item text-center"><a class="nav-link pl-2 pr-2 pt-0 pb-0" ng-click="lhc.currentPanel = 'onlineusers';lhc.current_chat_id = 0" href="#onlineusers" aria-controls="onlineusers" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors list');?>"><i class="material-icons mr-0">&#xf643;</i></a></li>
<?php endif;?>
