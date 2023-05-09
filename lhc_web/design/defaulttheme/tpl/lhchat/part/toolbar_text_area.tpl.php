<div class="btn-toolbar pb-2">

    <?php if ((int)erLhcoreClassModelUserSetting::getSetting('column_chats', 0) == 1 || (($detect = new Mobile_Detect()) && ($detect->isMobile() || $detect->isTablet()))) :  ?>
    <div class="dropdown dropup dropdown-menu-main me-1">
        <button id="dropdown-menu-main-action-<?php echo $chat->id?>" class="btn btn-outline-secondary dropdown-toggle btn-sm" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Styling options')?>" type="button"  aria-haspopup="true" aria-expanded="false">
            <i class="material-icons me-0">wysiwyg</i>
        </button>
        <div class="dropdown-menu dropdown-menu-end ps-2 pe-1">
    <?php endif; ?>
            <div class="btn-group btn-group-sm me-1 pb-1" role="group">

                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="b" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Bold')?>"><b>B</b></button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="i" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Italic')?>"><i>I</i></button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="u" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Underline')?>"><u>U</u></button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="s" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Strike')?>"><strike>S</strike></button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="quote" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Quote')?>">&quot;</button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="youtube" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Youtube')?>"><i class="material-icons me-0">ondemand_video</i></button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" data-bbcode="html" onclick="lhinst.handleBBCode($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','HTML Code')?>"><i class="material-icons me-0">code</i></button>

                <div class="dropdown dropup">
                    <button class="btn btn-outline-secondary dropdown-toggle btn-sm rounded-start-0 rounded-0 border-start-0 border-end-0"  title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Font Size')?>" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons me-0">format_size</i>
                    </button>
                    <div class="dropdown-menu">
                        <?php for($i = 0; $i < 7; $i++) : ?>
                            <a class="dropdown-item" href="#" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="lhinst.handleBBCode($(this))" data-bbcode="fs<?php echo 10+$i;?>" data-bbcode-end="fs" style="font-size: <?php echo 10+$i;?>pt">Font Size <?php echo 10+$i;?>pt</a>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="dropdown dropup">
                    <button class="btn btn-outline-secondary dropdown-toggle btn-sm rounded-start-0 rounded-end-1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Color')?>" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons me-0">palette</span>
                    </button>
                    <div class="dropdown-menu keepopen downdown-menu-color-<?php echo $chat->id?>" style="width: 128px;">
                        <div id="color-picker-chat-<?php echo $chat->id?>"></div>
                        <?php $colorItems = array('c00000','cf4c6d','ff0000','ffc000','ffff00','89c748','00b050','48c3c7','00b0f0','0070c0','002060','5c2585'); ?>
                        <div class="row">
                            <div class="col-12 text-center ms-2 pb-0 pe-2">
                                <?php foreach ($colorItems as $colorItem) : ?>
                                    <div class="float-start ms-1 mb-1 color-item" data-color="<?php echo $colorItem?>" style="background-color: #<?php echo $colorItem?>"></div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="pe-2 ps-2">
                            <button class="btn btn-outline-secondary w-100 btn-xs" id="color-apply-<?php echo $chat->id?>" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="lhinst.handleBBCode($(this))" data-bbcode-end="color" data-bbcode="color=00FF00" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Apply')?></button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="btn-group btn-group-sm me-1 pb-1" role="group">
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="window.lhcSelector = $(this).attr('data-selector'); lhc.revealModal({'hidecallback' : function(){$('.embed-into').removeClass('embed-into');},'showcallback' : function(){ $(window.lhcSelector).addClass('embed-into');},'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Insert image or file')?>','iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'file/attatchfileimg'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Insert image or file')?>"><i class="material-icons me-0">insert_link</i></button>
                <?php if (isset($chat) && isset($filesEnabled) && $filesEnabled == true) : ?>
                    <button type="button" class="btn btn-outline-secondary" onclick="$('#fileupload-<?php echo $chat->id?>').click()"><i class="material-icons me-0">attach_file</i></button>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="window.lhcSelector = $(this).attr('data-selector'); lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'/chat/bbcodeinsert/0/(mode)/editor'})">
                    <i class="material-icons me-0">&#xE24E;</i>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="return lhc.revealModal({'loadmethod':'post', 'datapost':{'msg':$($(this).attr('data-selector')).val()}, 'url':WWW_DIR_JAVASCRIPT +'chat/previewmessage'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Preview')?>"><i class="material-icons me-0">visibility</i></button>
            </div>

                <?php if ((int)erLhcoreClassModelUserSetting::getSetting('column_chats', 0) == 1 || (($detect = new Mobile_Detect()) && ($detect->isMobile() || $detect->isTablet()))) :  ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($chat)) : ?>

    <div class="btn-group btn-group-sm<?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?> hide<?php endif;?>  pb-1" id="action-block-row-<?php echo $chat->id?>">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/speech_action.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/voice_action.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/translation_action.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/hold_action.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/custom_toolbar_buttons_multiinclude_tab.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/send_message_button.tpl.php')); ?>
    </div>

    <?php endif;?>

</div>