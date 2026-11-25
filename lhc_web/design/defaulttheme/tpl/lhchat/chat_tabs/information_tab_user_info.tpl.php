<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information/information_top.tpl.php'));?>

<div>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/actions_order.tpl.php'));?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/actions_order_extension_multiinclude.tpl.php'));?>

    <?php foreach ($orderChatButtons as $buttonData) : ?>
        <?php if ($buttonData['enabled'] == true) : ?>
            <?php if ($buttonData['item'] == 'print_archive') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/print_archive.tpl.php'));?>
            <?php elseif ($buttonData['item'] == 'mail_archive') : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/mail_archive.tpl.php'));?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/chat_actions_extension_multiinclude.tpl.php'));?>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/information_order.tpl.php'));?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/information_order_extension_multiinclude.tpl.php'));?>

<table class="table table-sm table-borderless">
<?php foreach ($orderInformation as $buttonData) : ?>
    <?php if ($buttonData['enabled'] == true) : ?>
        <?php if ($buttonData['item'] == 'chat') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/above_department_extension_multiinclude.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/chat.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'mail') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/mail.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'product') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/product.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'uagent') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/uagent.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'voice_call') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/voice_call.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'subject') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/subject.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'phone') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/after_phone_extension_multiinclude.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'additional_data') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/additional_data.tpl.php'));?>
         <?php elseif ($buttonData['item'] == 'chat_duration') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/chat_duration.tpl.php'));?>
        <?php elseif ($buttonData['item'] == 'chat_owner') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/chat_owner.tpl.php'));?>
        <?php else : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/extension_information_row_multiinclude.tpl.php'));?>
        <?php endif;?>
    <?php endif; ?>
<?php endforeach; ?>
</table>
