<div class="pl10 pr10 pt10">
	<div class="row">
		<div class="col-xs-9">
			<div class="btn-group btn-block btn-block-department">
				<button type="button" class="btn btn-default btn-block btn-sm dropdown-toggle btn-department-dropdown" data-toggle="dropdown" aria-expanded="false">
					{{lhc.<?php echo $optinsPanel['panelid']?>.length == 0 ? "<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','All departments');?>" : lhc.<?php echo $optinsPanel['panelid']?>Names.join(", ")}} <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li ng-repeat="department in lhc.userDepartments" data-stopPropagation="true"><label><input type="checkbox" checklist-model="lhc.<?php echo $optinsPanel['panelid']?>" checklist-change="lhc.departmentChanged('<?php echo $optinsPanel['panelid']?>')" checklist-value="department.id"> {{department.name}}</label></li>
				</ul>
			</div>
		</div>
		<div class="col-xs-3">
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