<div class="pl10 pr10 pt10">
	<div class="row">
		<div class="col-xs-<?php isset($optinsPanel['userid']) ? print 6 : print 10?>">
			<div class="btn-group btn-block btn-block-department">
				<button type="button" class="btn btn-default btn-block btn-sm dropdown-toggle btn-department-dropdown" data-toggle="dropdown" aria-expanded="false">
					{{lhc.<?php echo $optinsPanel['panelid']?>.length == 0 ? "<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','All departments');?>" : lhc.<?php echo $optinsPanel['panelid']?>Names.join(", ")}} <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_all_departments"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Check all')?></label></li>
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_only_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only online')?></label></li>
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_only_explicit_online"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Only explicit online')?></label></li>
				    <li><label><input type="checkbox" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_hidden"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide hidden')?></label></li>
				    <li class="border-bottom-grey"><label><input data-stopPropagation="true" ng-change="lhc.allDepartmentsChanged('<?php echo $optinsPanel['panelid']?>',true)" type="checkbox" ng-model="lhc.<?php echo $optinsPanel['panelid']?>_hide_disabled"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide disabled')?></label></li>

				    <?php if (!isset($optinsPanel['disable_product']) || $optinsPanel['disable_product'] == false) : ?>
				    <li ng-repeat="product in lhc.userProductNames" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>_products" checklist-change="lhc.productChanged('<?php echo $optinsPanel['panelid']?>_products')" checklist-value="product.id"><i class="material-icons">&#xE8CC;</i>{{product.name}}</label></li>
				    <li ng-show="lhc.userProductNames.length > 0" class="border-bottom-grey"></li>
                    <?php endif;?>
                                        
					<li ng-repeat="department in lhc.userDepartments" data-stopPropagation="true" ng-hide="( (lhc.<?php echo $optinsPanel['panelid']?>_only_explicit_online == true && department.oexp == false) || (lhc.<?php echo $optinsPanel['panelid']?>_hide_hidden == true && department.hidden == true) || (lhc.<?php echo $optinsPanel['panelid']?>_hide_disabled == true && department.disabled == true) || (lhc.<?php echo $optinsPanel['panelid']?>_only_online == true && department.ogen == false))"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>" checklist-change="lhc.departmentChanged('<?php echo $optinsPanel['panelid']?>')" checklist-value="department.id"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Department')?>" class="material-icons">home</i>{{department.name}}</label></li>
				</ul>
			</div>
		</div>
		<?php if (isset($optinsPanel['userid'])) : ?>
		<div class="col-xs-4">
				
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'user_id_' . $optinsPanel['userid'],
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select operator'),
	                    'selected_id'    => 0,
						'ng-model' => "lhc." . $optinsPanel['userid'],
			            'css_class'      => 'form-control input-sm',
						'display_name' => 'name_official',
	                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
	            )); ?>
	            
		</div>
		<?php endif; ?>
		<div class="col-xs-2">
			<select class="form-control input-sm" ng-model="lhc.<?php echo $optinsPanel['limitid']?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Number of elements in list');?>">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
		</div>
	</div>
</div>