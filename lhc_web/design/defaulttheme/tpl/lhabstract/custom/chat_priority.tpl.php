<?php $fields = $object->getFields();?>

<div class="form-group">
    <label><?php echo $fields['dep_id']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('dep_id', $fields['dep_id'], $object)?>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['priority']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('priority', $fields['priority'], $object)?>
        </div>
    </div>
    <div class="col-6">

        <div class="row">
            <div class="col-5">
                <div class="form-group">
                    <label><?php echo $fields['role_destination']['trans'];?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('role_destination', $fields['role_destination'], $object)?>

                    <datalist id="brand-role-list">
                    <?php foreach (\LiveHelperChat\Models\Brand\BrandMember::getList(['group' => 'role']) as $brand) : ?>
                        <option value="<?php echo $brand->role?>">
                    <?php endforeach; ?>
                    </datalist>


                </div>
            </div>
            <div class="col-2 text-center fw-bold">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','OR');?>
            </div>
            <div class="col-5">
                <div class="form-group">
                    <label><?php echo $fields['dest_dep_id']['trans'];?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('dest_dep_id', $fields['dest_dep_id'], $object)?>
                </div>
            </div>
        </div>

    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['sort_priority']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('sort_priority', $fields['sort_priority'], $object)?>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['present_role_is']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('present_role_is', $fields['present_role_is'], $object)?>
        </div>
    </div>
</div>

<script>
    var priorityValue = <?php echo $object->value != '' ? $object->value : '[]'?>;
</script>

<div ng-controller="LHCPriorityCtrl as pchat" ng-init='pchat.setValue()'>

    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Main conditions');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a></h6>

    <textarea class="hide" name="AbstractInput_value">{{pchat.value | json : 0}}</textarea>

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
                                <option value="!=">!=</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <input class="form-control form-control-sm" ng-model="filter.value" name="value[{{$index}}]" type="text" value="" placeholder="value">
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn btn-danger btn-block btn-sm" ng-click="pchat.removeFilter(filter)"><i class="material-icons me-0">&#xE872;</i></button>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="btn-group btn-group-sm" role="group" aria-label="...">
    <input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>