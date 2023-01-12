<script>
    var startChatFieldsConditions = <?php isset($start_chat_data['pre_conditions']) ? print $start_chat_data['pre_conditions'] : print "{'online': [],'offline': [],'disable': [],'offline_enabled' : false,'disable_enabled' : false, 'disable_message' : ''}"; ?>
</script>

<div ng-controller="StartChatFormPreconditions as preconditions"  ng-init='preconditions.setStartFields();'>

<textarea class="hide" name="pre_conditions" >{{preconditions.conditions | json : 0}}</textarea>

<p class="pt-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Possible use cases')?>
    <ul>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','With these rules you can enable chat only for specific visitors')?></li>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Go to maintenance mode and disable widget completely.')?></li>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Provide chat only for specific players and for others leave a message that chat is available only as example for VIP visitors.')?></li>
    </ul>
</p>
<hr>
<p>
    <strong><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/preconditions'});" class="material-icons text-muted">help</a><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Online conditions')?>:</strong>
<br>
<small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','If these conditions are met widget will become online')?></small>
</p>
<button type="button" class="btn btn-outline-secondary btn-xs" ng-click="preconditions.addField('online')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Add condition')?></button><span class="text-muted ps-2"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','conditions are optional')?>.</small></span>

<div class="row pt-2" ng-repeat="conditionItem in preconditions.conditions.online track by $index" >
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
                    <option value="in"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','one of')?></option>
                    <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','like')?></option>
                    <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','not like')?></option>
                    <option value="contains"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','contains')?></option>
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
            <button type="button" ng-if="preconditions.conditions.online.length > 0 && preconditions.conditions.online.length != $index + 1" ng-click="preconditions.moveDown(conditionItem,preconditions.conditions.online)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_down</i></button>
            <button type="button" ng-if="$index > 0" ng-click="preconditions.moveUp(conditionItem,preconditions.conditions.online)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_up</i></button>
            <button type="button" ng-click="preconditions.deleteElement(conditionItem,preconditions.conditions.online)" class="btn btn-sm btn-danger"><i class="material-icons me-0">delete</i></button>
        </div>
    </div>
</div>

<div class="pt-2">
    <span ng-repeat="transactionItem in preconditions.conditions.online track by $index">
        {{((transactionItem.logic == 'or') && ($index == 0 || preconditions.conditions.online[$index - 1].logic == 'and' || !preconditions.conditions.online[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-class="{'bg-success':!transactionItem.exclude,'bg-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (preconditions.conditions.online[$index - 1].logic == 'or') ? ' ) ' : ''}}
        {{(transactionItem.logic == 'or') ? ' or ' : (($index+1 != preconditions.conditions.online.length) ? ' and ' : '')}}
    </span>
    <span class="mt-1 mb-1 p-2 badge fs14 d-block bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Success')?></span>
</div>

<hr>


<p class="mb-1"><strong><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/preconditions'});" class="material-icons text-muted">help</a>Offline conditions:</strong><br>
    <small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Make widget offline if widget is not in oline mode')?></small>
</p>
    <label class="d-block"><input type="checkbox" value="on" ng-model="preconditions.conditions.offline_enabled" name="prec_enable_offline" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable offline mode');?></label>
    <div ng-show="preconditions.conditions.offline_enabled">
        <button type="button" class="btn btn-outline-secondary btn-xs" ng-click="preconditions.addField('offline')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Add condition')?></button><span class="text-muted ps-2"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','conditions are optional.')?></small></span>
        <div class="row pt-2" ng-repeat="conditionItem in preconditions.conditions.offline track by $index" >
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
                            <option value="in"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','one of')?></option>
                            <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','like')?></option>
                            <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','not like')?></option>
                            <option value="contains"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','contains')?></option>
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
                    <button type="button" ng-if="preconditions.conditions.offline.length > 0 && preconditions.conditions.offline.length != $index + 1" ng-click="preconditions.moveDown(conditionItem,preconditions.conditions.offline)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_down</i></button>
                    <button type="button" ng-if="$index > 0" ng-click="preconditions.moveUp(conditionItem,preconditions.conditions.offline)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_up</i></button>
                    <button type="button" ng-click="preconditions.deleteElement(conditionItem,preconditions.conditions.offline)" class="btn btn-sm btn-danger"><i class="material-icons me-0">delete</i></button>
                </div>
            </div>
        </div>
        <div class="pt-2">
                        <span ng-repeat="transactionItem in preconditions.conditions.offline track by $index">
                            {{((transactionItem.logic == 'or') && ($index == 0 || preconditions.conditions.offline[$index - 1].logic == 'and' || !preconditions.conditions.offline[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-class="{'bg-success':!transactionItem.exclude,'bg-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (preconditions.conditions.offline[$index - 1].logic == 'or') ? ' ) ' : ''}}
                            {{(transactionItem.logic == 'or') ? ' or ' : (($index+1 != preconditions.conditions.offline.length) ? ' and ' : '')}}
                        </span>
            <span class="mt-1 mb-1 p-2 badge fs14 d-block bg-success">Success</span>
        </div>
    </div>
<hr>

<p class="mb-1"><strong><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/preconditions'});" class="material-icons text-muted">help</a>Disable conditions.</strong><br>
<small>If widget is not in online/offline mode after online conditions check. We will show a custom message once they open a widget.</small>
</p>
<label class="d-block"><input type="checkbox" value="on" ng-model="preconditions.conditions.disable_enabled" name="prec_enable_disable" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable disable mode');?></label>
    <div ng-show="preconditions.conditions.disable_enabled">
        <button type="button" class="btn btn-outline-secondary btn-xs" ng-click="preconditions.addField('disable')">Add condition</button><span class="text-muted ps-2"><small>conditions are optional.</small></span>
        <div class="row pt-2" ng-repeat="conditionItem in preconditions.conditions.disable track by $index" >
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
                            <option value="in"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','one of')?></option>
                            <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','like')?></option>
                            <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','not like')?></option>
                            <option value="contains"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','contains')?></option>
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
                    <button type="button" ng-if="preconditions.conditions.disable.length > 0 && preconditions.conditions.disable.length != $index + 1" ng-click="preconditions.moveDown(conditionItem,preconditions.conditions.disable)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_down</i></button>
                    <button type="button" ng-if="$index > 0" ng-click="preconditions.moveUp(conditionItem,preconditions.conditions.disable)" class="btn btn-sm btn-secondary"><i class="material-icons">keyboard_arrow_up</i></button>
                    <button type="button" ng-click="preconditions.deleteElement(conditionItem,preconditions.conditions.disable)" class="btn btn-sm btn-danger"><i class="material-icons me-0">delete</i></button>
                </div>
            </div>
        </div>
        <div class="pt-2">
                        <span ng-repeat="transactionItem in preconditions.conditions.disable track by $index">
                            {{((transactionItem.logic == 'or') && ($index == 0 || preconditions.conditions.disable[$index - 1].logic == 'and' || !preconditions.conditions.disable[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-class="{'bg-success':!transactionItem.exclude,'bg-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (preconditions.conditions.disable[$index - 1].logic == 'or') ? ' ) ' : ''}}
                            {{(transactionItem.logic == 'or') ? ' or ' : (($index+1 != preconditions.conditions.disable.length) ? ' and ' : '')}}
                        </span>
            <span class="mt-1 mb-1 p-2 badge fs14 d-block bg-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Success')?></span>
        </div>

        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Show this message to the visitors who opens a chat widget.')?></label>
        <textarea class="form-control form-control-sm" ng-model="preconditions.conditions.disable_message"></textarea>

    </div>
<hr>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','If none of the above conditions are met widget will become hidden.')?></p>

</div>