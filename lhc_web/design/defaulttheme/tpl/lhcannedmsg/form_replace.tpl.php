<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Identifier, use it like {identifier} in canned responses, or bot');?></label>
    <input maxlength="50" type="text" placeholder="E.g brand_name. Do not enter brackets" ng-non-bindable class="form-control form-control-sm" name="identifier" value="<?php echo htmlspecialchars($item->identifier);?>" />
</div>

<script>
    (function(){
        window.replaceConditions = <?php echo json_encode($item->conditions_array)?>;
        window.replaceDepartments = <?php $items = []; foreach (erLhcoreClassModelDepartament::getList(['limit' => false]) as $itemDepartment) { $items[$itemDepartment->id] = $itemDepartment->name; }; echo json_encode($items) ?>;
        window.replaceConditions.forEach(function(elm){

            if (typeof elm.cannedRepeatPeriod === 'undefined') {
                elm.cannedRepeatPeriod = '0';
            }

            ['active_from','active_to',
                'modStartTime','modEndTime',
                'tudStartTime','tudEndTime',
                'wedStartTime','wedEndTime',
                'thdStartTime','thdEndTime',
                'frdStartTime','frdEndTime',
                'sadStartTime','sadEndTime',
                'sudStartTime','sudEndTime',
            ].forEach(function(element) {
                if (typeof elm[element] !== undefined && elm[element] !== null && elm[element] !== '') {
                    elm[element] = new Date(elm[element]);
                }
            });
        });
    })();
</script>

<div ng-controller="CannedReplaceCtrl as crc" class="pb-1" ng-init='crc.setConditions()'>

    <textarea class="hide" name="conditions">{{crc.combinations | json : 0}}</textarea>

    <ul class="nav nav-pills" role="tablist" id="canned-main-tabs">
        <li role="presentation" class="nav-item" ><a class="nav-link active" href="#default" aria-controls="default" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default');?></a></li>
        <li ng-repeat="lang in crc.combinations" class="nav-item" role="presentation">

            <a href="#cmb-{{$index}}" class="nav-link" aria-controls="cmb-{{$index}}" role="tab" data-bs-toggle="tab" >
                <i class="material-icons">find_replace</i>{{lang.name || 'Nr. ' + ($index + 1)}}
                <span ng-click="crc.deleteElement(lang, crc.combinations)" class="material-icons icon-close-chat">close</span>
            </a>
        </li>
        <li class="nav-item"><a href="#addcombination" class="nav-link" ng-click="crc.addCombination()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add combination');?></a></li>
        <li role="presentation" class="nav-item" ><a class="nav-link" href="#activity-period" aria-controls="activity-period" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Activity period');?></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="default">
            <div class="form-group" ng-non-bindable>
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default value');?></label>
                <textarea rows="5" ng-trim="false" class="form-control form-control-sm" name="default"><?php echo htmlspecialchars($item->default);?></textarea>
            </div>
        </div>
        <div ng-repeat="combination in crc.combinations track by $index" role="tabpanel" class="tab-pane" id="cmb-{{$index}}">

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Tab custom name');?></label>
                <input type="text" ng-trim="false" placeholder="Custom tab title" ng-model="combination.name" class="form-control form-control-sm" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Value');?></label>
                <textarea rows="5" ng-trim="false" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Put a custom value here');?>" ng-model="combination.value" class="form-control form-control-sm"></textarea>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Priority');?></label>
                <input class="form-control form-control-sm" type="number" ng-model="combination.priority">
                <small><p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Rules with highest priority will be checked first');?></p></small>
            </div>

            <div class="accordion pb-2" id="accordionExample-{{$index}}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne-{{$index}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-{{$index}}" aria-expanded="true" aria-controls="collapseOne-{{$index}}">
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Conditions');?>
                        </button>
                    </h2>
                    <div id="collapseOne-{{$index}}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-12">
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department filter');?></label>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                                            'input_name'     => 'department_id-{{$index}}',
                                            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                                            'selected_id'    => "0",
                                            'ng-model'       => 'combination.dep_id',
                                            'ng-change'      => 'crc.addOption(combination)',
                                            'type'           => 'radio',
                                            'data_prop'      => 'data-limit="1"',
                                            'css_class'      => 'form-control',
                                            'display_name'   => 'name',
                                            'show_optional'  => true,
                                            'list_function_params' => array('limit' => false,'sort' => '`name` ASC'),
                                            'list_function'  => 'erLhcoreClassModelDepartament::getList',
                                        )); ?>
                                    </div>
                                </div>
                                <div class="col-8">
                    <span ng-repeat="dep_id in combination.dep_ids track by $index" role="tabpanel" ng-click="crc.deleteElement(dep_id,combination.dep_ids)" title="Click to remove" class="badge bg-secondary m-1 action-image">
                        {{crc.departments[dep_id]}} <span class="material-icons text-warning me-0">delete</span>
                    </span>
                                </div>
                            </div>

                            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Advanced filtering');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a></h6>

                            <button type="button" class="btn btn-sm btn-secondary" ng-click="crc.addCondition(combination)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add condition');?></button>

                            <div class="row pt-1" ng-repeat="conditionItem in combination.conditions track by $index" >
                                <div class="col-9">
                                    <div class="row">
                                        <div class="col-4">
                                            <input class="form-control form-control-sm" ng-model="conditionItem.field" name="field[{{$index}}]" type="text" value="" placeholder="field">
                                        </div>
                                        <div class="col-2">
                                            <select class="form-control form-control-sm" ng-model="conditionItem.comparator">
                                                <option value="gt">&gt;</option>
                                                <option value="lt">&lt;</option>
                                                <option value="gte">&gt;=</option>
                                                <option value="lte">&lt;=</option>
                                                <option value="eq">=</option>
                                                <option value="neq">!=</option>
                                                <option value="like">like</option>
                                                <option value="notlike">not like</option>
                                                <option value="contains">contains</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <input class="form-control form-control-sm" ng-model="conditionItem.value" name="value[{{$index}}]" type="text" value="" placeholder="value">
                                        </div>
                                        <div class="col-2">
                                            <select class="form-control form-control-sm" ng-model="conditionItem.logic">
                                                <option value="and">AND</option>
                                                <option value="or">OR</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" ng-if="combination.conditions.length > 0 && combination.conditions.length != $index + 1" ng-click="crc.moveDown(conditionItem,combination.conditions)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_down</i></button>
                                        <button type="button" ng-if="$index > 0" ng-click="crc.moveUp(conditionItem,combination.conditions)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_up</i></button>
                                        <button type="button" ng-click="crc.deleteElement(conditionItem,combination.conditions)" class="btn btn-sm btn-danger"><i class="material-icons me-0">delete</i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                    <span ng-repeat="transactionItem in combination.conditions track by $index">
                        {{((transactionItem.logic == 'or') && ($index == 0 || combination.conditions[$index - 1].logic == 'and' || !combination.conditions[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-class="{'bg-success':!transactionItem.exclude,'bg-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (combination.conditions[$index - 1].logic == 'or') ? ' ) ' : ''}}
                        {{(transactionItem.logic == 'or') ? ' or ' : (($index+1 != combination.conditions.length) ? ' and ' : '')}}
                    </span>
                                <span class="mt-1 mb-1 p-2 badge fs14 d-block bg-success">Success</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo-{{$index}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo-{{$index}}" aria-expanded="false" aria-controls="collapseTwo-{{$index}}">
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Activity period');?>
                        </button>
                    </h2>
                    <div id="collapseTwo-{{$index}}" class="accordion-collapse collapse" aria-labelledby="headingTwo-{{$index}}" data-bs-parent="#accordionExample-{{$index}}">
                        <div class="accordion-body">





                            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','You can make this canned message available only for certain period of times.');?></p>

                            <ul>
                                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 0 - 23, minutes format 0 - 59');?></li>
                                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Your time zone');?> - <b><?php print date_default_timezone_get()?></b> <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></li>
                            </ul>

                            <div class="row">
                                <div class="col-6">
                                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Period type');?></label>
                                    <select class="form-control form-control-sm" ng-model="combination.cannedRepeatPeriod">
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
                                        <select ng-model="combination.time_zone" class="form-control form-control-sm">
                                            <option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Application default Time Zone');?>]]</option>
                                            <?php foreach ($tzlist as $zone) : ?>
                                                <option value="<?php echo htmlspecialchars($zone)?>"><?php echo htmlspecialchars($zone)?></option>
                                            <?php endforeach;?>
                                        </select>
                                        <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','If you do not choose time zone in the back office replacement will be happening based on operator time zone. Variable used on widget interface - we will use visitor time zone.');?></i></small></p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?> date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>" ng-show="combination.cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?>' || combination.cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>'">

                                <p class="text-muted" ng-show="combination.cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>'"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Even if you enter a year. This canned message will be active annually at the same time each year.');?></small></p>

                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active from');?></label>
                                            <input class="form-control form-control-sm" ng-model="combination.active_from" type="datetime-local" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active to');?></label>
                                            <input class="form-control form-control-sm" ng-model="combination.active_to" type="datetime-local">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 date-range-<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>" ng-show="combination.cannedRepeatPeriod == '<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>'">
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
                                            <label><input type="checkbox" ng-model="combination.OnlineHoursDayActive<?php echo $dayShort ?>" name="<?php echo $dayShort ?>" value="1" <?php if (isset($item->days_activity_array[$dayShort])) : ?>checked="checked"<?php endif;?> /> <?php echo $dayLong; ?></label>
                                            <div class="row" ng-show="combination.OnlineHoursDayActive<?php echo $dayShort ?>">
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time from');?></label>
                                                        <input ng-model="combination.<?php echo $dayShort ?>StartTime" type="time" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time to');?></label>
                                                        <input ng-model="combination.<?php echo $dayShort ?>EndTime" type="time" class="form-control form-control-sm">
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


        </div>


        <div role="tabpanel" class="tab-pane pb-2" id="activity-period">

            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','You can make this canned message available only for certain period of times.');?></p>

            <ul>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 0 - 23, minutes format 0 - 59');?></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Your time zone');?> - <b><?php print date_default_timezone_get()?></b> <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></li>
            </ul>

            <div class="row">
                <div class="col-6">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Period type');?></label>
                    <select class="form-control form-control-sm" name="repetitiveness" ng-init="cannedRepeatPeriod='<?php echo $item->repetitiveness?>'" ng-model="cannedRepeatPeriod">
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
                        <select name="time_zone" class="form-control form-control-sm">
                            <option value="">[[<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Application default Time Zone');?>]]</option>
                            <?php foreach ($tzlist as $zone) : ?>
                                <option value="<?php echo htmlspecialchars($zone)?>" <?php $item->time_zone == $zone ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($zone)?></option>
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
                            <input class="form-control form-control-sm" name="active_from" type="datetime-local" value="<?php echo $item->active_from_edit/* date('Y-m-d\TH:i', $item->active_from > 0 ? $item->active_from : time())*/?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active to');?></label>
                            <input class="form-control form-control-sm" name="active_to" type="datetime-local" value="<?php echo $item->active_to_edit /*date('Y-m-d\TH:i', $item->active_to > 0 ? $item->active_to : time())*/?>">
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
                            <label><input type="checkbox" ng-init="OnlineHoursDayActive<?php echo $dayShort ?>=<?php if (isset($item->days_activity_array[$dayShort])) : ?>true<?php else : ?>false<?php endif?>" ng-model="OnlineHoursDayActive<?php echo $dayShort ?>" name="<?php echo $dayShort ?>" value="1" <?php if (isset($item->days_activity_array[$dayShort])) : ?>checked="checked"<?php endif;?> /> <?php echo $dayLong; ?></label>
                            <div class="row" ng-show="OnlineHoursDayActive<?php echo $dayShort ?>">
                                <div class="col-3">
                                    <div class="form-group" ng-non-bindable>
                                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time from');?></label>
                                        <?php

                                        if (isset($item->days_activity_array[$dayShort]['start'])) {
                                            $minutesStart = str_pad(substr($item->days_activity_array[$dayShort]['start'],-2),2,'0', STR_PAD_LEFT);
                                            $hoursStart = str_pad(substr($item->days_activity_array[$dayShort]['start'],0,strlen($item->days_activity_array[$dayShort]['start']) - 2), 2, '0', STR_PAD_LEFT);
                                        } else {
                                            $minutesStart = $hoursStart = '00';
                                        }

                                        if (isset($item->days_activity_array[$dayShort]['end'])){
                                            $minutesEnd = str_pad(substr($item->days_activity_array[$dayShort]['end'],-2),2,'0', STR_PAD_LEFT);
                                            $hoursEnd = str_pad(substr($item->days_activity_array[$dayShort]['end'],0,strlen($item->days_activity_array[$dayShort]['end']) - 2), 2, '0', STR_PAD_LEFT);
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
    </div>
</div>