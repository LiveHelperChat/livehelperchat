<div>
    <div class="row">
        <div class="columns col-md-12"> 
            <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('speech/language','Choose speech recognition language for this chat')?></h4>
        </div>
        <div class="columns col-md-12"> 
            <?php $dataSpeech = array(
                'language' => $chat_speech->language_id,
                'dialect' => $chat_speech->dialect              
            );
            ?>
            <?php include(erLhcoreClassDesign::designtpl('lhspeech/speech_form_fields.tpl.php'));?>
        </div>      
        <div class="columns col-md-12"> 
              <a class="btn btn-default" onclick="lhinst.setChatLanguageRecognition('<?php echo $chat->id?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?></a>
        </div>
    </div>  
</div>

<a class="close-reveal-modal">&#215;</a>