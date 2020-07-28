<?php foreach (erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $chat->id]]) as $message) : ?>
<h5><?php echo htmlspecialchars($message->subject)?></h5>

<ul>
    <li><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From');?></b> <?php echo htmlspecialchars($message->from_name)?> &lt;<?php echo htmlspecialchars($message->from_address)?>&gt; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','at');?> <?php echo htmlspecialchars($message->udate_front)?></li>
    <li><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To');?></b> <?php echo htmlspecialchars($message->to_data_front)?></li>
</ul>

<hr>

<?php echo $message->body_front ?>

<hr>
<?php endforeach; ?>