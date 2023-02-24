<div class="msg-body w-100 p-2 meta-message meta-message-<?php echo $messageId?>">
    <?php
    $renderFunction = function() use ($chat, $msg, $metaMessage) {

        $survey = erLhAbstractModelSurvey::fetch($metaMessage['survey_id']);
        $surveyItem = erLhAbstractModelSurveyItem::getInstance($chat, $survey);

        $tpl = new erLhcoreClassTemplate( 'lhsurvey/fill_inline.tpl.php');
        $tpl->set('chat',$chat);
        $tpl->set('survey',$survey);
        $tpl->set('survey_item',$surveyItem);

        echo $tpl->fetch();

    };
    $renderFunction();
    ?>
</div>