<?php foreach ($dynamic_invitation as $invitation) : ?>

<?php if ($invitation->event_type == 1) : ?>

if (typeof lh_inst.mouseOutAttatched == "undefined")
{
    lh_inst.mouseOutAttatched = true;
    lh_inst.mouseOutTriggered = false;        
    lh_inst.addEvent(document, "mouseout", function(e) {
        if (lh_inst.mouseOutTriggered == false) {
            e = e ? e : window.event;
            var from = e.relatedTarget || e.toElement;
            if (!from || from.nodeName == "HTML") {
                lh_inst.mouseOutTriggered = true;
                lh_inst.stopCheckNewMessage();
                lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?>/(inv)/<?php echo $invitation->id?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
            }
        }
    });
    
}
<?php endif; ?>

<?php endforeach; ?>