<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/map_tab_pre.tpl.php'));?>	
<?php if ($information_tab_map_tab_enabled == true) : ?>
<div role="tabpanel" class="tab-pane<?php if ($chatTabsOrderDefault == 'map_tab_tab') print ' active';?>" id="map-tab-chat-<?php echo $chat->id?>">
        <?php if ($chat->lat != 0 && $chat->lon) : ?>
            <?php $geo_location_data = erLhcoreClassModelChatConfig::fetch('geo_location_data')->data; ?>
		    <a target="_blank" href="//maps.google.com/maps?t=h&q=<?php echo $chat->lat?>,<?php echo $chat->lon?>&z=17&hl=en&z=11&t=m">
                <img id="chat-map-img-<?php echo $chat->id?>" data-src="//maps.google.com/maps/api/staticmap?<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false)) {echo 'key=' , erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false) , '&';} elseif (isset($geo_location_data['gmaps_api_key'])) {echo 'key=' ,$geo_location_data['gmaps_api_key'], '&';}?>zoom=13&size=400x300&maptype=roadmap&center=<?php echo $chat->lat?>,<?php echo $chat->lon?>&sensor=false&markers=color:green|<?php echo $chat->lat?>,<?php echo $chat->lon?>" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg" alt="" title="<?php echo $chat->lat?>,<?php echo $chat->lon?>" />
            </a>
            <script>
                $('#map-tab-chat-link-<?php echo $chat->id?>').click(function () {
                    $('#chat-map-img-<?php echo $chat->id?>').attr('src',$('#chat-map-img-<?php echo $chat->id?>').attr('data-src'));
                });
            </script>
		<?php else : ?>
		    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Could not detect. Make sure that GEO detection is enabled.')?></p>
		<?php endif;?>
</div>
<?php endif;?>
