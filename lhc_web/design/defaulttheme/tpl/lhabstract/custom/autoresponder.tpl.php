<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $fields = $object->getFields();?>
<div ng-controller="AutoResponderCtrl as cmsg"  ng-init='<?php if ($object->languages != '') : ?>cmsg.languages = <?php echo $object->languages?>;<?php endif;?>cmsg.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getList()))?>'>

<div class="form-group">
<label><?php echo $fields['name']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['siteaccess']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('siteaccess', $fields['siteaccess'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['position']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('position', $fields['position'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['dep_id']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('dep_id', $fields['dep_id'], $object)?>
</div>

<div class="row">
    <div class="col-xs-6">
        <div class="form-group">
            <label><?php echo $fields['mint_reset']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('mint_reset', $fields['mint_reset'], $object, 70)?>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="form-group">
            <label><?php echo $fields['maxt_reset']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('maxt_reset', $fields['maxt_reset'], $object, 120)?>
        </div>
    </div>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('dreset_survey', $fields['dreset_survey'], $object)?> <?php echo $fields['dreset_survey']['trans'];?></label>
</div>

<div class="form-group">		
<label><?php echo $fields['wait_message']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('wait_message', $fields['wait_message'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['operator']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('operator', $fields['operator'], $object)?>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('only_proactive', $fields['only_proactive'], $object)?> <?php echo $fields['only_proactive']['trans'];?></label>
</div>

<div role="tabpanel">
    	<!-- Nav tabs -->
    	<ul class="nav nav-tabs" role="tablist" id="autoresponder-tabs">
    		<li role="presentation" class="active"><a href="#pending" aria-controls="pending" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Pending chat messaging');?></a></li>
    		<li role="presentation"><a href="#active" aria-controls="active" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Not replying messaging');?></a></li>
    		<li role="presentation"><a href="#onhold" aria-controls="onhold" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','On-hold chat messaging');?></a></li>
    		<li role="presentation"><a href="#survey" aria-controls="survey" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Survey');?></a></li>
            <li ng-repeat="lang in cmsg.languages" role="presentation"><a href="#lang-{{$index}}" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i></a></li>
            <li><a href="#addlanguage" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Add translation');?></a></li>
    	</ul>
    
    	<!-- Tab panes -->
    	<div class="tab-content">
    		<div role="tabpanel" class="tab-pane active" id="pending">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/pending.tpl.php'));?>
    		</div>
    		<div role="tabpanel" class="tab-pane" id="active">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/active.tpl.php'));?>
    		</div>
            <div role="tabpanel" class="tab-pane" id="onhold">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/onhold.tpl.php'));?>
    		</div>
            <div role="tabpanel" class="tab-pane" id="survey">
    		  <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/survey.tpl.php'));?>
    		</div>

            <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/languages.tpl.php'));?>

		</div>
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-default" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
	<input type="submit" class="btn btn-default" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
	<input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>

</div>

<script>
$('select[name="AbstractInput_pending_bot_id"],select[name="AbstractInput_nreply_bot_id"],select[name="AbstractInput_onhold_bot_id"]').change(function(){
    var identifier = $(this).attr('name').replace(/AbstractInput_|_bot_id/g,"");
    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val() + '/0/(preview)/1/(element)/'+identifier+'_trigger_id', { }, function(data) {
        $('#'+identifier+'-trigger-list-id').html(data);
        renderPreview($('select[name="AbstractInput_'+identifier+'_trigger_id"]'));
    }).fail(function() {

    });
});

$.each([ {'id':'pending','val' : <?php echo (isset($object->bot_configuration_array['pending_trigger_id'])) ? $object->bot_configuration_array['pending_trigger_id'] : 0 ?>}, {'id':'nreply','val':<?php echo (isset($object->bot_configuration_array['nreply_trigger_id'])) ? $object->bot_configuration_array['nreply_trigger_id'] : 0 ?>}, {'id':'onhold','val': <?php echo (isset($object->bot_configuration_array['onhold_trigger_id'])) ? $object->bot_configuration_array['onhold_trigger_id'] : 0 ?>}], function( index, value ) {
    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="AbstractInput_'+value.id+'_bot_id"]').val() + '/'+value.val+'/(preview)/1/(element)/'+value.id+'_trigger_id', { }, function(data) {
        $('#' + value.id +'-trigger-list-id').html(data);
        if (parseInt(value.val) > 0){
            renderPreview($('select[name="AbstractInput_' + value.id +'_trigger_id"]'));
        }
    }).fail(function() {

    });
});

function renderPreview(inst) {

    var identifier = inst.attr('name').replace(/AbstractInput_|_trigger_id/g,"");

    $.get(WWW_DIR_JAVASCRIPT + 'theme/renderpreview/' + inst.val(), { }, function(data) {
        $('#'+identifier+'-trigger-preview-window').html(data);
    }).fail(function() {
        $('#'+identifier+'-trigger-preview-window').html('');
    });
}
</script>