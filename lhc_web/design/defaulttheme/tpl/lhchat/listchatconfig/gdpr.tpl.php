<div role="tabpanel" class="tab-pane" id="gdpr">
    <?php $attribute = 'do_no_track_ip';$boolValue = true;?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    <?php $attribute = 'encrypt_msg_after';?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    <?php $attribute = 'encrypt_msg_op';$boolValue = true;?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

</div>