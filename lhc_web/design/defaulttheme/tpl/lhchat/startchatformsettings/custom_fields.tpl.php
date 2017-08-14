<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Custom fields generator, these fields will be visible in start chat forms')?></p>

<div ng-controller="StartChatFormCtrl as startChat" <?php if (isset($start_chat_data['custom_fields']) && $start_chat_data['custom_fields'] != '') : ?> ng-init='startChat.startchatfields = <?php echo $start_chat_data['custom_fields']?>'<?php endif;?>>

	<div class="row">
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Field label')?></label>
                <input ng-model="startChat.fieldname" class="form-control" ng-model="" type="text" name="" value="" />
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Default value')?></label>
                <input ng-model="startChat.defaultvalue" class="form-control" type="text" name="" value="" />
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Type')?></label> <select ng-model="startChat.fieldtype" class="form-control">
					<option value="text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Text')?></option>
					<option value="hidden"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Hidden')?></option>
					<option value="dropdown"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Dropdown')?></option>
				</select>
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Visible on')?></label>
                <select ng-model="startChat.visibility" class="form-control">
					<option value="all"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Offline and online form')?></option>
					<option value="off"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Only offline')?></option>
					<option value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Only online')?></option>
				</select>
			</div>
		</div>

        <div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Show if')?></label>
                <select ng-model="startChat.showcondition" class="form-control">
					<option value="always"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Always')?></option>
					<option value="uempty"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Username is empty')?></option>
				</select>
			</div>
		</div>

		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Size (between 1 and 12)')?></label> <input ng-model="startChat.size" class="form-control" type="text" name="" value="6" />
			</div>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Field identifier')?></label> <input ng-model="startChat.fieldidentifier" class="form-control" ng-model="" type="text" name="" value="" />
			</div>
		</div>
		<div class="col-xs-3">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Is required')?> <input ng-model="startChat.isrequired" type="checkbox" name="isRequired" /></label>
		</div>
        <div class="col-xs-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Options')?></label>
                <textarea class="form-control" ng-model="startChat.options" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Each option in new line')?>"></textarea>
            </div>
        </div>
		<div class="col-xs-3">
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

                <button type="button" class="btn btn-danger btn-xs" ng-click="startChat.deleteField(field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove')?></button>
			</div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Field label')?></label>
                        <input ng-model="field.fieldname" class="form-control" ng-model="" type="text" name="customFieldLabel[]" value="" />
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Type')?></label>
                        <select ng-model="field.fieldtype" class="form-control" name="customFieldType[]">
                            <option value="text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Text')?></option>
                            <option value="hidden"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Hidden')?></option>
                            <option value="dropdown"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Dropdown')?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Field identifier')?></label>
                        <input ng-model="field.fieldidentifier" class="form-control" ng-model="" type="text" name="customFieldIdentifier[]" value="" />
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Size (between 1 and 12)')?></label>
                        <input ng-model="field.size" class="form-control" type="text" name="customFieldSize[]" value="6" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Visible on')?></label>
                        <select ng-model="field.visibility" name="customFieldVisibility[]" class="form-control">
                            <option value="all"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Offline and online form')?></option>
                            <option value="off"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Only offline')?></option>
                            <option value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Only online')?></option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-6">
                    <label><input ng-model="field.isrequired" type="checkbox" name="customFieldIsrequired[]" value="true" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Is required')?></label>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Default value')?></label>
                        <input ng-model="field.defaultvalue" class="form-control" type="text" name="customFieldDefaultValue[]" value="" />
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Show if')?></label>
                        <select name="customFieldCondition[]" ng-model="field.showcondition" class="form-control">
                            <option value="always"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Always')?></option>
                            <option value="uempty"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Username is empty')?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Options')?></label>
                <textarea name="customFieldOptions[]" class="form-control" ng-model="field.options" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Each option in new line')?>"></textarea>
            </div>



		</div>
	</div>

</div>