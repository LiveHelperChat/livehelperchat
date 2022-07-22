<div class="reactions-holder reactions-holder-admin <?php if (isset($metaMessageData['content']['reactions']['current'])) : ?>reactions-selected<?php endif;?>" id="reaction-message-<?php echo $msg['id']?>">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/reaction_to_visitor_body.tpl.php'));?>
</div>