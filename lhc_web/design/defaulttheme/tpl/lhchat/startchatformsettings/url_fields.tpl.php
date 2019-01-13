<p>If you page has custom argumetns in URL you can extract them here</p>

<div ng-controller="StartChatFormURLCtrl as startChatURL" <?php if (isset($start_chat_data['custom_fields_url']) && $start_chat_data['custom_fields_url'] != '') : ?> ng-init='startChatURL.startchatfields = <?php echo $start_chat_data['custom_fields_url']?>'<?php endif;?>>

<div class="row">
    <div class="col-3">
        <div class="form-group">
            <label>Argument identifier</label>
            <input type="text" ng-model="startChatURL.fieldidentifier" placeholder="id" value="" class="form-control" />
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <label>Argument name</label>
            <input type="text" name="" ng-model="startChatURL.fieldname" placeholder="ID" value="" class="form-control" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <input type="button" class="btn btn-secondary col-12" ng-click="startChatURL.addField()" value="Add a field">
    </div>
</div>

<hr>

<div class="row">
    <div ng-repeat="field in startChatURL.startchatfields" class="col-6">
        <div class="form-group">
            <div class="btn-group float-right" role="group" aria-label="...">
                <button ng-if="$index > 0" type="button" class="btn btn-secondary btn-xs" ng-click="startChatURL.moveLeftField(field)">&laquo; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','left')?></button>
                <button ng-if="$index < startChatURL.startchatfields.length-1" type="button" class="btn btn-secondary btn-xs" ng-click="startChatURL.moveRightField(field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','right')?> &raquo;</button>
            </div>

            <button type="button" class="btn btn-danger btn-xs" ng-click="startChatURL.deleteField(field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove')?></button>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Argument identifier')?></label>
                    <input ng-model="field.fieldidentifier" class="form-control" ng-model="" type="text" name="customFieldURLIdentifier[]" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Argument name')?></label>
                    <input ng-model="field.fieldname" class="form-control" ng-model="" type="text" name="customFieldURLName[]" value="" />
                </div>
            </div>
        </div>

    </div>
</div>

</div>