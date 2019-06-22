<?php foreach ($inject_html as $injectInvitation) : ?>

<?php if ($injectInvitation->dynamic_invitation == 1) : ?>
    <?php if ($injectInvitation->event_type == 1) : ?>
        lh_inst.outWindowCallbackInject = function(e) {
            e = e ? e : window.event;
            var from = e.relatedTarget || e.toElement;
            if (!from || from.nodeName == "HTML") {
                lh_inst.sendHTMLSnippet(<?php echo $injectInvitation->id?>,'inv_0');
                <?php if (!isset($injectInvitation->design_data_array['dynamic_everytime']) ||  $injectInvitation->design_data_array['dynamic_everytime'] == 0) : ?>
                lh_inst.removeEvent(document,"mouseout",lh_inst.outWindowCallbackInject);
                <?php endif; ?>
            }
        };
        lh_inst.addEvent(document, "mouseout", lh_inst.outWindowCallbackInject);
    <?php elseif ($injectInvitation->event_type == 2) : ?>

            lh_inst.iddleTimeoutActivityInject = null;

            lh_inst.resetTimeoutIddleInject = function() {
            lh_inst.iddleEventResetActivityInject();
            };

            lh_inst.iddleEventResetActivityInject = function() {
            clearTimeout(this.iddleTimeoutActivityInject);
            var _that = this;
            this.iddleTimeoutActivityInject = setTimeout(function(){

            clearTimeout(_that.iddleTimeoutActivityInject);

            lh_inst.sendHTMLSnippet(<?php echo $injectInvitation->id?>,'inv_0');

            <?php if (!isset($injectInvitation->design_data_array['dynamic_everytime']) ||  $injectInvitation->design_data_array['dynamic_everytime'] == 0) : ?>
            ['mousemove','mousedown','click','scroll','keypress','load'].forEach(function(element) {
            lh_inst.removeEvent(window,element,lh_inst.resetTimeoutIddleInject);
            });

            ['mousemove','scroll','touchstart','touchend'].forEach(function(element) {
            lh_inst.removeEvent(document,element,lh_inst.resetTimeoutIddleInject);
            });
            <?php endif; ?>




            }, <?php echo $injectInvitation->iddle_for *1000?>);
            };

            lh_inst.iddleEventResetActivityInject();

            ['mousemove','mousedown','click','scroll','keypress','load'].forEach(function(element) {
            lh_inst.addEvent(window,element,lh_inst.resetTimeoutIddleInject);
            });

            ['mousemove','scroll','touchstart','touchend'].forEach(function(element) {
            lh_inst.addEvent(document,element,lh_inst.resetTimeoutIddleInject);
            });
    <?php endif; ?>

<?php else : ?>
    lh_inst.sendHTMLSnippet(<?php echo $injectInvitation->id?>,'inv_0');
<?php endif;?>
<?php endforeach; ?>
