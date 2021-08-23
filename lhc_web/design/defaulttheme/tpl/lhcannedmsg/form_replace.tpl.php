<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Identifier, use it like {identifier} in canned responses, or bot');?></label>
    <input maxlength="50" type="text" placeholder="E.g brand_name. Do not enter brackets" ng-non-bindable class="form-control form-control-sm" name="identifier" value="<?php echo htmlspecialchars($item->identifier);?>" />
</div>

<script>
    var replaceConditions = <?php echo json_encode($item->conditions_array)?>;
</script>

<div ng-controller="CannedReplaceCtrl as crc" class="pb-1" ng-init='crc.setConditions()'>

    <textarea class="hide" name="conditions">{{crc.combinations | json : 0}}</textarea>

    <ul class="nav nav-pills" role="tablist" id="canned-main-tabs">
        <li role="presentation" class="nav-item" ><a class="nav-link active" href="#default" aria-controls="default" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default');?></a></li>
        <li ng-repeat="lang in crc.combinations" class="nav-item" role="presentation">

            <a href="#cmb-{{$index}}" class="nav-link" aria-controls="cmb-{{$index}}" role="tab" data-toggle="tab" >
                <i class="material-icons">find_replace</i>Nr. {{$index + 1}}
                <span ng-click="crc.deleteElement(lang, crc.combinations)" class="material-icons icon-close-chat">close</span>
            </a>
        </li>
        <li class="nav-item"><a href="#addcombination" class="nav-link" ng-click="crc.addCombination()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add combination');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="default">
            <div class="form-group" ng-non-bindable>
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Default value');?></label>
                <textarea class="form-control form-control-sm" name="default"><?php echo htmlspecialchars($item->default);?></textarea>
            </div>
        </div>
        <div ng-repeat="combination in crc.combinations track by $index" role="tabpanel" class="tab-pane" id="cmb-{{$index}}">

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Value');?></label>
                <textarea placeholder="Put custom value here" ng-model="combination.value" class="form-control form-control-sm"></textarea>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Priority');?></label>
                <input class="form-control form-control-sm" type="number" ng-model="combination.priority">
                <small><p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Rules with highest priority will be checked first');?></p></small>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department filter');?></label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'department_id',
                    'ng-model'       => 'combination.dep_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => "0",
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function_params' => array(),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>

            <h6>Advanced filtering</h6>

            <button type="button" class="btn btn-sm btn-secondary" ng-click="crc.addCondition(combination)">Add condition</button>

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
                        <button type="button" ng-click="crc.deleteElement(conditionItem,combination.conditions)" class="btn btn-sm btn-danger"><i class="material-icons mr-0">delete</i></button>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                    <span ng-repeat="transactionItem in combination.conditions track by $index">
                        {{((transactionItem.logic == 'or') && ($index == 0 || combination.conditions[$index - 1].logic == 'and' || !combination.conditions[$index - 1].logic)) ? ' ( ' : ''}}<span class="badge" ng-class="{'badge-success':!transactionItem.exclude,'badge-danger':transactionItem.exclude}">{{$index + 1}}.</span>{{transactionItem.logic == 'and' && (combination.conditions[$index - 1].logic == 'or') ? ' ) ' : ''}}
                        {{(transactionItem.logic == 'or') ? ' or ' : (($index+1 != combination.conditions.length) ? ' and ' : '')}}
                    </span>
                <span class="mt-1 mb-1 p-2 badge fs14 d-block badge-success">Success</span>
            </div>

        </div>
    </div>



</div>