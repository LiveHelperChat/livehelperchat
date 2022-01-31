<script>
    var languageCanned<?php echo $canned_message->id?> = <?php echo json_encode(json_decode($canned_message->languages, true), JSON_HEX_APOS) ?>;
    var languageDialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>;
</script>

<div id="canned-controller" ng-controller="CannedMsgCtrl as cmsg" ng-cloak ng-init='cmsg.initController()<?php if ($canned_message->languages != '') : ?>cmsg.initLanguage(<?php echo $canned_message->id?>);<?php endif;?>'>

<ul class="nav nav-pills" role="tablist" id="canned-main-tabs">
    <li role="presentation" class="nav-item" ><a class="nav-link active" href="#main" aria-controls="main" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Main');?></a></li>
    <li role="presentation" class="nav-item" ><a class="nav-link" href="#activity-period" aria-controls="activity-period" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Activity period');?></a></li>
    <li ng-repeat="lang in cmsg.languages" class="nav-item" role="presentation"><a href="#lang-{{$index}}" class="nav-link" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i> [{{cmsg.getLanguagesChecked(lang)}}]</a></li>
    <li class="nav-item"><a href="#addlanguage" class="nav-link" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add translation');?></a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="main">

        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title');?></label>
            <input type="text" ng-non-bindable class="form-control form-control-sm" name="Title" value="<?php echo htmlspecialchars($canned_message->title);?>" />
        </div>

        <label><input type="checkbox" name="Disabled" value="on" <?php $canned_message->disabled == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Disabled');?></label>

        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("chat/cannedmsg","Tag's");?></label>
            <input type="text" ng-non-bindable class="form-control form-control-sm" name="Tags" value="<?php echo htmlspecialchars($canned_message->tags_plain)?>" />
        </div>

        <?php if ($canned_message->id > 0) : ?>
        <label>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("chat/cannedmsg","Subject");?> <button type="button" class="btn btn-xs btn-outline-secondary pb-1 pl-1" onclick="return lhc.revealModal({'url':'/index.php/site_admin/cannedmsg/subject/<?php echo $canned_message->id?>'})"><i class="material-icons mr-0"></i></button>
            <div id="canned-message-subjects-<?php echo $canned_message->id?>"></div>
            <script>
                $.get(WWW_DIR_JAVASCRIPT + 'cannedmsg/subject/<?php echo $canned_message->id?>/?getsubjects=1', function(data) {
                    $('#canned-message-subjects-<?php echo $canned_message->id?>').html(data);
                });
            </script>
        </label>
        <?php endif; ?>

        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Explain');?></label>
            <input type="text" ng-non-bindable class="form-control form-control-sm" name="ExplainHover" value="<?php echo htmlspecialchars($canned_message->explain);?>" />
        </div>

        <div class="row">
            <div class="col-6">
                <label><input type="checkbox" name="AutoSend" value="on" <?php $canned_message->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
                    <input type="text" ng-non-bindable class="form-control form-control-sm" name="Delay" value="<?php echo $canned_message->delay?>" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?>
                <a class="live-help-tooltip" data-placement="top" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','The smaller the position value the higher the canned message will appear in the list')?>" data-toggle="tooltip" ><i class="material-icons">&#xE887;</i></a>
            </label>
            <input type="text" ng-non-bindable class="form-control form-control-sm" ng-non-bindable name="Position" value="<?php echo $canned_message->position?>" />
        </div>

        <?php $showAnyDepartment = erLhcoreClassUser::instance()->hasAccessTo('lhchat','see_global'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/department.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_cannedmsgform_multiinclude.tpl.php')); ?>

        <ul class="nav nav-pills" role="tablist" id="canned-main-extension">
            <li role="presentation" class="nav-item"><a class="active nav-link" href="#main-extension" aria-controls="main-extension" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_multiinclude.tpl.php')); ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main-extension">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                    <?php $bbcodeOptions = array('selector' => '#canned-message'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea ng-non-bindable class="form-control" rows="5" id="canned-message" name="Message"><?php echo htmlspecialchars($canned_message->msg);?></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                    <?php $bbcodeOptions = array('selector' => '#id-FallbackMessage'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea ng-non-bindable class="form-control" id="id-FallbackMessage" rows="5" name="FallbackMessage"><?php echo htmlspecialchars($canned_message->fallback_msg);?></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','HTML Snippet');?></label>
                    <textarea ng-non-bindable class="form-control" rows="5" name="HTMLSnippet"><?php echo htmlspecialchars($canned_message->html_snippet);?></textarea>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_content_multiinclude.tpl.php')); ?>
        </div>

    </div>

    <div role="tabpanel" class="tab-pane pb-2" id="activity-period">

        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','You can make this canned message available only for certain period of times.');?></p>

        <ul>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 0 - 23, minutes format 0 - 59');?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These hours will be using');?> <b><?php print date_default_timezone_get()?></b> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','time zone');?> <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></li>
        </ul>

        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Period type');?></label>
        <select class="form-control form-control-sm" name="repetitiveness" ng-init="cannedRepeatPeriod='<?php echo $canned_message->repetitiveness?>'" ng-model="cannedRepeatPeriod">
            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_NO?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Not active');?></option>
            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Week day');?></option>
            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','One time period');?></option>
            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Annually');?></option>
        </select>

        <div class="pt-2 date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?> date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>" ng-show="cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?>' || cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>'">

            <p class="text-muted" ng-show="cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>'"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Even if you enter a year. This canned message will be active annually at the same time each year.');?></small></p>

            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active from');?></label>
                        <input class="form-control form-control-sm" name="active_from" type="datetime-local" value="<?php echo date('Y-m-d\TH:i', $canned_message->active_from > 0 ? $canned_message->active_from : time())?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active to');?></label>
                        <input class="form-control form-control-sm" name="active_to" type="datetime-local" value="<?php echo date('Y-m-d\TH:i', $canned_message->active_to > 0 ? $canned_message->active_to : time())?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-2 date-range-<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>" ng-show="cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>'">
            <?php foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) : ?>
                <?php
                $startHourName = $dayShort.'_start_hour';
                $startHourFrontName = $dayShort.'_start_hour_front';
                $startMinutesFrontName = $dayShort.'_start_minutes_front';
                $endHourFrontName = $dayShort.'_end_hour_front';
                $endMinutesFrontName = $dayShort.'_end_minutes_front';
                ?>
                <div class="row">
                    <div class="col-12">
                        <label><input type="checkbox" ng-init="OnlineHoursDayActive<?php echo $dayShort ?>=<?php if (isset($canned_message->days_activity_array[$dayShort])) : ?>true<?php else : ?>false<?php endif?>" ng-model="OnlineHoursDayActive<?php echo $dayShort ?>" name="<?php echo $dayShort ?>" value="1" <?php if (isset($canned_message->days_activity_array[$dayShort])) : ?>checked="checked"<?php endif;?> /> <?php echo $dayLong; ?></label>
                        <div class="row" ng-show="OnlineHoursDayActive<?php echo $dayShort ?>">
                            <div class="col-3">
                                <div class="form-group" ng-non-bindable>
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time from');?></label>
                                    <?php

                                    if (isset($canned_message->days_activity_array[$dayShort]['start'])) {
                                        $minutesStart = str_pad(substr($canned_message->days_activity_array[$dayShort]['start'],-2),2,'0', STR_PAD_LEFT);
                                        $hoursStart = str_pad(substr($canned_message->days_activity_array[$dayShort]['start'],0,strlen($canned_message->days_activity_array[$dayShort]['start']) - 2), 2, '0', STR_PAD_LEFT);
                                    } else {
                                        $minutesStart = $hoursStart = '00';
                                    }

                                    if (isset($canned_message->days_activity_array[$dayShort]['end'])){
                                        $minutesEnd = str_pad(substr($canned_message->days_activity_array[$dayShort]['end'],-2),2,'0', STR_PAD_LEFT);
                                        $hoursEnd = str_pad(substr($canned_message->days_activity_array[$dayShort]['end'],0,strlen($canned_message->days_activity_array[$dayShort]['end']) - 2), 2, '0', STR_PAD_LEFT);
                                    } else {
                                        $minutesEnd = $hoursEnd = '00';
                                    }

                                    ?>
                                    <input name="<?php echo $dayShort ?>StartTime" value="<?php echo htmlspecialchars($hoursStart.':'.$minutesStart)?>" type="time" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group" ng-non-bindable>
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time to');?></label>
                                    <input name="<?php echo $dayShort ?>EndTime" value="<?php echo htmlspecialchars($hoursEnd.':'.$minutesEnd)?>" type="time" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/language_choose.tpl.php'));?>

        <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="nav-item"><a class="active nav-link" href="#main-extension-lang-{{$index}}" aria-controls="main-extension-lang-{{$index}}" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_lang_tab_multiinclude.tpl.php')); ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main-extension-lang-{{$index}}">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                    <?php $bbcodeOptions = array('selector' => '#message_lang-{{$index}}'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea class="form-control" rows="5" id="message_lang-{{$index}}" name="message_lang[{{$index}}]" ng-model="lang.message"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                    <?php $bbcodeOptions = array('selector' => '#fallback_message_lang-{{$index}}'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea class="form-control" rows="5" id="fallback_message_lang-{{$index}}" name="fallback_message_lang[{{$index}}]" ng-model="lang.fallback_message"></textarea>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_lang_tab_content_multiinclude.tpl.php')); ?>
        </div>

    </div>

</div>

</div>
<script>
    $('.live-help-tooltip').tooltip();
</script>