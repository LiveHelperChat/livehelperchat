<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $fields = $object->getFields();?>
<div role="tabpanel">
    	<!-- Nav tabs -->
    	<ul class="nav nav-tabs mb-2" role="tablist">
    		<li role="presentation" class="nav-item"><a class="active nav-link" href="#invitation" aria-controls="invitation" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation');?></a></li>
    		<li role="presentation" class="nav-item"><a class="nav-link" href="#bot" aria-controls="bot" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Bot');?></a></li>
    		<li role="presentation" class="nav-item"><a class="nav-link" href="#dynamic" aria-controls="dynamic" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Dynamic');?></a></li>
    		<li role="presentation" class="nav-item"><a class="nav-link" href="#events" aria-controls="events" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Events');?></a></li>
    		<li role="presentation" class="nav-item"><a class="nav-link" href="#design" aria-controls="design" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Design');?></a></li>
    		<li role="presentation" class="nav-item"><a class="nav-link" href="#injecthtml" aria-controls="design" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Inject HTML');?></a></li>
    	</ul>
    
    	<!-- Tab panes -->
    	<div class="tab-content">
    		<div role="tabpanel" class="tab-pane active" id="invitation">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/invitation.tpl.php'));?>
    		</div>
    		<div role="tabpanel" class="tab-pane" id="bot">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/bot.tpl.php'));?>
    		</div>
            <div role="tabpanel" class="tab-pane" id="events">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/events.tpl.php'));?>
    		</div>
    		<div role="tabpanel" class="tab-pane" id="dynamic">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/dynamic.tpl.php'));?>
    		</div>
            <div role="tabpanel" class="tab-pane" id="design">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/design.tpl.php'));?>
    		</div>
            <div role="tabpanel" class="tab-pane" id="injecthtml">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/injecthtml.tpl.php'));?>
    		</div>
		</div>
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
	<input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
	<input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhabstract/parts/after_form.tpl.php'));?>