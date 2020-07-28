<h1><?php echo htmlspecialchars($chat->subject)?></h1>

<ul>
    <li><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From');?></b> <?php echo htmlspecialchars($chat->from_name)?> &lt;<?php echo htmlspecialchars($chat->from_address)?>&gt; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','at');?> <?php echo htmlspecialchars($chat->udate_front)?></li>
    <li><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To');?></b> <?php echo htmlspecialchars($chat->to_data_front)?></li>
</ul>

<?php echo $chat->body_front ?>

<hr>