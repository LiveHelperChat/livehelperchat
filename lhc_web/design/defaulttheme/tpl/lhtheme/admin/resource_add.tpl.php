<div class="row">
	<div class="col-xs-6">
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Resource name for replacement');?></label>
			<div class="row">
				<div class="col-xs-6">
					<input type="text" class="form-control" ng-model="cform.<?php echo $paramsResourceAdd['scope']?>_name" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Please enter resource identifier')?>" value="" />
				</div>
				<div class="col-xs-6">
					<input type="button" class="btn btn-default col-xs-12" ng-click="cform.<?php echo $paramsResourceAdd['add_function']?>()" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Add resource')?>" />
				</div>
			</div>
		</div>
	</div>
</div>