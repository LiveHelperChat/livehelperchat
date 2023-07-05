<?php if (is_array($metaMessage)) : ?>
    <?php

    $msgBody = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser', 'Inline survey') . ' - ';

        $surveyItem = erLhAbstractModelSurvey::fetch($metaMessage['survey_id']);
        if (is_object($surveyItem)) {
            $msgBody .= $surveyItem;
        } else {
            $msgBody .= $metaMessage['survey_id'];
        }

    $msgBody = "[html]<span class=\"material-icons\">quiz</span>[/html]" . $msgBody;

    $paramsMessageRender = array('msg_body_class' => 'text-muted bg-light','sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
<?php endif; ?>
