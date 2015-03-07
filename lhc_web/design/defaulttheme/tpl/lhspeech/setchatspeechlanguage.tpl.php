<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
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
          <a class="btn btn-default" onclick="return lhc.methodCall('lhc.speak','setChatLanguageRecognition',{'chat_id':'<?php echo $chat->id?>','lhinst':lhinst})"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save')?></a>
    </div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>