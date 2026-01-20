<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Name');?></label>
    <input maxlength="50" type="text" class="form-control form-control-sm" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Name for personal reasons');?>" name="name" value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<ul class="nav nav-tabs mb-3" role="tablist" data-remember="true">
    <li role="presentation" class="nav-item"><a href="#hooks-settings" class="nav-link active" aria-controls="hooks-settings" role="tab" data-bs-toggle="tab" aria-selected="false"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Hooks events')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#chooks-settings" aria-controls="chooks-settings" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Continuous chat events')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#wchooks-settings" aria-controls="wchooks-settings" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Continuous mail events')?></a></li>
</ul>

<textarea name="configuration" class="hide" ng-model="webhooksctl.conditions_json"></textarea>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane form-group active" id="hooks-settings">
        <div class="form-group">
            <label><input type="radio" value="0" name="type" <?php if ($item->type == 0) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','This is hook event');?></label>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group" ng-non-bindable>
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Event');?><span class="text-muted fs13 ps-2">(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','required if it is hook event');?>)</span></label>
                    <input type="text" class="form-control form-control-sm" name="event" value="<?php echo htmlspecialchars($item->event);?>" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group" ng-non-bindable>
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Delay event processing by N seconds');?><span class="text-muted fs13 ps-2">(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','applied only if background workers are used');?>)</span></label>
                    <input type="number" max="60" min="0" class="form-control form-control-sm" name="delay" value="<?php echo htmlspecialchars($item->delay);?>" />
                </div>
            </div>
        </div>

    </div>
    <div role="tabpanel" class="tab-pane form-group" id="chooks-settings">
        <div class="form-group">
            <label><input type="radio" value="1" name="type" <?php if ($item->type == 1) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','This is continuous chat event');?></label>
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Make sure you define some conditions. Only pending, active and bot chats are checked against these conditions.');?></p>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane form-group" id="wchooks-settings">
        <div class="form-group">
            <label><input type="radio" value="2" name="type" <?php if ($item->type == 2) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','This is continuous mail event');?></label>
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Make sure you define some conditions. Only new, active mails are checked against these conditions.');?></p>

            <p><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Execute if conditions are NOT valid');?></b> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','are not executed in this event type.');?></p>

            <?php if ($item->type == 2 && $item->id > 0) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/edit')?>/<?php echo $item->id?>/(action)/reset_webhook" class="btn btn-danger btn-sm csfr-required"><span class="material-icons">restart_alt</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reset events. We will process matching messages again.');?></a>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>
            <ul>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Processed events');?> - <?php echo \LiveHelperChat\mailConv\Webhooks\Continous::getProcessedMailEvents($item); ?></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Last 10 messages processed');?>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Older events than 31 day are deleted automatically');?>
                        <?php foreach (\LiveHelperChat\mailConv\Webhooks\Continous::getLast10Events($item) as $event) : ?>
                        <span class="badge bg-info">
                            <?php $msg = erLhcoreClassModelMailconvMessage::fetch($event['message_id']); if ($msg instanceof erLhcoreClassModelMailconvMessage) : ?>
                            <a target="_blank" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message ID followed by conversation ID')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/single')?>/<?php echo $msg->conversation_id?>">
                               <?php echo $event['message_id']?>, <?php echo $msg->conversation_id?>
                            </a>
                            <?php endif;?>
                        </span>
                        <?php endforeach; ?>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<hr class="border-top">

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Conditions');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a><span class="text-muted fs13 ps-2">(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','optional');?>)</span></h5>

<div class="row mb-2">
    <div class="col-6">
        <select class="form-control form-control-sm" ng-model="webhooksctl.itemAdd">
            <option value="1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Visitor message contains');?></option>
            <option value="3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Compare attribute');?></option>
            <option value="4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Start of OR');?></option>
        </select>
    </div>
    <div class="col-6">
        <button type="button" class="btn btn-sm btn-light" ng-click="webhooksctl.addItem(webhooksctl.itemAdd)">Add</button>
    </div>
</div>
<div ng-repeat="condition in webhooksctl.conditions track by $index">
    <div class="row">
        <div class="col-8 pb-2">
            <div ng-if="condition.type == 1">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Visitor message contains');?></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">{{$index +1}}</span>
                    <input type="text" ng-model="condition.message_contains" class="form-control form-control-sm" value="" />
                </div>
            </div>
            <div ng-if="condition.type == 3">
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute');?></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">{{$index +1}}</span>
                                <input type="text" ng-model="condition.attr" placeholder="yes, thanks" class="form-control form-control-sm" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Condition');?></label>
                            <select class="form-control form-control-sm" ng-model="condition.condition">
                                <option value="gt">&gt;</option>
                                <option value="gte">&gt;=</option>
                                <option value="lt">&lt;</option>
                                <option value="lte">&lt;=</option>
                                <option value="eq">=</option>
                                <option value="neq">!=</option>
                                <option value="empty"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Empty');?></option>
                                <option value="notempty"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Not empty');?></option>
                                <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text like');?></option>
                                <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text not like');?></option>
                                <option value="contains"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Contains');?></option>
                                <option value="in_list"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','In list');?></option>
                                <option value="in_list_lowercase"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','In list (lowercase)');?></option>
                                <option value="not_in_list"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Not in list');?></option>
                                <option value="not_in_list_lowercase"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Not in list (lowercase)');?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Value');?></label>
                            <input type="text" ng-model="condition.value" placeholder="yes, thanks" class="form-control form-control-sm" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div ng-if="condition.type == 4">
                <div class="text-center fw-bold mt-4">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','OR');?>
                </div>
            </div>
        </div>
        <div class="col-2">
            <label class="d-block">&nbsp;</label>
            <select ng-if="condition.type != '4'" class="form-control form-control-sm ng-valid ng-not-empty ng-dirty ng-valid-parse ng-touched" ng-model="condition.logic">
                <option value="and" selected="selected"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','AND');?></option>
                <option value="or"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','OR');?></option>
            </select>
        </div>
        <div class="col-2" ng-if="transactionItem.type != '4'">
            <label class="d-block">&nbsp;</label>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" ng-if="$index > 0" ng-click="webhooksctl.moveUp(condition,webhooksctl.conditions)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_up</i></button>
                <button type="button" ng-if="webhooksctl.conditions.length > 0 && webhooksctl.conditions.length != $index + 1" ng-click="webhooksctl.moveDown(condition,webhooksctl.conditions)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_down</i></button>
                <button type="button" ng-click="webhooksctl.deleteCondition(condition)" class="btn btn-sm btn-danger"><i class="material-icons me-0">delete</i></button>
            </div>
        </div>
    </div>
</div>

<span ng-repeat="transactionItem in webhooksctl.conditions track by $index">
        {{((transactionItem.logic == 'or') && ($index == 0 || webhooksctl.conditions[$index - 1].logic == 'and' || !webhooksctl.conditions[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-if="transactionItem.type != '4'" ng-class="{'bg-success':!transactionItem.exclude,'bg-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (webhooksctl.conditions[$index - 1].logic == 'or' ) ? ' ) ' : ''}}
        {{(transactionItem.logic == 'or') ? ' or ' : ((transactionItem.type != 4 && $index+1 != webhooksctl.conditions.length && webhooksctl.conditions[$index + 1].type != 4) ? ' and ' : '')}}
        <span ng-if="transactionItem.type == '4'" class="mt-1 p-2 mb-1 badge bg-info fs14 d-block"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','OR');?></span>
        </span>
<span class="mt-1 mb-1 p-2 badge fs14 d-block bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Success');?></span>

<hr class="border-top">

<div class="row">
    <div class="col-6">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Execute if conditions are valid');?><span class="text-muted fs13 ps-2">(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','required');?>)</span></h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a bot');?></label>
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'bot_id',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose a bot'),
                'selected_id'    => $item->bot_id,
                'list_function'  => 'erLhcoreClassModelGenericBotBot::getList',
                'list_function_params'  => array('sort' => '`name` ASC', 'limit' => false)
            ) ); ?>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a trigger');?></label>
            <div id="trigger-list-id"></div>
        </div>
    </div>
    <div class="col-6">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Execute if conditions are NOT valid');?><span class="text-muted fs13 ps-2">(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','optional');?>)</span></h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a bot');?></label>
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'bot_id_alt',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose a bot'),
                'selected_id'    => $item->bot_id_alt,
                'list_function'  => 'erLhcoreClassModelGenericBotBot::getList',
                'list_function_params'  => array('sort' => '`name` ASC', 'limit' => false)
            ) ); ?>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Please choose a trigger');?></label>
            <div id="trigger-alt-list-id"></div>
        </div>

    </div>
</div>

<script>
    $('select[name="bot_id"]').change(function(){
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val(), { }, function(data) {
            $('#trigger-list-id').html(data);
        });
    });
    $('select[name="bot_id_alt"]').change(function(){
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val() + '/0/(element)/trigger_id_alt', { }, function(data) {
            $('#trigger-alt-list-id').html(data);
        });
    });
    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="bot_id"]').val() + '/<?php echo $item->trigger_id?>',  { }, function(data) {
        $('#trigger-list-id').html(data);
    });
    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="bot_id_alt"]').val() + '/<?php echo $item->trigger_id_alt?>/(element)/trigger_id_alt',  { }, function(data) {
        $('#trigger-alt-list-id').html(data);
    });
</script>

<div class="form-group">
    <label><input type="checkbox" value="on" name="disabled" <?php echo $item->disabled == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?></label>
</div>