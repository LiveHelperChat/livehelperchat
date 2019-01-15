<div class="p-2">
	<div class="row">
		<div class="col-<?php isset($optinsPanel['userid']) ? print 6 : print 10?> pr-0">
			<div class="btn-group btn-block btn-block-department">
				<button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-toggle="dropdown" aria-expanded="false">
					{{lhc.<?php echo $optinsPanel['panelid']?>.length == 0 ? "<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','All departments');?>" : lhc.<?php echo $optinsPanel['panelid']?>Names.join(", ")}}
				</button>
				<ul class="dropdown-menu" role="menu">
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_all_departments"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Check all')?></label></li>
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_only_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only online')?></label></li>
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_only_explicit_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only explicit online')?></label></li>
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_hidden"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide hidden')?></label></li>
				    <li class="border-bottom"><label><input data-stopPropagation="true" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" type="checkbox" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_disabled"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide disabled')?></label></li>

				    <?php if (!isset($optinsPanel['disable_product']) || $optinsPanel['disable_product'] == false) : ?>
				    <li ng-repeat="product in lhc.userProductNames" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>_products" checklist-change="lhc.productChanged('<?php echo $optinsPanel['panelid']?>_products')" checklist-value="product.id"><i class="material-icons">&#xE8CC;</i>{{product.name}}</label></li>
				    <li ng-show="lhc.userProductNames.length > 0" class="border-bottom"></li>
                    <?php endif;?>
                       
                    <li ng-repeat="department in lhc.userDepartmentsGroups" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>_dpgroups" checklist-change="lhc.productChanged('<?php echo $optinsPanel['panelid']?>_dpgroups')" checklist-value="department.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Department group')?>" class="material-icons">&#xE84F;</i>{{department.name}}</label></li>
                    <li ng-show="lhc.userDepartmentsGroups.length > 0" class="border-bottom"></li>

					<li ng-repeat="department in lhc.userDepartments" data-stopPropagation="true" ng-hide="( (lhc.<?php echo $optinsPanel['panelid']?>_only_explicit_online == true && department.oexp == false) || (lhc.<?php echo $optinsPanel['panelid']?>_hide_hidden == true && department.hidden == true) || (lhc.<?php echo $optinsPanel['panelid']?>_hide_disabled == true && department.disabled == true) || (lhc.<?php echo $optinsPanel['panelid']?>_only_online == true && department.ogen == false))"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>" checklist-change="lhc.departmentChanged('<?php echo $optinsPanel['panelid']?>')" checklist-value="department.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Department')?>" class="material-icons">home</i>{{department.name}}</label></li>
				</ul>
			</div>
		</div>
		<?php if (isset($optinsPanel['userid'])) : ?>
		<div class="col-4 pr-0">
            <div class="btn-group btn-block btn-block-department">
                <button type="button" class="btn btn-light btn-block btn-sm dropdown-toggle btn-department-dropdown" data-toggle="dropdown" aria-expanded="false">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Users')?>
                </button>
                <ul class="dropdown-menu dropdown-lhc" role="menu">
                    <li ng-repeat="userGroup in lhc.userGroups" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>_ugroups" checklist-change="lhc.productChanged('<?php echo $optinsPanel['panelid']?>_ugroups')" checklist-value="userGroup.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','User group')?>" class="material-icons">people</i>{{userGroup.name}}</label></li>
                    <li ng-show="lhc.userGroups.length > 0" class="border-bottom"></li>
                    <li ng-repeat="userItem in lhc.userList" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['userid']?>" checklist-change="lhc.productChanged('<?php echo $optinsPanel['userid']?>')" checklist-value="userItem.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','User')?>" class="material-icons">account_box</i>{{userItem.name_official}}</label></li>
                </ul>
            </div>
		</div>
		<?php endif; ?>
		<div class="col-2">
			<select class="form-control form-control-sm btn-light" ng-model="lhc.<?php echo $optinsPanel['limitid']?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Number of elements in list');?>">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
		</div>
	</div>
</div>