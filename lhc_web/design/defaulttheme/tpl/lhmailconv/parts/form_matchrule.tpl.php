
<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Do these actions if rule matches');?></h6>

<div class="row" ng-non-bindable>

    <div class="col-12">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Name for personal reasons');?></label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Set department to');?></label>
            <?php
            $params = array (
                'input_name'     => 'dep_id',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'selected_id'    => $item->dep_id,
                'list_function'  => 'erLhcoreClassModelDepartament::getList',
                'list_function_params'  => array('limit' => false, 'sort' => '`name` ASC'),
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose')
            );
            echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Priority conversation should get');?></label>
            <input type="text" class="form-control form-control-sm" name="priority" value="<?php echo htmlspecialchars($item->priority)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="close_conversation" value="on" <?php isset($item->options_array['close_conversation']) && $item->options_array['close_conversation'] == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Close conversation');?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="skip_message" value="on" <?php isset($item->options_array['skip_message']) && $item->options_array['skip_message'] == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Skip message, messages will not be imported');?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" name="block_rule" value="on" <?php isset($item->options_array['block_rule']) && $item->options_array['block_rule'] == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Matching rule applies to blocked e-mails');?></label>
        </div>
    </div>
</div>

<hr>

<h6>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Conditions');?>
</h6>

<label><input type="checkbox" onclick="$('#mailbox-list input').prop( 'checked', $(this).is(':checked') );"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Choose all mailbox');?></label>

<div class="form-group" >
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Mail is send to one of these mailbox');?></label>
    <br>
    <div class="row" style="max-height: 500px; overflow: auto" id="mailbox-list">
    <?php echo erLhcoreClassRenderHelper::renderCheckbox( array (
        'input_name'     => 'mailbox_ids[]',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Select mail'),
        'selected_id'    => $item->mailbox_ids,
        'css_class'      => 'form-control mailbox-item',
        'wrap_prepend'   => '<div class="col-3">',
        'wrap_append'    => '</div>',
        'display_name'   => function($item){
            return $item->name. ' ('. $item->mail.')';
        },
        'list_function_params' => ['limit' => false, 'sort' => '`mail` ASC'],
        'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
    )); ?>
    </div>
</div>


<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','From mail');?></label>
    <textarea class="form-control form-control-sm" name="from_mail" placeholder="example1@example.org,example2@example.org"><?php echo htmlspecialchars($item->from_mail)?></textarea>
</div>

<div class="row" ng-non-bindable>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','From name');?></label>
            <textarea class="form-control form-control-sm" name="from_name" placeholder="Live Helper Chat"><?php echo htmlspecialchars($item->from_name)?></textarea>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Subject contains');?></label>
            <textarea class="form-control form-control-sm" name="subject_contains" placeholder="Live Helper Chat"><?php echo htmlspecialchars($item->subject_contains)?></textarea>
        </div>
    </div>
    <div class="col-12">
        <p><i><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Every possible combination should start from a new line.');?><br><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','E.g fish,car && price{2}$ - fish or car word plus price can have two typos.');?></small></i></small></i></p>
    </div>
</div>

<hr class="border-top">

<textarea name="conditions" class="hide" ng-model="webhooksctl.conditions_json"></textarea>

<h6>
    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attributes conditions');?><span class="text-muted fs13 ps-2">(<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','optional');?>)</span>
    <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/matchingruleconditions'});" class="material-icons text-muted">help</a>
</h6>

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
                                <option value="empty">is empty</option>
                                <option value="notempty">not empty</option>
                                <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text like');?></option>
                                <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text not like');?></option>
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


<div class="row" ng-non-bindable>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Priority of matching rule. Rules with lower number will be checked first.');?></label>
            <input type="text" class="form-control form-control-sm" name="priority_rule" value="<?php echo htmlspecialchars($item->priority_rule)?>" />
        </div>
    </div>
</div>



<div class="form-group">
    <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Active');?></label>
</div>