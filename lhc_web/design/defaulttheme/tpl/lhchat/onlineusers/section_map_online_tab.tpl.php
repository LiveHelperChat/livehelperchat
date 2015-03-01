<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online_tab_pre.tpl.php')); ?>
<?php if ($chat_onlineusers_section_map_online_tab_enabled == true) : ?>
<li role="presentation"><a id="map-activator" href="#map" aria-controls="map" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online users on map');?>"><i class="icon-globe"></i></a></li>
<?php endif;?>