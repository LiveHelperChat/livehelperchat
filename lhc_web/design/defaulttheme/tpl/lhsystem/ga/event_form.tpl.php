<?php if (isset($eventtrackoptions['show_department']) && $eventtrackoptions['show_department'] == true) : ?>
    <div class="form-group" ng-non-bindable>
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Name');?></label>
        <input type="text" class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Name');?>" name="name" value="<?php echo htmlspecialchars($event_item->name);?>" />
    </div>
    <div class="form-group" ng-non-bindable>
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></label>
        <?php
        $params = array (
            'input_name'     => 'DepartmentID',
            'display_name'   => 'name',
            'css_class'      => 'form-control',
            'selected_id'    => $event_item->department_id,
            'list_function'  => 'erLhcoreClassModelDepartament::getList',
            'list_function_params'  => array('limit' => '1000000')
        );
        echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
    </div>
<?php endif; ?>


<div class="form-group">
    <label ng-non-bindable ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Javascript to execute on event. {{eventCategory}}, {{eventAction}}, {{eventLabel}}, {{eventInternal}} you can use as placeholders.')?></label>

    <select onchange="document.getElementById('id-ga_js').value = this.value;" class="form-control form-control-sm mb-2" ng-non-bindable >
        <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Choose one of the possible templates')?></option>
        <option value="console.log({{eventCategory}}+'-'+{{eventAction}}+'-'+{{eventLabel}}+'-'+{{eventInternal}});">Custom</option>
        <option value="<?php echo $ga = htmlspecialchars("if (typeof gtag !== \"undefined\") {
gtag('event', {{eventAction}}, {  'event_category': {{eventCategory}},  'event_label': {{eventLabel}} });
} else if (\"ga\" in window) {
    var galist = ga.getAll(); for (var i = 0;i < galist.length; i++) {var tracker = galist[i];tracker && tracker.send(\"event\", {{eventCategory}}, {{eventAction}}, {{eventLabel}});}
}");?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Google Analytics')?></option>
        <option value="typeof _paq !== 'undefined' && _paq.push(['trackEvent', {{eventCategory}}, {{eventAction}}, {{eventLabel}}]);">Matomo</option>
    </select>

    <textarea id="id-ga_js" rows="6" ng-non-bindable name="ga_js" class="form-control form-control-sm"><?php isset($ga_options['ga_js']) ? print htmlspecialchars($ga_options['ga_js']) : print $ga?></textarea>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Javascript for static URL. Paste your GA or any other script here.')?></label>
    <textarea rows="6" ng-non-bindable name="js_static" class="form-control form-control-sm"><?php isset($ga_options['js_static']) ? print htmlspecialchars($ga_options['js_static']) : print ''?></textarea>
</div>

<ul class="nav nav-tabs mb-3" role="tablist" ng-non-bindable>
    <li role="presentation" class="nav-item"><a href="#widget" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="widget" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Widget');?></a></li>
    <li role="presentation" class="nav-item"><a href="#chat" class="nav-link<?php if ($tab == 'chat') : ?> active<?php endif;?>" aria-controls="chat" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Chat');?></a></li>
    <li role="presentation" class="nav-item"><a href="#invitation" class="nav-link<?php if ($tab == 'invitation') : ?> active<?php endif;?>" aria-controls="invitation" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Invitation');?></a></li>
    <li role="presentation" class="nav-item"><a href="#needhelp" class="nav-link<?php if ($tab == 'needhelp') : ?> active<?php endif;?>" aria-controls="needhelp" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Need help');?></a></li>
    <li role="presentation" class="nav-item"><a href="#bot" class="nav-link<?php if ($tab == 'bot') : ?> active<?php endif;?>" aria-controls="bot" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking','Bot');?></a></li>
</ul>

<div class="tab-content" ng-non-bindable>
    <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="widget">

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [clickAction]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['clickAction_on']) && $ga_options['clickAction_on'] == 1) : ?>checked="checked"<?php endif;?> name="clickAction_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Status was clicked')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="clickAction_category" value="<?php isset($ga_options['clickAction_category']) ? print htmlspecialchars($ga_options['clickAction_category']) : print 'Widget'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="clickAction_action" value="<?php isset($ga_options['clickAction_action']) ? print htmlspecialchars($ga_options['clickAction_action']) : print 'Status Clicked'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="clickAction_label" value="<?php isset($ga_options['clickAction_label']) ? print htmlspecialchars($ga_options['clickAction_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [showWidget]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['showWidget_on']) && $ga_options['showWidget_on'] == 1) : ?>checked="checked"<?php endif;?> name="showWidget_on"> Widget was shown</label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="showWidget_category" value="<?php isset($ga_options['showWidget_category']) ? print htmlspecialchars($ga_options['showWidget_category']) : print 'Widget'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="showWidget_action" value="<?php isset($ga_options['showWidget_action']) ? print htmlspecialchars($ga_options['showWidget_action']) : print 'Show'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="showWidget_label" value="<?php isset($ga_options['showWidget_label']) ? print htmlspecialchars($ga_options['showWidget_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [closeWidget]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['closeWidget_on']) && $ga_options['closeWidget_on'] == 1) : ?>checked="checked"<?php endif;?> name="closeWidget_on"> Widget was closed/minimized</label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="closeWidget_category" value="<?php isset($ga_options['closeWidget_category']) ? print htmlspecialchars($ga_options['closeWidget_category']) : print 'Widget'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="closeWidget_action" value="<?php isset($ga_options['closeWidget_action']) ? print htmlspecialchars($ga_options['closeWidget_action']) : print 'Close'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="closeWidget_label" value="<?php isset($ga_options['closeWidget_label']) ? print htmlspecialchars($ga_options['closeWidget_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [openPopup]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['openPopup_on']) && $ga_options['openPopup_on'] == 1) : ?>checked="checked"<?php endif;?> name="openPopup_on"> Popup opened</label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="openPopup_category" value="<?php isset($ga_options['openPopup_category']) ? print htmlspecialchars($ga_options['openPopup_category']) : print 'Widget'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="openPopup_action" value="<?php isset($ga_options['openPopup_action']) ? print htmlspecialchars($ga_options['openPopup_action']) : print 'Popup'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="openPopup_label" value="<?php isset($ga_options['openPopup_label']) ? print htmlspecialchars($ga_options['openPopup_label']) : ''?>" />
                </div>
            </div>
        </div>


    </div>
    <div role="tabpanel" class="tab-pane <?php if ($tab == 'chat') : ?>active<?php endif;?>" id="chat">

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [endChat]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['endChat_on']) && $ga_options['endChat_on'] == 1) : ?>checked="checked"<?php endif;?> name="endChat_on"> Chat ended</label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="endChat_category" value="<?php isset($ga_options['endChat_category']) ? print htmlspecialchars($ga_options['endChat_category']) : print 'Chat'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="endChat_action" value="<?php isset($ga_options['endChat_action']) ? print htmlspecialchars($ga_options['endChat_action']) : print 'Ended'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="endChat_label" value="<?php isset($ga_options['endChat_label']) ? print htmlspecialchars($ga_options['endChat_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [chatStarted]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['chatStarted_on']) && $ga_options['chatStarted_on'] == 1) : ?>checked="checked"<?php endif;?> name="chatStarted_on"> Chat started</label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="chatStarted_category" value="<?php isset($ga_options['chatStarted_category']) ? print htmlspecialchars($ga_options['chatStarted_category']) : print 'Chat'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="chatStarted_action" value="<?php isset($ga_options['chatStarted_action']) ? print htmlspecialchars($ga_options['chatStarted_action']) : print 'Started'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="chatStarted_label" value="<?php isset($ga_options['chatStarted_label']) ? print htmlspecialchars($ga_options['chatStarted_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [offlineMessage]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['offlineMessage_on']) && $ga_options['offlineMessage_on'] == 1) : ?>checked="checked"<?php endif;?> name="offlineMessage_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Offline message')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="offlineMessage_category" value="<?php isset($ga_options['offlineMessage_category']) ? print htmlspecialchars($ga_options['offlineMessage_category']) : print 'Chat'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="offlineMessage_action" value="<?php isset($ga_options['offlineMessage_action']) ? print htmlspecialchars($ga_options['offlineMessage_action']) : print 'Offline message'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="offlineMessage_label" value="<?php isset($ga_options['offlineMessage_label']) ? print htmlspecialchars($ga_options['offlineMessage_label']) : ''?>" />
                </div>
            </div>
        </div>

    </div>
    <div role="tabpanel" class="tab-pane <?php if ($tab == 'invitation') : ?>active<?php endif;?>" id="invitation">

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [showInvitation]" class="font-weight-bold"><input type="checkbox" <?php if (isset($ga_options['showInvitation_on']) && $ga_options['showInvitation_on'] == 1) : ?>checked="checked"<?php endif;?> value="on" name="showInvitation_on"> Invitation shown</label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="showInvitation_category" value="<?php isset($ga_options['showInvitation_category']) ? print htmlspecialchars($ga_options['showInvitation_category']) : print 'Invitation'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="showInvitation_action" value="<?php isset($ga_options['showInvitation_action']) ? print htmlspecialchars($ga_options['showInvitation_action']) : print 'Show'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="showInvitation_label" value="<?php isset($ga_options['showInvitation_label']) ? print htmlspecialchars($ga_options['showInvitation_label']) : ''?>" />
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'If you leave empty we will set automatically invitation name.')?></p>
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [hideInvitation]" class="font-weight-bold"><input type="checkbox" <?php if (isset($ga_options['hideInvitation_on']) && $ga_options['hideInvitation_on'] == 1) : ?>checked="checked"<?php endif;?> value="on" name="hideInvitation_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Invitation hide')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="hideInvitation_category" value="<?php isset($ga_options['hideInvitation_category']) ? print htmlspecialchars($ga_options['hideInvitation_category']) : print 'Invitation'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="hideInvitation_action" value="<?php isset($ga_options['hideInvitation_action']) ? print htmlspecialchars($ga_options['hideInvitation_action']) : print 'Hide'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="hideInvitation_label" value="<?php isset($ga_options['hideInvitation_label']) ? print htmlspecialchars($ga_options['hideInvitation_label']) : ''?>" />
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'If you leave empty we will set automatically invitation name.')?></p>
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [cancelInvitation]" class="font-weight-bold"><input type="checkbox" <?php if (isset($ga_options['cancelInvitation_on']) && $ga_options['cancelInvitation_on'] == 1) : ?>checked="checked"<?php endif;?> value="on" name="cancelInvitation_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Cancel invitation')?>.  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', ' Called if invitation was in full widget and widget was minimised or visitor clicked close icon in invitation tooltip')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="cancelInvitation_category" value="<?php isset($ga_options['cancelInvitation_category']) ? print htmlspecialchars($ga_options['cancelInvitation_category']) : print 'Invitation'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="cancelInvitation_action" value="<?php isset($ga_options['cancelInvitation_action']) ? print htmlspecialchars($ga_options['cancelInvitation_action']) : print 'Cancel'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="cancelInvitation_label" value="<?php isset($ga_options['cancelInvitation_label']) ? print htmlspecialchars($ga_options['cancelInvitation_label']) : ''?>" />
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'If you leave empty we will set automatically invitation name.')?></p>
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [fullInvitation]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['fullInvitation_on']) && $ga_options['fullInvitation_on'] == 1) : ?>checked="checked"<?php endif;?> name="fullInvitation_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Invitation clicked. Visitor clicked invitation tooltip. It is not triggered if it just clicks status icon while invitation tooltip is shown.')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="fullInvitation_category" value="<?php isset($ga_options['fullInvitation_category']) ? print htmlspecialchars($ga_options['fullInvitation_category']) : print 'Invitation'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="fullInvitation_action" value="<?php isset($ga_options['fullInvitation_action']) ? print htmlspecialchars($ga_options['fullInvitation_action']) : print 'Clicked'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="fullInvitation_label" value="<?php isset($ga_options['fullInvitation_label']) ? print htmlspecialchars($ga_options['fullInvitation_label']) : ''?>" />
                    <p>If you leave empty we will set automatically invitation name.</p>
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [readInvitation]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['readInvitation_on']) && $ga_options['readInvitation_on'] == 1) : ?>checked="checked"<?php endif;?> name="readInvitation_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Invitation was read. Means visitor opened widget with an invitation either by clicking invitation tooltip or status icon.')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="readInvitation_category" value="<?php isset($ga_options['readInvitation_category']) ? print htmlspecialchars($ga_options['readInvitation_category']) : print 'Invitation'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="readInvitation_action" value="<?php isset($ga_options['readInvitation_action']) ? print htmlspecialchars($ga_options['readInvitation_action']) : print 'Read'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="readInvitation_label" value="<?php isset($ga_options['readInvitation_label']) ? print htmlspecialchars($ga_options['readInvitation_label']) : ''?>" />
                    <p>If you leave empty we will set automatically invitation name.</p>
                </div>
            </div>
        </div>


    </div>

    <div role="tabpanel" class="tab-pane <?php if ($tab == 'needhelp') : ?>active<?php endif;?>" id="needhelp">

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [nhShow]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['nhShow_on']) && $ga_options['nhShow_on'] == 1) : ?>checked="checked"<?php endif;?> name="nhShow_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Need help was shown')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhShow_category" value="<?php isset($ga_options['nhShow_category']) ? print htmlspecialchars($ga_options['nhShow_category']) : print 'Need help'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhShow_action" value="<?php isset($ga_options['nnhShow_action']) ? print htmlspecialchars($ga_options['nhShow_action']) : print 'Show'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="nhShow_label" value="<?php isset($ga_options['nhShow_label']) ? print htmlspecialchars($ga_options['nhShow_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [nhHide]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['nhHide_on']) && $ga_options['nhHide_on'] == 1) : ?>checked="checked"<?php endif;?> name="nhHide_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Need help was hidden')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhHide_category" value="<?php isset($ga_options['nhHide_category']) ? print htmlspecialchars($ga_options['nhHide_category']) : print 'Need help'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhHide_action" value="<?php isset($ga_options['nhHide_action']) ? print htmlspecialchars($ga_options['nhHide_action']) : print 'Show'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="nhHide_label" value="<?php isset($ga_options['nhHide_label']) ? print htmlspecialchars($ga_options['nhHide_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [nhClicked]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['nhClicked_on']) && $ga_options['nhClicked_on'] == 1) : ?>checked="checked"<?php endif;?> name="nhClicked_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Need help was clicked')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhClicked_category" value="<?php isset($ga_options['nhClicked_category']) ? print htmlspecialchars($ga_options['nhClicked_category']) : print 'Need help'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhClicked_action" value="<?php isset($ga_options['nhClicked_action']) ? print htmlspecialchars($ga_options['nhClicked_action']) : print 'Clicked'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="nhClicked_label" value="<?php isset($ga_options['nhClicked_label']) ? print htmlspecialchars($ga_options['nhClicked_label']) : ''?>" />
                </div>
            </div>
        </div>

        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [nhClosed]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['nhClosed_on']) && $ga_options['nhClosed_on'] == 1) : ?>checked="checked"<?php endif;?> name="nhClosed_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Need help close icon was clicked')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhClosed_category" value="<?php isset($ga_options['nhClosed_category']) ? print htmlspecialchars($ga_options['nhClosed_category']) : print 'Need help'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="nhClosed_action" value="<?php isset($ga_options['nhClosed_action']) ? print htmlspecialchars($ga_options['nhClosed_action']) : print 'Closed'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <input type="text" class="form-control form-control-sm" name="nhClosed_label" value="<?php isset($ga_options['nhClosed_label']) ? print htmlspecialchars($ga_options['nhClosed_label']) : ''?>" />
                </div>
            </div>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane <?php if ($tab == 'bot') : ?>active<?php endif;?>" id="bot">
        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Track this event')?> [botTrigger]" class="font-weight-bold"><input type="checkbox" value="on" <?php if (isset($ga_options['botTrigger_on']) && $ga_options['botTrigger_on'] == 1) : ?>checked="checked"<?php endif;?> name="botTrigger_on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Bot trigger was executed')?></label>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Category')?> [eventCategory]*</label>
                    <input type="text" class="form-control form-control-sm" name="botTrigger_category" value="<?php isset($ga_options['botTrigger_category']) ? print htmlspecialchars($ga_options['botTrigger_category']) : print 'Bot'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event action')?> [eventAction]*</label>
                    <input type="text" class="form-control form-control-sm" name="botTrigger_action" value="<?php isset($ga_options['botTrigger_action']) ? print htmlspecialchars($ga_options['botTrigger_action']) : print 'Trigger'?>" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Event label')?> [eventLabel]</label>
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'We will set eventLabel to trigger name')?></p>
                </div>
            </div>
        </div>
    </div>
</div>