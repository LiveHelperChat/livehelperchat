<?php if (isset($reopen_chat)) : ?>
lh_inst.stopCheckNewMessage();
lh_inst.addCookieAttribute('hash','<?php echo $reopen_chat->id;?>_<?php echo $reopen_chat->hash?>');
lh_inst.showStartWindow();
<?php elseif ($visitor->has_message_from_operator == true && (!isset($dynamic_everytime) || $dynamic_everytime == false)) : ?>
lh_inst.stopCheckNewMessage();

<?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && $visitor->invitation->show_on_mobile == 1) : ?>

    <?php if (($visitor->invitation_assigned == false && $visitor->invitation->delay > 0) || $visitor->invitation->delay_init > 0) : ?>
    setTimeout(function() {
    <?php endif; ?>

        var invitationURL =  '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $tag !== false ? print '/(tag)/' . $tag : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>';

        <?php if (isset($visitor->invitation->design_data_array['mobile_html']) && $visitor->invitation->design_data_array['mobile_html'] != '') : ?>

            <?php if (isset($visitor->invitation->design_data_array['mobile_style']) && $visitor->invitation->design_data_array['mobile_style'] != '') : ?>
                <?php
                    $replaceStyleArray = array();
                    for ($i = 1; $i < 5; $i++) {
                        $replaceStyleArray['{proactive_img_' . $i . '}'] =  erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . $visitor->invitation->{'design_data_img_' . $i . '_url'};
                    }
                ?>
                <?php
                    $contentCSS = str_replace(array_keys($replaceStyleArray),array_values($replaceStyleArray),$visitor->invitation->design_data_array['mobile_style']);
                    $contentCSS = str_replace(array("\n","\r"),'',$contentCSS);
                ?>
                lh_inst.addCss(<?php echo json_encode($contentCSS)?>);
            <?php endif; ?>
            lh_inst.invitationURL = invitationURL;
            var fragmentInv = lh_inst.appendHTML(<?php echo json_encode(str_replace(array("\n","\r",'{readmessage}','{hideInvitation}'),array('','',"return lh_inst.showHTMLInvitation(lh_inst.invitationURL)","return lh_inst.hideHTMLInvitation()"),$visitor->invitation->design_data_array['mobile_html']))?>);
            document.body.insertBefore(fragmentInv, document.body.childNodes[0]);
            lh_inst.isProactivePending = 1;
            lh_inst.toggleStatusWidget(true);

        <?php else : ?>
            <?php if (isset($visitor->invitation->design_data_array['api_do_not_show']) && $visitor->invitation->design_data_array['api_do_not_show'] == 1) : ?>
                lh_inst.showBasicInvitation(invitationURL);
            <?php else : ?>
            if (window.innerWidth > 700) {
                lh_inst.isProactivePending = 1;
                lh_inst.showStartWindow(invitationURL,true);
            } else {
                lh_inst.showBasicInvitation(invitationURL);
            }
            <?php endif; ?>
        <?php endif; ?>

    <?php if (($visitor->invitation_assigned == false && $visitor->invitation->delay > 0) || $visitor->invitation->delay_init > 0) : ?>
    },<?php echo ($visitor->invitation_assigned == true ? $visitor->invitation->delay_init : $visitor->invitation->delay) * 1000?>);
    <?php endif; ?>

<?php else : ?>
            <?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && (($visitor->invitation_assigned == false && $visitor->invitation->delay > 0) || $visitor->invitation->delay_init > 0)) : ?>
                setTimeout(function() {
            <?php endif; ?>
            var urlInvitation = '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $tag !== false ? print '/(tag)/' . $tag : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>';
            <?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && isset($visitor->invitation->design_data_array['api_do_not_show']) && $visitor->invitation->design_data_array['api_do_not_show'] == 1) : ?>
                lh_inst.showBasicInvitation(urlInvitation);
            <?php else : ?>
                if (window.innerWidth > 700) {
                        lh_inst.isProactivePending = 1;
                        lh_inst.showStartWindow(urlInvitation,true);
                    } else {
                        lh_inst.showBasicInvitation(urlInvitation);
                    }
            <?php endif; ?>
            <?php if ($visitor->invitation instanceof erLhAbstractModelProactiveChatInvitation && ($visitor->invitation_assigned == false && $visitor->invitation->delay > 0 || $visitor->invitation->delay_init > 0)) : ?>
                },<?php echo ($visitor->invitation_assigned == true ? $visitor->invitation->delay_init : $visitor->invitation->delay) * 1000?>);
            <?php endif; ?>
<?php endif; ?>

<?php elseif (isset($dynamic)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/dynamic_events.tpl.php')); ?>	
<?php endif; ?>

<?php if (isset($inject_html)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/inject_events.tpl.php')); ?>
<?php endif; ?>

<?php if (isset($operation)) : ?><?php echo $operation;?><?php endif;?>

<?php if ($visitor->next_reschedule > 0) : ?>
    setTimeout(function() {
        lh_inst.startNewMessageCheckSingle();
    },<?php echo (($visitor->next_reschedule + 1)*1000);?>);
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/chatcheckoperatormessage_multiinclude.tpl.php')); ?>	