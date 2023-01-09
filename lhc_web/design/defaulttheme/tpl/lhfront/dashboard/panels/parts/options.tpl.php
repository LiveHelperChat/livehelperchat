<div class="p-<?php isset($optinsPanel['padding_filters']) ? print ((int)$optinsPanel['padding_filters']) : print 2;?>">
	<div class="row">
		<div class="col-<?php isset($optinsPanel['userid']) ? print 6 : print (!isset($optinsPanel['hide_limits']) ? 10 : 12)?> pe-0">
			<div class="btn-group btn-block btn-block-department">
				<button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
					{{<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>.length == 0 ? "<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','All departments');?>" : (<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>.length == 1 && <?php echo isset($optinsPanel['no_names_department']) &&$optinsPanel['no_names_department'] == true ? 'false'  : 'true' ?> ? <?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>Names.join(", ") : '['+<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>.length+'] '+'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','departments');?>')}}
				</button>
				<ul class="dropdown-menu" role="menu">
                    <li class="dropdown-result">
                        <ul class="list-unstyled dropdown-lhc">
                            <?php if (!isset($optinsPanel['hide_department_variations'])) : ?>
                            <li><label><input type="checkbox" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_all_departments"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Check all')?></label></li>
                            <li><label><input type="checkbox" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_only_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only online')?></label></li>
                            <li><label><input type="checkbox" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_only_explicit_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only explicit online')?></label></li>
                            <li><label><input type="checkbox" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_hidden"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide hidden')?></label></li>
                            <?php endif; ?>

                            <?php if (isset($optinsPanel['hide_department']) && $optinsPanel['hide_department'] == true) : ?>
                            <li><label><input type="checkbox" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_dep"> <i class="material-icons">home</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide departments')?></label></li>
                            <?php endif; ?>

                            <?php if (isset($optinsPanel['hide_depgroup']) && $optinsPanel['hide_depgroup'] == true) : ?>
                            <li><label><input type="checkbox" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_dgroup"> <i class="material-icons">&#xE84F;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide departments groups')?></label></li>
                            <?php endif; ?>

                            <?php if (!isset($optinsPanel['hide_department_variations'])) : ?>
                            <li class="border-bottom"><label><input data-stopPropagation="true" ng-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" type="checkbox" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_disabled"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide disabled')?></label></li>
                            <?php endif; ?>

                            <?php if (!isset($optinsPanel['disable_product']) || $optinsPanel['disable_product'] == false) : ?>
                            <li ng-repeat="product in <?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.userProductNames" data-stopPropagation="true"><label><input type="checkbox" checklist-model="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>_products" checklist-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.productChanged('<?php echo $optinsPanel['panelid']?>_products')" checklist-value="product.id"><i class="material-icons">&#xE8CC;</i>{{product.name}}</label></li>
                            <li ng-show="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.userProductNames.length > 0" class="border-bottom"></li>
                            <?php endif;?>

                            <li ng-repeat="department in lhc.userDepartmentsGroups" data-stopPropagation="true"><label><input type="checkbox" checklist-model="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>_dpgroups" checklist-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.productChanged('<?php echo $optinsPanel['panelid']?>_dpgroups')" checklist-value="department.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Department group')?>" class="material-icons">&#xE84F;</i>{{department.name}}</label></li>
                            <li ng-show="lhc.userDepartmentsGroups.length > 0" class="border-bottom"></li>
                            <li class="p-1"><input type="text" data-stopPropagation="true" ng-model="lhc.depFilterText" placeholder="Search for department" class="form-control form-control-sm" value="" /></li>

                            <li class="dropdown-result" style="max-height: 218px;min-height: 218px">
                                <ul class="list-unstyled dropdown-lhc">
                                    <li ng-repeat="department in lhc.userDepartments" data-stopPropagation="true" ng-hide="((<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>_only_explicit_online == true && department.oexp == false) || (<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>_hide_hidden == true && department.hidden == true) || (<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>_hide_disabled == true && department.disabled == true) || (<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>_only_online == true && department.ogen == false))"><label><input type="checkbox" checklist-model="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.<?php echo $optinsPanel['panelid']?>" checklist-change="<?php echo isset($optinsPanel['controller_panel']) ? $optinsPanel['controller_panel'] : 'lhc'?>.departmentChanged('<?php echo $optinsPanel['panelid']?>')" checklist-value="department.id"><i ng-class="{'chat-active':department.slc}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Department')?>" class="material-icons">home</i>{{department.name}}</label></li>
                                </ul>
                            </li>

                        </ul>
                    </li>
				</ul>
			</div>
		</div>
		<?php if (isset($optinsPanel['userid'])) : ?>
		<div class="col-4 pe-0">
            <div class="btn-group btn-block btn-block-department">
                <button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Users')?>
                </button>
                <ul class="dropdown-menu dropdown-lhc" role="menu">
                    <li class="p-1"><input type="text" data-stopPropagation="true" ng-model="lhc.userFilterText" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Search for operator')?>" class="form-control form-control-sm" value=""></li>
                    <li class="dropdown-result">
                        <ul class="list-unstyled dropdown-lhc">
                            <li ng-repeat="userItem in lhc.userList" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['userid']?>" checklist-change="lhc.productChanged('<?php echo $optinsPanel['userid']?>')" checklist-value="userItem.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','User')?>" class="material-icons">account_box</i>{{userItem.name || userItem.name_official}}</label></li>
                            <li ng-show="lhc.userGroups.length > 0" class="border-top"></li>
                            <li ng-repeat="userGroup in lhc.userGroups" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>_ugroups" checklist-change="lhc.productChanged('<?php echo $optinsPanel['panelid']?>_ugroups')" checklist-value="userGroup.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','User group')?>" class="material-icons">people</i>{{userGroup.name}}</label></li>
                        </ul>
                    </li>
                </ul>
            </div>
		</div>
		<?php endif; ?>

        <?php if (!isset($optinsPanel['hide_limits'])) : ?>
		<div class="col-2">
			<select class="form-control form-control-sm btn-light" ng-model="lhc.<?php echo $optinsPanel['limitid']?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Number of elements in list');?>">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
		</div>
        <?php endif; ?>

	</div>
</div>