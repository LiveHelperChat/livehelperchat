<h1 class="attr-header">Analytics</h1>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="ga" <?php isset($ga_options['ga_enabled']) && ($ga_options['ga_enabled'] == true) ? print 'checked="checked"' : ''?> /> Enable Google Analytics</label><br/>
    </div>

    <p>
        In order for Google Analytics to work properly your site already should have GA embedded and `ga` function should be available. References
        <ul>
            <li><a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/events">https://developers.google.com/analytics/devguides/collection/analyticsjs/events</a> </li>
            <li><a href="https://support.google.com/analytics/answer/1033068#Anatomy">https://support.google.com/analytics/answer/1033068#Anatomy</a></li>
        </ul>
    </p>

    <div class="form-group">
        <label>Javascript to execute on event. This is Google Analytics version of the script. You can write custom one of you want.</label>
        <textarea rows="5" ng-non-bindable name="ga_js" class="form-control form-control-sm"><?php isset($ga_options['ga_js']) ? print htmlspecialchars($ga_options['ga_js']) : print "ga('send', 'event', { 'eventCategory': {{eventCategory}},'eventAction': {{eventAction}},'eventLabel': {{eventLabel}} })"?></textarea>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li role="presentation" class="nav-item"><a href="#widget" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="widget" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Widget');?></a></li>
        <li role="presentation" class="nav-item"><a href="#chat" class="nav-link<?php if ($tab == 'chat') : ?> active<?php endif;?>" aria-controls="chat" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chat');?></a></li>
        <li role="presentation" class="nav-item"><a href="#invitation" class="nav-link<?php if ($tab == 'invitation') : ?> active<?php endif;?>" aria-controls="invitation" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Invitation');?></a></li>
        <li role="presentation" class="nav-item"><a href="#needhelp" class="nav-link<?php if ($tab == 'needhelp') : ?> active<?php endif;?>" aria-controls="needhelp" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Need help');?></a></li>
        <li role="presentation" class="nav-item"><a href="#bot" class="nav-link<?php if ($tab == 'bot') : ?> active<?php endif;?>" aria-controls="bot" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Bot');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="widget">

            <label title="Track this event [showWidget]" class="font-weight-bold"><input type="checkbox" value="on" name="showWidget_on"> Widget was shown</label>

            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="showWidget_category" value="<?php isset($ga_options['showWidget_category']) ? print htmlspecialchars($ga_options['showWidget_category']) : print 'Widget'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="showWidget_action" value="<?php isset($ga_options['showWidget_action']) ? print htmlspecialchars($ga_options['showWidget_action']) : print 'Show'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="showWidget_label" value="<?php isset($ga_options['showWidget_label']) ? print htmlspecialchars($ga_options['showWidget_label']) : ''?>" />
                    </div>
                </div>
            </div>

            <label title="Track this event [closeWidget]" class="font-weight-bold"><input type="checkbox" value="on" name="closeWidget_on"> Widget was closed/minimized</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="closeWidget_category" value="<?php isset($ga_options['closeWidget_category']) ? print htmlspecialchars($ga_options['closeWidget_category']) : print 'Widget'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="closeWidget_action" value="<?php isset($ga_options['closeWidget_action']) ? print htmlspecialchars($ga_options['closeWidget_action']) : print 'Close'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="closeWidget_label" value="<?php isset($ga_options['closeWidget_label']) ? print htmlspecialchars($ga_options['closeWidget_label']) : ''?>" />
                    </div>
                </div>
            </div>

            <label title="Track this event [openPopup]" class="font-weight-bold"><input type="checkbox" value="on" name="openPopup_on"> Popup opened</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="openPopup_category" value="<?php isset($ga_options['openPopup_category']) ? print htmlspecialchars($ga_options['openPopup_category']) : print 'Widget'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="openPopup_action" value="<?php isset($ga_options['openPopup_action']) ? print htmlspecialchars($ga_options['openPopup_action']) : print 'Popup'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="openPopup_label" value="<?php isset($ga_options['openPopup_label']) ? print htmlspecialchars($ga_options['openPopup_label']) : ''?>" />
                    </div>
                </div>
            </div>


        </div>
        <div role="tabpanel" class="tab-pane <?php if ($tab == 'chat') : ?>active<?php endif;?>" id="chat">

            <label title="Track this event [endChat]" class="font-weight-bold"><input type="checkbox" value="on" name="endChat_on"> Chat ended</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="endChat_category" value="<?php isset($ga_options['endChat_category']) ? print htmlspecialchars($ga_options['endChat_category']) : print 'Chat'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="endChat_action" value="<?php isset($ga_options['endChat_action']) ? print htmlspecialchars($ga_options['endChat_action']) : print 'Ended'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="endChat_label" value="<?php isset($ga_options['endChat_label']) ? print htmlspecialchars($ga_options['endChat_label']) : ''?>" />
                    </div>
                </div>
            </div>

            <label title="Track this event [chatStarted]" class="font-weight-bold"><input type="checkbox" value="on" name="chatStarted_on"> Chat started</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="chatStarted_category" value="<?php isset($ga_options['chatStarted_category']) ? print htmlspecialchars($ga_options['chatStarted_category']) : print 'Chat'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="chatStarted_action" value="<?php isset($ga_options['chatStarted_action']) ? print htmlspecialchars($ga_options['chatStarted_action']) : print 'Started'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="chatStarted_label" value="<?php isset($ga_options['chatStarted_label']) ? print htmlspecialchars($ga_options['chatStarted_label']) : ''?>" />
                    </div>
                </div>
            </div>

            <label title="Track this event [offlineMessage]" class="font-weight-bold"><input type="checkbox" value="on" name="offlineMessage_on">Offline message</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="offlineMessage_category" value="<?php isset($ga_options['offlineMessage_category']) ? print htmlspecialchars($ga_options['offlineMessage_category']) : print 'Chat'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="offlineMessage_action" value="<?php isset($ga_options['offlineMessage_action']) ? print htmlspecialchars($ga_options['offlineMessage_action']) : print 'Offline message'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="offlineMessage_label" value="<?php isset($ga_options['offlineMessage_label']) ? print htmlspecialchars($ga_options['offlineMessage_label']) : ''?>" />
                    </div>
                </div>
            </div>

        </div>
        <div role="tabpanel" class="tab-pane <?php if ($tab == 'invitation') : ?>active<?php endif;?>" id="invitation">

            <label title="Track this event [showInvitation]" class="font-weight-bold"><input type="checkbox" value="on" name="showInvitation_on">Invitation shown</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="showInvitation_category" value="<?php isset($ga_options['showInvitation_category']) ? print htmlspecialchars($ga_options['showInvitation_category']) : print 'Invitation'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="showInvitation_action" value="<?php isset($ga_options['showInvitation_action']) ? print htmlspecialchars($ga_options['showInvitation_action']) : print 'Show'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="showInvitation_label" value="<?php isset($ga_options['showInvitation_label']) ? print htmlspecialchars($ga_options['showInvitation_label']) : ''?>" />
                        <p>If you leave empty we will set automatically invitation name.</p>
                    </div>
                </div>
            </div>

            <label title="Track this event [hideInvitation]" class="font-weight-bold"><input type="checkbox" value="on" name="hideInvitation_on">Invitation hide</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="hideInvitation_category" value="<?php isset($ga_options['hideInvitation_category']) ? print htmlspecialchars($ga_options['hideInvitation_category']) : print 'Invitation'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="hideInvitation_action" value="<?php isset($ga_options['hideInvitation_action']) ? print htmlspecialchars($ga_options['hideInvitation_action']) : print 'Hide'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="hideInvitation_label" value="<?php isset($ga_options['hideInvitation_label']) ? print htmlspecialchars($ga_options['hideInvitation_label']) : ''?>" />
                        <p>If you leave empty we will set automatically invitation name.</p>
                    </div>
                </div>
            </div>

            <label title="Track this event [cancelInvitation]" class="font-weight-bold"><input type="checkbox" value="on" name="cancelInvitation_on">Invitation cancel</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="cancelInvitation_category" value="<?php isset($ga_options['cancelInvitation_category']) ? print htmlspecialchars($ga_options['cancelInvitation_category']) : print 'Invitation'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="cancelInvitation_action" value="<?php isset($ga_options['cancelInvitation_action']) ? print htmlspecialchars($ga_options['cancelInvitation_action']) : print 'Cancel'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="cancelInvitation_label" value="<?php isset($ga_options['cancelInvitation_label']) ? print htmlspecialchars($ga_options['cancelInvitation_label']) : ''?>" />
                        <p>If you leave empty we will set automatically invitation name.</p>
                    </div>
                </div>
            </div>

            <label title="Track this event [fullInvitation]" class="font-weight-bold"><input type="checkbox" value="on" name="fullInvitation_on">Invitation clicked. Visitor clicked invitation tooltip. It's not triggered if it just clicks status icon while invitation tooltip is shown.</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="fullInvitation_category" value="<?php isset($ga_options['fullInvitation_category']) ? print htmlspecialchars($ga_options['fullInvitation_category']) : print 'Invitation'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="fullInvitation_action" value="<?php isset($ga_options['fullInvitation_action']) ? print htmlspecialchars($ga_options['fullInvitation_action']) : print 'Clicked'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="fullInvitation_label" value="<?php isset($ga_options['fullInvitation_label']) ? print htmlspecialchars($ga_options['fullInvitation_label']) : ''?>" />
                        <p>If you leave empty we will set automatically invitation name.</p>
                    </div>
                </div>
            </div>

            <label title="Track this event [readInvitation]" class="font-weight-bold"><input type="checkbox" value="on" name="readInvitation_on">Invitation was read. Means visitor opened widget with an invitation either by clicking invitation tooltip or status icon.</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="readInvitation_category" value="<?php isset($ga_options['readInvitation_category']) ? print htmlspecialchars($ga_options['readInvitation_category']) : print 'Invitation'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="readInvitation_action" value="<?php isset($ga_options['readInvitation_action']) ? print htmlspecialchars($ga_options['readInvitation_action']) : print 'Read'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="readInvitation_label" value="<?php isset($ga_options['readInvitation_label']) ? print htmlspecialchars($ga_options['readInvitation_label']) : ''?>" />
                        <p>If you leave empty we will set automatically invitation name.</p>
                    </div>
                </div>
            </div>


        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'needhelp') : ?>active<?php endif;?>" id="needhelp">

            <label title="Track this event [nhShow]" class="font-weight-bold"><input type="checkbox" value="on" name="nhShow_on">Need help show</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhShow_category" value="<?php isset($ga_options['nhShow_category']) ? print htmlspecialchars($ga_options['nhShow_category']) : print 'Need help'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhShow_action" value="<?php isset($ga_options['nnhShow_action']) ? print htmlspecialchars($ga_options['nhShow_action']) : print 'Show'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="nhShow_label" value="<?php isset($ga_options['nhShow_label']) ? print htmlspecialchars($ga_options['nhShow_label']) : ''?>" />
                    </div>
                </div>
            </div>

            nhClicked
            // Continue here
            <label title="Track this event [nhHide]" class="font-weight-bold"><input type="checkbox" value="on" name="nhHide_on">Need help hide</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhHide_category" value="<?php isset($ga_options['nhHide_category']) ? print htmlspecialchars($ga_options['nhHide_category']) : print 'Need help'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhHide_action" value="<?php isset($ga_options['nhHide_action']) ? print htmlspecialchars($ga_options['nhHide_action']) : print 'Show'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="nhHide_label" value="<?php isset($ga_options['nhHide_label']) ? print htmlspecialchars($ga_options['nhHide_label']) : ''?>" />
                    </div>
                </div>
            </div>

            <label title="Track this event [nhClicked]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['nhClicked_on']) && $ga_options['nhClicked_on'] == 1) : ?>checked="checked"<?php endif;?> name="nhClicked_on">Need help clicked</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhClicked_category" value="<?php isset($ga_options['nhClicked_category']) ? print htmlspecialchars($ga_options['nhClicked_category']) : print 'Need help'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhClicked_action" value="<?php isset($ga_options['nhClicked_action']) ? print htmlspecialchars($ga_options['nhClicked_action']) : print 'Clicked'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="nhClicked_label" value="<?php isset($ga_options['nhClicked_label']) ? print htmlspecialchars($ga_options['nhClicked_label']) : ''?>" />
                    </div>
                </div>
            </div>

            <label title="Track this event [nhClosed]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['nhClosed_on']) && $ga_options['nhClosed_on'] == 1) : ?>checked="checked"<?php endif;?> name="nhClosed_on">Need help closed</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhClosed_category" value="<?php isset($ga_options['nhClosed_category']) ? print htmlspecialchars($ga_options['nhClosed_category']) : print 'Need help'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="nhClosed_action" value="<?php isset($ga_options['nhClosed_action']) ? print htmlspecialchars($ga_options['nhClosed_action']) : print 'Closed'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control form-control-sm" name="nhClosed_label" value="<?php isset($ga_options['nhClosed_label']) ? print htmlspecialchars($ga_options['nhClosed_label']) : ''?>" />
                    </div>
                </div>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'bot') : ?>active<?php endif;?>" id="bot">
            <label title="Track this event [botTrigger]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['botTrigger_on']) && $ga_options['botTrigger_on'] == 1) : ?>checked="checked"<?php endif;?> name="botTrigger_on">Trigger execute</label>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control form-control-sm" name="botTrigger_category" value="<?php isset($ga_options['botTrigger_category']) ? print htmlspecialchars($ga_options['botTrigger_category']) : print 'Bot'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control form-control-sm" name="botTrigger_action" value="<?php isset($ga_options['botTrigger_action']) ? print htmlspecialchars($ga_options['botTrigger_action']) : print 'Trigger'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <p>We will set eventLabel to trigger name</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
