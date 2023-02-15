<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Conditions')?></h6>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['mpc_nm']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('mpc_nm', $fields['mpc_nm'], $object, 0)?>
            <div class="small text-muted"><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','E.g 5, means there have to be 5 pending chats in the queue and I am 6 in the queue.')?></i></div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('ignore_pa_chat', $fields['ignore_pa_chat'], $object)?> <?php echo $fields['ignore_pa_chat']['trans'];?></label>
        </div>
    </div>
</div>

<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','OR')?>&nbsp;<span class="small text-muted fw-normal"><i>if any of conditions will be satisfied pending chat messaging will be activated.</i></span></h6>

<div class="accordion pb-2" id="accordionExample-pending">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne-pending">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-pending" aria-expanded="true" aria-controls="collapseOne-pending">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Activity period');?>&nbsp;<span class="small badge text-muted bg-light"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','click to expand');?></span>
            </button>
        </h2>
        <div id="collapseOne-pending" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body">

                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','You can make this canned message available only for certain period of times.');?></p>

                <ul>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 0 - 23, minutes format 0 - 59');?></li>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Your time zone');?> - <b><?php print date_default_timezone_get()?></b> <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></li>
                </ul>

                <div class="row">
                    <div class="col-6">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Period type');?></label>
                        <select class="form-control form-control-sm" name="AbstractInput_pnd_repetitiveness" ng-init="cannedRepeatPeriod='<?php echo isset($object->bot_configuration_array['pnd_repetitiveness']) ? $object->bot_configuration_array['pnd_repetitiveness'] : erLhcoreClassModelCannedMsg::REP_NO?>'" ng-model="cannedRepeatPeriod">
                            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_NO?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Not active');?></option>
                            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Week day');?></option>
                            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','One time period');?></option>
                            <option value="<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Annually');?></option>
                        </select>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Variable Time Zone');?></label>
                            <?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
                            <select name="AbstractInput_pnd_time_zone" class="form-control form-control-sm">
                                <option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Application default Time Zone');?>]]</option>
                                <?php foreach ($tzlist as $zone) : ?>
                                    <option value="<?php echo htmlspecialchars($zone)?>" <?php isset($object->bot_configuration_array['pnd_time_zone']) && $object->bot_configuration_array['pnd_time_zone'] == $zone ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($zone)?></option>
                                <?php endforeach;?>
                            </select>
                            <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','If you do not choose time zone in the back office replacement will be happening based on operator time zone. Variable used on widget interface - we will use visitor time zone.');?></i></small></p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?> date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>" ng-show="cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?>' || cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>'">

                    <p class="text-muted" ng-show="cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>'"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Even if you enter a year. This canned message will be active annually at the same time each year.');?></small></p>

                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active from');?></label>
                                <input class="form-control form-control-sm" name="AbstractInput_pnd_active_from_edit" type="datetime-local" value="<?php echo isset($object->bot_configuration_array['pnd_active_from_edit']) ? $object->bot_configuration_array['pnd_active_from_edit'] : ''?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active to');?></label>
                                <input class="form-control form-control-sm" name="AbstractInput_pnd_active_to_edit" type="datetime-local" value="<?php echo isset($object->bot_configuration_array['pnd_active_to_edit']) ? $object->bot_configuration_array['pnd_active_to_edit'] : ''?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-2 date-range-<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>" ng-show="cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>'">
                    <?php foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) : ?>
                        <div class="row">
                            <div class="col-12">
                                <label><input type="checkbox" ng-init="OnlineHoursDayActive<?php echo $dayShort ?>=<?php if (isset($object->bot_configuration_array['pnd_'.$dayShort]) && $object->bot_configuration_array['pnd_'.$dayShort] == 1) : ?>true<?php else : ?>false<?php endif?>" ng-model="OnlineHoursDayActive<?php echo $dayShort ?>" name="AbstractInput_pnd_<?php echo $dayShort ?>" value="1" <?php if (isset($object->bot_configuration_array['pnd_' . $dayShort]) && $object->bot_configuration_array['pnd_' . $dayShort] == 1) : ?>checked="checked"<?php endif;?> /> <?php echo $dayLong; ?></label>
                                <div class="row" ng-show="OnlineHoursDayActive<?php echo $dayShort ?>">
                                    <div class="col-3">
                                        <div class="form-group" ng-non-bindable>
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time from');?></label>
                                            <input name="AbstractInput_pnd_<?php echo $dayShort ?>_start_time" value="<?php if (isset($object->bot_configuration_array['pnd_'.$dayShort.'_start_time'])) : ?><?php echo htmlspecialchars($object->bot_configuration_array['pnd_'.$dayShort.'_start_time'])?><?php endif;?>" type="time" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group" ng-non-bindable>
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time to');?></label>
                                            <input name="AbstractInput_pnd_<?php echo $dayShort ?>_end_time" value="<?php if (isset($object->bot_configuration_array['pnd_'.$dayShort.'_end_time'])) : ?><?php echo htmlspecialchars($object->bot_configuration_array['pnd_'.$dayShort.'_end_time'])?><?php endif;?>" type="time" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</div>


















<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Messaging')?></h6>

<div class="form-group">
<label><?php echo $fields['repeat_number']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('repeat_number', $fields['repeat_number'], $object)?>
</div>

<div class="row">
    <div class="col-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout']['trans'];?> [1]</label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout', $fields['wait_timeout'], $object)?>
        </div>
    </div>
    <div class="col-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_message']['trans'];?> [1]</label>
        <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_timeout_message]'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message', $fields['timeout_message'], $object)?>
        </div>
    </div>
    <?php if (!isset($autoResponderOptions['hide_pending_bot']) || $autoResponderOptions['hide_pending_bot'] === false) : ?>
        <div class="col-9">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_bot_id_1']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('pending_op_bot_id_1', $fields['pending_op_bot_id_1'], $object)?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_1_trigger_id']['trans'];?></label>
                        <div id="pending_op_1-trigger-list-id"></div>
                    </div>
                </div>
                <div class="col-4">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
                    <div id="pending_op_1-trigger-preview-window">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php for ($i = 2; $i <= 5; $i++) : ?>
<div class="row">
    <div class="col-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout_' . $i]['trans'];?> [<?php echo $i?>]</label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout_' . $i, $fields['wait_timeout_' . $i], $object)?>
        </div>
    </div>
    <div class="col-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_message_' . $i]['trans'];?> [<?php echo $i?>]</label>
        <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_timeout_message_'.$i.']'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message_' . $i, $fields['timeout_message_' . $i], $object)?>
        </div>
    </div>
    <?php if (!isset($autoResponderOptions['hide_pending_bot']) || $autoResponderOptions['hide_pending_bot'] === false) : ?>
        <div class="col-9">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_bot_id_' . $i]['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('pending_op_bot_id_' . $i, $fields['pending_op_bot_id_' . $i], $object)?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label><?php echo $fields['pending_op_' . $i . '_trigger_id']['trans'];?></label>
                        <div id="pending_op_<?php echo $i ?>-trigger-list-id"></div>
                    </div>
                </div>
                <div class="col-4">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
                    <div id="pending_op_<?php echo $i ?>-trigger-preview-window">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endfor;?>