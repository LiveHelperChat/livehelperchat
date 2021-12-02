<script>
    var startChatFieldsConditions = {
        'online': [],
        'offline': [],
        'disable': []
    };
</script>

<div ng-controller="StartChatFormPreconditions as preconditions"  ng-init='preconditions.setStartFields();'>

<br>
<p><strong>Online conditions:</strong><br>
<small>If these conditions are met widget will become online</small>
</p>
<button type="button" class="btn btn-outline-secondary btn-xs" ng-click="preconditions.addField('online')">Add condition</button>

<div class="row pt-1" ng-repeat="conditionItem in preconditions.conditions.online track by $index" >
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
            <button type="button" ng-if="preconditions.conditions.online.length > 0 && preconditions.conditions.online.length != $index + 1" ng-click="crc.moveDown(conditionItem,preconditions.conditions.online)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_down</i></button>
            <button type="button" ng-if="$index > 0" ng-click="crc.moveUp(conditionItem,preconditions.conditions.online)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_up</i></button>
            <button type="button" ng-click="crc.deleteElement(conditionItem,preconditions.conditions.online)" class="btn btn-sm btn-danger"><i class="material-icons mr-0">delete</i></button>
        </div>
    </div>
</div>

<div class="pt-2">
                    <span ng-repeat="transactionItem in preconditions.conditions.online track by $index">
                        {{((transactionItem.logic == 'or') && ($index == 0 || preconditions.conditions.online[$index - 1].logic == 'and' || !preconditions.conditions.online[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-class="{'badge-success':!transactionItem.exclude,'badge-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (preconditions.conditions.online[$index - 1].logic == 'or') ? ' ) ' : ''}}
                        {{(transactionItem.logic == 'or') ? ' or ' : (($index+1 != preconditions.conditions.online.length) ? ' and ' : '')}}
                    </span>
    <span class="mt-1 mb-1 p-2 badge fs14 d-block badge-success">Success</span>
</div>



<hr>

<p class="mb-1"><strong>Offline conditions:</strong><br>
    <small>Make widget offline if widget is not in oline mode</small>
</p>
    <label><input type="checkbox" value="on" name="prec_enable_offline" <?php (isset($start_chat_data['prec_enable_offline']) && $start_chat_data['prec_enable_offline'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable offline mode');?></label>
<hr>

<p class="mb-1"><strong>Disable conditions.</strong><br>
<small>If widget is not in online/offline mode after online conditions check. We will show a custom message once they open a widget.</small>
</p>
<label><input type="checkbox" value="on" name="prec_enable_disable" <?php (isset($start_chat_data['prec_enable_disable']) && $start_chat_data['prec_enable_disable'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable disable mode');?></label>

<hr>

<p>If none of the above conditions are met widget will become hidden.</p>



</div>