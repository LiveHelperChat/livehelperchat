<?php foreach (erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $chat->id]]) as $message) :

    if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
        $message->setSensitive(true);

        if ($message->response_type !== erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
            $message->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($message->from_address);
        }
    }

    ?>
    <h5><?php echo htmlspecialchars($message->subject)?></h5>

    <ul>
        <li><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvprint','From');?></b> <?php echo htmlspecialchars($message->from_name)?> &lt;<?php echo htmlspecialchars($message->from_address)?>&gt; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvprint','at');?> <?php echo htmlspecialchars($message->udate_front)?></li>
        <li><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvprint','To');?></b> <?php echo htmlspecialchars($message->to_data_front)?></li>
    </ul>

    <hr>

    <?php echo $message->body_front ?>

    <hr>
<?php endforeach; ?>