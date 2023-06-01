<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" ng-non-bindable class="form-control form-control-sm" name="Name" value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="row">
    <div class="col-12">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Brand members');?></label>
    </div>
    <div class="col-4">
        <div class="form-group">
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'department_id',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                'selected_id'    => "0",
                'ng-model'       => 'combination.dep_id',
                'ng-change'      => 'crc.addMember(combination)',
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
        <span ng-repeat="member in crc.members track by $index" role="tabpanel" class="badge bg-secondary m-1 action-image">
            {{crc.departments[member]}} <span ng-click="crc.deleteMember(member)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Click to remove');?>" class="material-icons text-warning me-0">delete</span>
            <input type="hidden" name="department[]" ng-value="member" >
            <input type="text" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Role');?>" name="role[{{member}}]" ng-model="crc.departmentsRoles[member]" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Role');?>">
        </span>
    </div>
</div>
