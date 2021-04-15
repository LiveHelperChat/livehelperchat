<?php $gaOptions = erLhcoreClassModelChatConfig::fetch('ga_options')->data_value; ?>

    <?php if (isset($gaOptions['ga_enabled']) && $gaOptions['ga_enabled'] == true) :

    $continueTrack = false;

    if (isset($gaOptions['js_static']) && !empty($gaOptions['js_static']) && ((isset($gaOptions['ga_all']) && $gaOptions['ga_all'] == true) || (isset($gaOptions['ga_dep']) && isset($department) && is_array($department) && count(array_intersect($department, $gaOptions['ga_dep'])) > 0))) {
        $continueTrack = true;
    }

    if (isset($dep_id) && $dep_id > 0) {
        $gaByDep = erLhcoreClassModelChatEventTrack::findOne(array('filter' => array('department_id' => $dep_id)));
        if ($gaByDep instanceof erLhcoreClassModelChatEventTrack) {
            $gaOptions = $gaByDep->data_array;
            $continueTrack = true;
        }
    }

    if ($continueTrack == true) : ?>
    window.LHCEventTracker = function(key,params) {
    if (typeof params !== 'undefined' && typeof params[0] !== 'undefined') {
        params = params[0];
    }
    try {
        <?php
            $optionEvents = array(
                'showWidget',
                'closeWidget',
                'openPopup',
                'endChat',
                'chatStarted',
                'offlineMessage',
                'showInvitation',
                'hideInvitation',
                'nhClicked',
                'nhClosed',
                'nhShow',
                'nhHide',
                'fullInvitation',
                'cancelInvitation',
                'readInvitation',
                'clickAction',
                'botTrigger',
            );
            $events = ['events' => [],'js' => $gaOptions['ga_js']];
            foreach ($optionEvents as $optionEvent) {
                if (isset($gaOptions[$optionEvent .'_on']) && $gaOptions[$optionEvent .'_on'] == 1) {
                    $events['events'][$optionEvent] = array(
                        'ev' => $optionEvent,
                        'ec' => $gaOptions[$optionEvent .'_category'],
                        'ea' => $gaOptions[$optionEvent .'_action'],
                        'el' => (isset($gaOptions[$optionEvent .'_label']) ? $gaOptions[$optionEvent .'_label'] : ''),
                    );
                }
            }
        ?>
        var eventsTracked = <?php echo json_encode($events);?>;
        if (typeof eventsTracked['events'][key] !== 'undefined') {
            var item = eventsTracked['events'][key];

            if (item.ev == 'hideInvitation' && typeof params !== 'undefined' && params.full) {
                return ;
            }

            var label = item.el;

            // Set invitation name
            if ((item.ev == 'showInvitation' || item.ev == 'readInvitation' || item.ev == 'fullInvitation' || item.ev == 'cancelInvitation') && typeof params !== 'undefined' && params.name) {
                label = label || params.name;
            } else if (item.ev == 'botTrigger') {
                if (typeof params !== 'undefined' && params.trigger && params.trigger.length > 0) {
                    for(var i=0;i<params.trigger.length;i++) {
                        var triggerLabel = params.trigger[i];
                        var js = eventsTracked['js'].replace(
                            /\{\{eventCategory\}\}/g,JSON.stringify(item.ec)
                        ).replace(
                            /\{\{eventAction\}\}/g,JSON.stringify(item.ea)
                        ).replace(
                            /\{\{eventLabel\}\}/g,JSON.stringify(triggerLabel)
                        ).replace(
                            /\{\{eventInternal\}\}/g,JSON.stringify(item.ev)
                        );
                        try {
                            eval(js);
                        } catch (err) {
                            console.log(err);
                        }
                    }
                    return ;
                } else {
                    return;
                }
            }
            var js = eventsTracked['js'].replace(
                /\{\{eventCategory\}\}/g,JSON.stringify(item.ec)
            ).replace(
                /\{\{eventAction\}\}/g,JSON.stringify(item.ea)
            ).replace(
                /\{\{eventLabel\}\}/g,JSON.stringify(label)
            ).replace(
                /\{\{eventInternal\}\}/g,JSON.stringify(item.ev)
            );
            try {
                eval(js);
            } catch (err) {
                console.log(err);
            }
        }
    } catch (e) {
        console.log(e);
    }
};
<?php endif; endif; ?>

