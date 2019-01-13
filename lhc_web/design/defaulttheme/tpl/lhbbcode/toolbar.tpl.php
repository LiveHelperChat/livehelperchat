<div class="btn-toolbar pb-2">
    <div class="btn-group btn-group-sm mr-2" role="group">
        <button type="button" class="btn btn-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="window.lhcSelector = $(this).attr('data-selector'); lhc.revealModal({'hidecallback' : function(){$('.embed-into').removeClass('embed-into');},'showcallback' : function(){ $(window.lhcSelector).addClass('embed-into');},'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Insert image or file')?>','iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT +'file/attatchfileimg'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Insert image or file')?>"><i class="material-icons mr-0">attach_file</i></button>
        <button type="button" class="btn btn-secondary" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="window.lhcSelector = $(this).attr('data-selector'); lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'/chat/bbcodeinsert/0/(mode)/editor'})">
            <i class="material-icons mr-0">face</i>
        </button>
        <button type="button" class="btn btn-info" data-selector="<?php echo $bbcodeOptions['selector']?>" onclick="return lhc.revealModal({'loadmethod':'post', 'datapost':{'msg':$($(this).attr('data-selector')).val()}, 'url':WWW_DIR_JAVASCRIPT +'chat/previewmessage'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Preview')?>"><i class="material-icons mr-0">visibility</i></button>
    </div>
</div>