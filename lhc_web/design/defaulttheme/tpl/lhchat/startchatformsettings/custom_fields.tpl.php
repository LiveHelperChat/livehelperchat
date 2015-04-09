<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Custom fields generator, these fields will be visible in start chat forms')?></p>

<div ng-controller="StartChatFormCtrl as startChat" <?php if (isset($start_chat_data['custom_fields']) && $start_chat_data['custom_fields'] != '') : ?> ng-init='startChat.startchatfields = <?php echo $start_chat_data['custom_fields']?>'<?php endif;?>>

	<div class="row">
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Field label')?></label> <input ng-model="startChat.fieldname" class="form-control" ng-model="" type="text" name="" value="" />
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Default value')?></label> <input ng-model="startChat.defaultvalue" class="form-control" type="text" name="" value="" />
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Type')?></label> <select ng-model="startChat.fieldtype" class="form-control">
					<option value="text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Text')?></option>
					<option value="hidden"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Hidden')?></option>
				</select>
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Visible on')?></label> <select ng-model="startChat.visibility" class="form-control">
					<option value="all"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Offline and online form')?></option>
					<option value="off"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Only offline')?></option>
					<option value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Only online')?></option>
				</select>
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Size (between 1 and 12)')?></label> <input ng-model="startChat.size" class="form-control" type="text" name="" value="6" />
			</div>
		</div>
		<div class="col-xs-3">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Is required')?> <input ng-model="startChat.isrequired" type="checkbox" name="isRequired" /></label>
		</div>

		<div class="col-xs-6">
			<input type="button" class="btn btn-default col-xs-12" ng-click="startChat.addField()" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Add a field')?>" />
		</div>
	</div>
	<hr>

	<div class="row">
		<div ng-repeat="field in startChat.startchatfields" class="col-xs-{{field.size}}">
			<div class="form-group">
				<div class="btn-group pull-right" role="group" aria-label="...">
					<button ng-if="$index > 0" type="button" class="btn btn-default btn-xs" ng-click="startChat.moveLeftField(field)">&laquo; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','left')?></button>
					<button ng-if="$index < startChat.startchatfields.length-1" type="button" class="btn btn-default btn-xs" ng-click="startChat.moveRightField(field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','right')?> &raquo;</button>
				</div>
				<label>{{field.fieldname}}{{field.isrequired ? '*' : ''}}</label> <input class="form-control" type="text" value="{{field.defaultvalue}}" readonly="readonly">
				<ul class="list-unstyled pt10">
					<li ng-if="field.visibility == 'off'"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Visible only in offline form')?></li>
					<li ng-if="field.visibility == 'on'"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Visible only in online form')?></li>
					<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Size')?> - {{field.size}}</li>
					<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Type')?> - {{field.fieldtype}}</li>
				</ul>
				<button type="button" class="btn btn-danger btn-xs" ng-click="startChat.deleteField(field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove')?></button>
			</div>
			<input type="hidden" name="customFieldLabel[]" value="{{field.fieldname}}" />
			<input type="hidden" name="customFieldType[]" value="{{field.fieldtype}}" />
			<input type="hidden" name="customFieldSize[]" value="{{field.size}}" />
			<input type="hidden" name="customFieldVisibility[]" value="{{field.visibility}}" />
			<input type="hidden" name="customFieldIsrequired[]" value="{{field.isrequired}}" />
			<input type="hidden" name="customFieldDefaultValue[]" value="{{field.defaultvalue}}" />
		</div>
	</div>

</div>