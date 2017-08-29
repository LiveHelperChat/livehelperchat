<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $fields = $object->getFields();?>
<div role="tabpanel">
    	<!-- Nav tabs -->
    	<ul class="nav nav-tabs" role="tablist">
    		<li role="presentation" class="active"><a href="#invitation" aria-controls="invitation" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation');?></a></li>
    		<li role="presentation"><a href="#dynamic" aria-controls="dynamic" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Dynamic');?></a></li>
    		<li role="presentation"><a href="#events" aria-controls="events" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Events');?></a></li>
    	</ul>
    
    	<!-- Tab panes -->
    	<div class="tab-content">
    		<div role="tabpanel" class="tab-pane active" id="invitation">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/invitation.tpl.php'));?>
    		</div>
    		<div role="tabpanel" class="tab-pane" id="events">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/events.tpl.php'));?>
    		</div>
    		<div role="tabpanel" class="tab-pane" id="dynamic">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/dynamic.tpl.php'));?>
    		</div>
		</div>
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-default" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
	<input type="submit" class="btn btn-default" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
	<input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhabstract/parts/after_form.tpl.php'));?>