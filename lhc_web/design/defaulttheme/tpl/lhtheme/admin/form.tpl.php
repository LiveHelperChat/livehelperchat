<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<div role="tabpanel" ng-controller="IClickToCallFormGenerator as cform"  ng-init='<?php if ($form->static_content != '') : ?>cform.staticResources = <?php echo $form->static_content?>;<?php endif;?><?php if ($form->static_js_content != '') : ?>cform.staticJSResources = <?php echo $form->static_js_content?>;<?php endif;?><?php if ($form->static_css_content != '') : ?>cform.staticCSSResources = <?php echo $form->static_css_content?>;<?php endif;?>'>

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Settings');?></a></li>
		<li role="presentation"><a href="#headersettings" aria-controls="headersettings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Header settings');?></a></li>
		<li role="presentation"><a href="#headercss" aria-controls="headercss" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Header css');?></a></li>

		<?php if ($form->id !== null) : ?>
		<li role="presentation"><a href="#static" aria-controls="static" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Static content');?></a></li>
		<li role="presentation"><a href="#js" aria-controls="js" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','JS');?></a></li>
		<li role="presentation"><a href="#css" aria-controls="css" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','CSS');?></a></li>
		<?php endif; ?>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="settings">
			<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Name');?>*</label> 
				<input type="text" class="form-control" name="Name" value="<?php echo htmlspecialchars($form->name) ?>" />
			</div>				
		</div>

		<div role="tabpanel" class="tab-pane" id="headersettings">
            <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Header content');?></label>
				<textarea ng-non-bindable name="header_content" class="form-control" rows="10" cols=""><?php echo htmlspecialchars($form->header_content) ?></textarea>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="headercss">
            <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('icclicktocallform/form','Header css');?></label>
				<textarea ng-non-bindable name="header_css" class="form-control" rows="10" cols=""><?php echo htmlspecialchars($form->header_css) ?></textarea>
			</div>
		</div>

		<?php 
		// Visible only if form is stored
		if ($form->id !== null) : ?>
		<div role="tabpanel" class="tab-pane" id="static">
            <?php $paramsResourceAdd = array(
                    'scope' => 'static_content',
                    'add_function' => 'addStaticResource'
                );
            ?>
			<?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/resource_add.tpl.php'));?>
			<?php $paramsResourceRepeat = array(
                    'attr' => 'staticResources',
                    'scope' => 'static_content',
                    'delete' => 'deleteStaticResource'
                );
            ?>
			<?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/resource_repeat.tpl.php'));?>
		</div>
		
		<div role="tabpanel" class="tab-pane" id="js">
		    <?php $paramsResourceAdd = array(
                    'scope' => 'static_js_content',
                    'add_function' => 'addStaticJSResource'
                );
            ?>
			<?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/resource_add.tpl.php'));?>
			<?php $paramsResourceRepeat = array(
                    'attr' => 'staticJSResources',
                    'scope' => 'static_js_content',
                    'delete' => 'deleteStaticJSResource'
                );
            ?>
			<?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/resource_repeat.tpl.php'));?>
		</div>
		
		<div role="tabpanel" class="tab-pane" id="css">
		    <?php $paramsResourceAdd = array(
                    'scope' => 'static_css_content',
                    'add_function' => 'addStaticCSSResource'
                );
            ?>
			<?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/resource_add.tpl.php'));?>
			<?php $paramsResourceRepeat = array(
                    'attr' => 'staticCSSResources',
                    'scope' => 'static_css_content',
                    'delete' => 'deleteStaticCSSResource'
                );
            ?>
			<?php include(erLhcoreClassDesign::designtpl('lhtheme/admin/resource_repeat.tpl.php'));?>
		</div>
		<?php endif;?>
	</div>
</div>