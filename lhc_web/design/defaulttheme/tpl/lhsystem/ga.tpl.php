<h1 class="attr-header">Google Analytics</h1>

<form action="" method="post">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <p>
        In order for Google Analytics to work properly your site already should have GA embedded and `ga` function should be available. https://developers.google.com/analytics/devguides/collection/analyticsjs/events
    </p>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="ga" <?php isset($ga_options['ga_enabled']) && ($ga_options['ga_enabled'] == true) ? print 'checked="checked"' : ''?> /> Enable Google Analytics</label><br/>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li role="presentation" class="nav-item"><a href="#widget" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="widget" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Widget');?></a></li>
        <li role="presentation" class="nav-item"><a href="#chat" class="nav-link<?php if ($tab == 'chat') : ?> active<?php endif;?>" aria-controls="chat" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chat');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="widget">

            <h6>Widget was shown</h6>

            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control" name="showWidget_category" value="<?php isset($mb_options['showWidget_category']) ? print $mb_options['showWidget_category'] : print 'Widget'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control" name="showWidget_action" value="<?php isset($mb_options['showWidget_action']) ? print $mb_options['showWidget_action'] : print 'Show'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control" name="showWidget_label" value="<?php isset($mb_options['showWidget_label']) ? print $mb_options['showWidget_label'] : ''?>" />
                    </div>
                </div>
            </div>

            <h3>Widget was closed/minimized</h3>

            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Category [eventCategory]*</label>
                        <input type="text" class="form-control" name="closeWidget_category" value="<?php isset($mb_options['closeWidget_category']) ? print $mb_options['closeWidget_category'] : print 'Widget'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event action [eventAction]*</label>
                        <input type="text" class="form-control" name="closeWidget_action" value="<?php isset($mb_options['closeWidget_action']) ? print $mb_options['closeWidget_action'] : print 'Close'?>" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Event label [eventLabel]</label>
                        <input type="text" class="form-control" name="closeWidget_label" value="<?php isset($mb_options['closeWidget_label']) ? print $mb_options['closeWidget_label'] : ''?>" />
                    </div>
                </div>
            </div>




        </div>
        <div role="tabpanel" class="tab-pane <?php if ($tab == 'chat') : ?>active<?php endif;?>" id="chat">

        </div>
    </div>


    <div class="form-group">
        <label>Event actions [eventAction]</label>
        <input type="text" class="form-control" name="fcm_key" value="<?php isset($mb_options['event_action']) ? print $mb_options['event_action'] : 'widget'?>" />
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="showWidget" value="<?php isset($mb_options['showWidget']) ? print $mb_options['showWidget'] : 'Show widget'?>" />
        <p>Widget was shown.</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="closeWidget" value="<?php isset($mb_options['closeWidget']) ? print $mb_options['closeWidget'] : 'Close widget'?>" />
        <p>Widget was closed.</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="endChat" value="<?php isset($mb_options['endChat']) ? print $mb_options['endChat'] : 'Chat ended'?>" />
        <p>Chat ended.</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="chatStarted" value="<?php isset($mb_options['chatStarted']) ? print $mb_options['chatStarted'] : 'Chat ended'?>" />
        <p>Chat started.</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="showInvitation" value="<?php isset($mb_options['showInvitation']) ? print $mb_options['showInvitation'] : 'Invitation shown'?>" />
        <p>Invitation shown</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="hideInvitation" value="<?php isset($mb_options['hideInvitation']) ? print $mb_options['hideInvitation'] : 'Invitation hidden'?>" />
        <p>Invitation hidden</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="openPopup" value="<?php isset($mb_options['openPopup']) ? print $mb_options['openPopup'] : 'Popup opened'?>" />
        <p>Popup opened</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="offlineMessage" value="<?php isset($mb_options['offlineMessage']) ? print $mb_options['offlineMessage'] : 'Offline message'?>" />
        <p>Offline message was left</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="nhClicked" value="<?php isset($mb_options['nhClicked']) ? print $mb_options['nhClicked'] : 'Need help clicked'?>" />
        <p>Need help was clicked</p>
    </div>

    <div class="form-group">
        <label>Event label [eventLabel]</label>
        <input type="text" class="form-control" name="nhClosed" value="<?php isset($mb_options['nhClosed']) ? print $mb_options['nhClosed'] : 'Need help closed'?>" />
        <p>Need help was closed</p>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>
