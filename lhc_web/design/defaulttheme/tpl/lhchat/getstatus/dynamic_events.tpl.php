<?php foreach ($dynamic_invitation as $invitation) : ?>

<?php if (in_array($invitation->id, $dynamic_processed)) {
    continue; // Skip if particular invitation was already shown
} ?>

<?php if ($invitation->event_type == 1) : ?>
lh_inst.dynamicAssigned.push(<?php echo $invitation->id?>);      
lh_inst.outWindowCallback = function(e) {
    <?php /* Show only if mouse was never triggered before, widget is not open, and chat was not started during current session. */ ?>
    if (lh_inst.timeoutStatusWidgetOpen == 0 && lh_inst.chat_started == false) {
        e = e ? e : window.event;
        var from = e.relatedTarget || e.toElement;
        if (!from || from.nodeName == "HTML") {                
            lh_inst.stopCheckNewMessage();
            lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?>/(inv)/<?php echo $invitation->id?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
            lh_inst.removeEvent(document,"mouseout",lh_inst.outWindowCallback);
        }
    }
};    
lh_inst.addEvent(document, "mouseout", lh_inst.outWindowCallback);
<?php elseif ($invitation->event_type == 2) : ?>
lh_inst.dynamicAssigned.push(<?php echo $invitation->id?>);

lh_inst.iddleTimeoutActivity = null;

lh_inst.resetTimeoutIddle = function() {
    lh_inst.iddleEventResetActivity();
};    

lh_inst.iddleEventResetActivity = function() {
    clearTimeout(this.iddleTimeoutActivity);
    var _that = this;
    this.iddleTimeoutActivity = setTimeout(function(){
        
        clearTimeout(_that.iddleTimeoutActivity); 
        
        lh_inst.stopCheckNewMessage();
        lh_inst.showStartWindow('<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/readoperatormessage')?>/(inv)/<?php echo $invitation->id?><?php $department !== false ? print '/(department)/'.$department : '' ?><?php $theme !== false ? print '/(theme)/'.$theme : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : ''?><?php $survey !== false ? print '/(survey)/'.$survey : ''?>/(vid)/<?php echo $vid;?><?php $visitor->invitation_assigned == true ? print '/(playsound)/true' : ''?>/(fullheight)/<?= $fullheight ? 'true' : 'false' ?>',true);
 
        ['mousemove','mousedown','click','scroll','keypress','load'].forEach(function(element) {
            lh_inst.removeEvent(window,element,lh_inst.resetTimeoutIddle);   
        });
        
        ['mousemove','scroll','touchstart','touchend'].forEach(function(element) {
            lh_inst.removeEvent(document,element,lh_inst.resetTimeoutIddle);   
        });                         
        
    }, <?php echo $invitation->iddle_for *1000?>);        
};

lh_inst.iddleEventResetActivity();

['mousemove','mousedown','click','scroll','keypress','load'].forEach(function(element) {
    lh_inst.addEvent(window,element,lh_inst.resetTimeoutIddle);   
});

['mousemove','scroll','touchstart','touchend'].forEach(function(element) {
    lh_inst.addEvent(document,element,lh_inst.resetTimeoutIddle);   
});
<?php endif; ?>

<?php endforeach; ?>