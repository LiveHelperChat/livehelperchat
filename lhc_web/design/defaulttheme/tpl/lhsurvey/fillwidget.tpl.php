<div class="fill-survey-container px-2">
    <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/parts/header.tpl.php'));?>
    <div class="fill-survey-form">
        <form action="" method="post">
            <?php if (isset($errors)) : ?>
            		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php endif; ?>
            
            <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fill.tpl.php'));?>

            <div class="row mt-2">
                <div class="col">
                <?php if ($survey_item->is_filled == true && ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW, erLhcoreClassModelChat::STATUS_SUB_SURVEY_COLLECTED)))) : ?>
                    <input type="button" class="btn btn-sm w-100 btn-primary mb-1" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close')?>" onclick="lhinst.userclosedchatembed();" />
                <?php endif;?>

                <?php if ($survey_item->is_filled == false) : ?>
                    <input type="submit" class="w-100 btn btn-primary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send feedback')?>" name="Vote" />
                <?php endif;?>
                </div>

                <div class="col text-end">
                    <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_txt_dwnld')->current_value == 0 && !(isset($survey->configuration_array['disable_chat_download']) && $survey->configuration_array['disable_chat_download'] == true)) : ?>
                        <a href="<?php echo erLhcoreClassDesign::baseurl('chat/downloadtxt')?>/<?php echo $chat->id,"/",$chat->hash?>" class="btn text-muted btn-link btn-sm" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/chatpreview/<?php echo $chat->id?>/<?php echo $chat->hash?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Download as txt')?>"><i class="material-icons">cloud_download</i></a>
                    <?php endif; ?>

                    <?php if (!(isset($survey->configuration_array['disable_chat_preview']) && $survey->configuration_array['disable_chat_preview'] == true)) : ?>
                    <button type="button" class="btn text-muted btn-link btn-sm" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/chatpreview/<?php echo $chat->id?>/<?php echo $chat->hash?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Preview chat')?>"><i class="material-icons">preview</i></button>
                    <?php endif; ?>
                </div>

                <div class="col">
                <?php
                /**
                 * Because user filled a survey we have to redirect it back to chat
                 * */
                if (($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT || (isset($survey->configuration_array['return_on_close']) && $survey->configuration_array['return_on_close'] == true)) && in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW, erLhcoreClassModelChat::STATUS_SUB_SURVEY_COLLECTED))) : ?>
                    <input type="button" class="btn btn-sm btn-success mb-1 float-end" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Back to chat')?>" onclick="return lhinst.continueChatFromSurvey('<?php echo $survey->id?>');" />
                <?php endif;?>
                </div>
            </div>

        </form>
    </div>
</div>
<script type="text/javascript">
<?php if ($survey_item->is_filled == false) : ?>
setInterval(function(){
    $.getJSON(WWW_DIR_JAVASCRIPT + 'survey/isfilled/<?php echo $chat->id?>/<?php echo $chat->hash?>/<?php echo $survey->id?>', function(data) {
        if (data === true) {
           document.location.reload();
        }
    });
},2000);
<?php endif; ?>

<?php if ($survey_item->is_filled == true && $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_COLLECTED && isset($just_stored) && $just_stored == true) : ?>
setTimeout(function() {
        <?php if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
	        lhinst.continueChatFromSurvey('<?php echo $survey->id?>');
        <?php endif; ?>
}, 3000);
<?php elseif ($survey_item->is_filled == true && $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_COLLECTED && !isset($just_stored)) : ?>
$( document ).ready(function() {
	lhinst.userclosedchatembed();
});
<?php endif;?>

lhinst.setChatID('<?php echo $chat->id?>');
lhinst.setChatHash('<?php echo $chat->hash?>');
</script>