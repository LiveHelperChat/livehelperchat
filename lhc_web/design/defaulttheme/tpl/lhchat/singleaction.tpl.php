<?php if ($singleAction == 'map') : ?>

<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Location on map')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div>
    <?php $geo_location_data = erLhcoreClassModelChatConfig::fetch('geo_location_data')->data; ?>
    <a target="_blank" href="//maps.google.com/maps?t=h&q=<?php echo $chat->lat?>,<?php echo $chat->lon?>&z=17&hl=en&z=11&t=m">
        <img id="chat-map-img-<?php echo $chat->id?>" src="//maps.google.com/maps/api/staticmap?<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false)) {echo 'key=' , erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false) , '&';} elseif (isset($geo_location_data['gmaps_api_key'])) {echo 'key=' ,$geo_location_data['gmaps_api_key'], '&';}?>zoom=13&size=400x300&maptype=roadmap&center=<?php echo $chat->lat?>,<?php echo $chat->lon?>&sensor=false&markers=color:green|<?php echo $chat->lat?>,<?php echo $chat->lon?>" alt="" title="<?php echo $chat->lat?>,<?php echo $chat->lon?>" />
    </a>
</div>


<?php elseif ($singleAction == 'screenshot') : ?>

    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_pre.tpl.php')); ?>
    <?php if ($operator_screenshot_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','take_screenshot')) : ?>
        <?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Screenshot')?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

        <div class="btn-group" role="group" aria-label="...">
            <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Take user screenshot')?>" class="btn btn-secondary" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_screenshot')" />
            <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="btn btn-secondary" onclick="lhinst.updateScreenshot('<?php echo $chat->id?>')" />
        </div>

        <div id="user-screenshot-container-<?php echo $chat->id?>">
            <?php if ($chat->screenshot !== false) : ?>
                <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $chat->screenshot->date_front?></h5>
                <a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>/(inline)/true" target="_blank" class="screnshot-container">
                    <img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>" alt="" />
                </a>
                <script>
                    $(document).ready(function(){
                        $('.screnshot-container').zoom();
                    });
                </script>
            <?php else : ?>
            <br/>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Empty...')?>
            <?php endif;?>
        </div>

    <?php endif; ?>

<?php elseif ($singleAction == 'translation') : ?>

    <?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Automatic translation')?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <?php $dataChatTranslation = !isset($dataChatTranslation) ? erLhcoreClassModelChatConfig::fetch('translation_data')->data_value : $dataChatTranslation; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_translation_pre.tpl.php')); ?>
    <?php if ($chat_translation_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhtranslation','use')) : ?>
        <?php
        // This values comes from tab template
        if ($dataChatTranslation['enable_translations'] && $dataChatTranslation['enable_translations'] == true) : ?>
            <div id="main-user-info-translation-<?php echo $chat->id?>">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Visitor language');?></label>
                            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                                'input_name'     => 'chat_locale_'.$chat->id,
                                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatically detected'),
                                'selected_id'    => $chat->chat_locale,
                                'css_class'      => 'form-control',
                                'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
                            )); ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','My language');?></label>
                            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                                'input_name'     => 'chat_locale_to_'.$chat->id,
                                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatically detected'),
                                'selected_id'    => ($chat->chat_locale_to != '' ? $chat->chat_locale_to : substr(erLhcoreClassSystem::instance()->Language, 0, 2)),
                                'css_class'      => 'form-control',
                                'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
                            )); ?>
                        </div>
                    </div>
                </div>

                <label>
                    <input id="chat_auto_translate_<?php echo $chat->id?>" type="checkbox" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','On save automatically translate old chat messages. If not checked only new messages will be translated.');?>
                </label>

                <label>
                    <input <?php if (isset($chat->chat_variables_array['lhc_live_trans']) && $chat->chat_variables_array['lhc_live_trans'] === true) : ?>checked="checked"<?php endif; ?> id="live_translations_<?php echo $chat->id?>" type="checkbox" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Automatically translate operator and visitor messages');?>
                </label>

                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','You can always translate old chat messages by clicking right mouse button on the message.');?></p>

                <div class="btn-group form-group" role="group" aria-label="...">
                    <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Save settings')?>" class="translate-button-<?php echo $chat->id?> btn btn-secondary<?php if ($chat->chat_locale != '' && $chat->chat_locale_to != '') :?> btn-success<?php endif;?>" data-loading-text="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translating')?>..." onclick="return lhc.methodCall('lhc.translation','startTranslation',{'btn':$(this),'chat_id':'<?php echo $chat->id?>'})" />
                </div>

            </div>
        <?php endif;?>
    <?php endif;?>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>