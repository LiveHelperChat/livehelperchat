<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?>*</label>
    <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Identifier');?>*</label>
    <input type="text" class="form-control form-control-sm" name="identifier" value="<?php echo htmlspecialchars($item->identifier);?>" />
</div>

<script>
    var priorityValue = <?php echo $item->configuration != '' ? $item->configuration : '[]'?>;
</script>

<div ng-controller="LHCPriorityCtrl as pchat" ng-init='pchat.setValue()'>

    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Main conditions');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a></h6>

    <textarea class="hide" name="configuration">{{pchat.value | json : 0}}</textarea>

    <div class="form-group">
        <input type="button" ng-click="pchat.addFilter()" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Add condition');?>">
    </div>

    <div class="row" ng-show="pchat.value.length > 0">
        <div class="col-11">
            <div class="row">
                <div class="col-5">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Field');?></label>
                </div>
                <div class="col-2">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Condition');?></label>
                </div>
                <div class="col-5">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Value');?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="row form-group" ng-repeat="filter in pchat.value">
        <div class="col-12">
            <div class="row">
                <div class="col-11">
                    <div class="row">
                        <div class="col-5">
                            <input class="form-control form-control-sm" ng-model="filter.field" name="field[{{$index}}]" type="text" value="" placeholder="field">
                        </div>
                        <div class="col-2">
                            <select class="form-control form-control-sm" name="comparator[{{$index}}]" ng-model="filter.comparator">
                                <option value="&gt;">&gt;</option>
                                <option value="&lt;">&lt;</option>
                                <option value="&gt;=">&gt;=</option>
                                <option value="&lt;=">&lt;=</option>
                                <option value="=">=</option>
                                <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Text like')?></option>
                                <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Text not like')?></option>
                                <option value="contains"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Contains')?></option>
                            </select>
                        </div>
                        <div class="col-5">
                            <input class="form-control form-control-sm" ng-model="filter.value" name="value[{{$index}}]" type="text" value="" placeholder="value">
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn btn-danger btn-sm btn-block" ng-click="pchat.removeFilter(filter)"><i class="material-icons me-0">&#xE872;</i></button>
                </div>
            </div>
        </div>
    </div>


</div>