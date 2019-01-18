<?php /* list without files */ ?>
<div class="row">
	<div ng-repeat="field in cform.<?php echo $paramsResourceRepeat['attr']?>" class="col-xs-12" ng-if="!field.file">
		<div class="form-group">
			<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Resource name for replacement');?></label>
			<div class="row">
				<div class="col-xs-6">
					<input class="form-control input-sm" type="text" ng-model="field.name" value="" name="<?php echo $paramsResourceRepeat['scope']?>_name[{{field.hash}}]" />
				</div>
				<div class="col-xs-6">
					<input type="file" class="form-control input-sm" name="<?php echo $paramsResourceRepeat['scope']?>_file_{{field.hash}}" />
				</div>
			</div>
		</div>
		
		<div ng-if="field.file">
		   <a target="_blank" href="{{field.file_dir != '' ? '<?php echo erLhcoreClassSystem::instance()->wwwDir()?>' : '<?php echo erLhcoreClassSystem::instance()->wwwImagesDir()?>'}}/{{field.file_dir}}{{field.file}}">{{field.file}}</a>
		</div>
		
		<button type="button" class="btn btn-danger btn-sm" ng-click="cform.<?php echo $paramsResourceRepeat['delete']?>('<?php echo $form->id?>',field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove')?></button>
		<input type="hidden" name="<?php echo $paramsResourceRepeat['scope']?>_hash[{{field.hash}}]" value="{{field.hash}}" />					
		<hr class="mt5 mb5" style="margin-top: 5px">
	</div>
</div>

<?php /* list with files */ ?>
<div class="row">
	<div ng-repeat="field in cform.<?php echo $paramsResourceRepeat['attr']?>" class="col-xs-12" ng-if="field.file">
		<div class="form-group">
			<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Resource name for replacement');?></label>
			<div class="row">
				<div class="col-xs-6">
					<input class="form-control input-sm" type="text" ng-model="field.name" value="" name="<?php echo $paramsResourceRepeat['scope']?>_name[{{field.hash}}]" />
				</div>
				<div class="col-xs-6">
					<input type="file" class="form-control input-sm" name="<?php echo $paramsResourceRepeat['scope']?>_file_{{field.hash}}" />
				</div>
			</div>
		</div>
		
		<div ng-if="field.file">
		   <a target="_blank" href="{{field.file_dir != '' ? '<?php echo erLhcoreClassSystem::instance()->wwwDir()?>' : '<?php echo erLhcoreClassSystem::instance()->wwwImagesDir()?>'}}/{{field.file_dir}}{{field.file}}">{{field.file}}</a>
		</div>
		
		<button type="button" class="btn btn-danger btn-sm" ng-click="cform.<?php echo $paramsResourceRepeat['delete']?>('<?php echo $form->id?>',field)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Remove')?></button>
		<input type="hidden" name="<?php echo $paramsResourceRepeat['scope']?>_hash[{{field.hash}}]" value="{{field.hash}}" />					
		<hr class="mt5 mb5" style="margin-top: 5px">
	</div>
</div>