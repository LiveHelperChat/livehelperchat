<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','You can make this canned message available only for certain period of times.');?></p>

<ul>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Work hours, 24 hours format, 0 - 23, minutes format 0 - 59');?></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These hours will be using');?> <b><?php print date_default_timezone_get()?></b> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','time zone');?> <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Days activity will be using time set my database server.');?> <b>[<?php
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT NOW()");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $data['now()'];
        ?>]</b></li>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time zone used for the visitor will be');?> <b>[<?php
            if (erConfigClassLhConfig::getInstance()->getSetting('site','time_zone', false) != '') : ?>
                <?php echo erConfigClassLhConfig::getInstance()->getSetting('site','time_zone', false).' ' . (new DateTime('now', new DateTimeZone(erConfigClassLhConfig::getInstance()->getSetting('site','time_zone', false))))->format('Y-m-d H:i:s')?>
             <?php else : ?>
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit', 'Server default timezone.')?> <?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s')?>
                <span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','If you have set your time zone in account. Make sure you set it in default settings file also.');?></span>
            <?php endif; ?>
    ]</b></li>
    <li>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','All time zones and times should match before making any adjustments to activity period.');?>
    </li>
</ul>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Period type');?></label>
<select id="canned-repeat-period" class="form-control form-control-sm" name="AbstractInput_repetitiveness">
    <option value="<?php echo erLhcoreClassModelCannedMsg::REP_NO?>" <?php if ($object->repetitiveness == erLhcoreClassModelCannedMsg::REP_NO) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Not active');?></option>
    <option value="<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>" <?php if ($object->repetitiveness == erLhcoreClassModelCannedMsg::REP_DAILY) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Week day');?></option>
    <option value="<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?>" <?php if ($object->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','One time period');?></option>
    <option value="<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>" <?php if ($object->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD_REP) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Annually');?></option>
</select>

<div class="pt-2 show-by-period show-by-period-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD?> show-by-period-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?> hide date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?> date-range-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>">

    <p class="text-muted show-by-period hide show-by-period-<?php echo erLhcoreClassModelCannedMsg::REP_PERIOD_REP?>"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Even if you enter a year. This proactive invitation will be active annually at the same time each year.');?></small></p>

    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active from');?></label>
                <input class="form-control form-control-sm" name="AbstractInput_active_from" type="datetime-local" value="<?php echo date('Y-m-d\TH:i', $object->active_from > 0 ? $object->active_from : time())?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Active to');?></label>
                <input class="form-control form-control-sm" name="AbstractInput_active_to" type="datetime-local" value="<?php echo $object->active_to > 0 ? date('Y-m-d\TH:i', $object->active_to) : ""?>">
            </div>
        </div>
    </div>
</div>

<div class="pt-2 show-by-period show-by-period-<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?> hide date-range-<?php echo erLhcoreClassModelCannedMsg::REP_DAILY?>">
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
                <label><input type="checkbox" class="show-by-date" name="<?php echo $dayShort ?>" value="1" <?php if (isset($object->days_activity_array[$dayShort])) : ?>checked="checked"<?php endif;?> /> <?php echo $dayLong; ?></label>
                <div class="row hide show-by-date-<?php echo $dayShort ?>">
                    <div class="col-3">
                        <div class="form-group" >
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time from');?></label>
                            <?php

                            if (isset($object->days_activity_array[$dayShort]['start'])) {
                                $minutesStart = str_pad(substr($object->days_activity_array[$dayShort]['start'],-2),2,'0', STR_PAD_LEFT);
                                $hoursStart = str_pad(substr($object->days_activity_array[$dayShort]['start'],0,strlen($object->days_activity_array[$dayShort]['start']) - 2), 2, '0', STR_PAD_LEFT);
                            } else {
                                $minutesStart = $hoursStart = '00';
                            }

                            if (isset($object->days_activity_array[$dayShort]['end'])){
                                $minutesEnd = str_pad(substr($object->days_activity_array[$dayShort]['end'],-2),2,'0', STR_PAD_LEFT);
                                $hoursEnd = str_pad(substr($object->days_activity_array[$dayShort]['end'],0,strlen($object->days_activity_array[$dayShort]['end']) - 2), 2, '0', STR_PAD_LEFT);
                            } else {
                                $minutesEnd = $hoursEnd = '00';
                            }

                            ?>
                            <input name="<?php echo $dayShort ?>StartTime" value="<?php echo htmlspecialchars($hoursStart.':'.$minutesStart)?>" type="time" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group" >
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time to');?></label>
                            <input name="<?php echo $dayShort ?>EndTime" value="<?php echo htmlspecialchars($hoursEnd.':'.$minutesEnd)?>" type="time" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<hr>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation is active if any of these are true.')?></p>

<div class="form-group">
    <label><?php echo $fields['on_op_id']['trans'];?> - <b><?php echo erLhcoreClassUser::instance()->getUserID()?></b></label>
    <?php echo erLhcoreClassAbstract::renderInput('on_op_id', $fields['on_op_id'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['op_max_chats']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('op_max_chats', $fields['op_max_chats'], $object)?>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If operator has 3 max chats, and you enter here 2. Means proactive invitation will be active only if operator has less than 5 chats assigned to him.')?></i></small></p>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Other conditions')?></h5>
<div class="form-group">
    <label><?php echo $fields['last_visit_prev']['trans'];?></label>
    <select class="form-control form-control-sm" name="AbstractInput_last_visit_prev">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Does not apply');?></option>
        <option value="60" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
        <option value="300" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="600" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="1800" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="3600" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
        <option value="7200" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="14400" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="28800" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="57600" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="86400" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
    </select>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If visitor arrived to website today, and they were on website yesterday. This value holds last time visitor was seen on website yesterday.')?></i></small></p>
</div>

<div class="form-group">
    <label><?php echo $fields['last_chat']['trans'];?></label>
    <select class="form-control form-control-sm" name="AbstractInput_last_chat">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Does not apply');?></option>
        <option value="60" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
        <option value="300" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="600" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="1800" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="3600" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
        <option value="7200" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="14400" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="28800" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="57600" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="86400" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
    </select>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attributes conditions conditions')?>

    <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/proactiveconditions'});" class="material-icons text-muted">help</a>

</h5>
<p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/widgettheme","You can filter by `online_attr_system` attribute key and it's value. Multiple values can be separated by ||")?></i></small></p>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/attributes_conditions.tpl.php'));?>
