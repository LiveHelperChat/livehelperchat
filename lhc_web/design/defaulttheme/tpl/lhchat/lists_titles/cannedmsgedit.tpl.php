<span class="float-end text-muted">
    <small>
        <?php if ($canned_message->created_at > 0) : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Created at');?>: <?php echo htmlspecialchars($canned_message->created_at_front);?><?php endif; ?><?php if ($canned_message->updated_at > 0) : ?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Updated at');?>: <?php echo htmlspecialchars($canned_message->updated_at_front);?>
        <?php endif; ?>
    </small>
</span>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit canned message');?></h1>