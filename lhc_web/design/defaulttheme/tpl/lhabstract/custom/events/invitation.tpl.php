<div class="form-group">		
<label><?php echo $fields['name']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

<div class="form-group">
<label><?php echo erLhcoreClassAbstract::renderInput('disabled', $fields['disabled'], $object)?> <?php echo $fields['disabled']['trans'];?></label>
</div>

<?php $translatableItem = array('identifier' => 'operator_name'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">		
<label><?php echo $fields['position']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('position', $fields['position'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['siteaccess']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('siteaccess', $fields['siteaccess'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['time_on_site']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('time_on_site', $fields['time_on_site'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['delay']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('delay', $fields['delay'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['delay_init']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('delay_init', $fields['delay_init'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['pageviews']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('pageviews', $fields['pageviews'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['referrer']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('referrer', $fields['referrer'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['hide_after_ntimes']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('hide_after_ntimes', $fields['hide_after_ntimes'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['requires_email']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('requires_email', $fields['requires_email'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['requires_username']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('requires_username', $fields['requires_username'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['requires_phone']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('requires_phone', $fields['requires_phone'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['show_on_mobile']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('show_on_mobile', $fields['show_on_mobile'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['show_everytime']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('show_everytime', $fields['show_everytime'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['show_random_operator']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('show_random_operator', $fields['show_random_operator'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['operator_ids']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('operator_ids', $fields['operator_ids'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['identifier']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('identifier', $fields['identifier'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['tag']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('tag', $fields['tag'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['dep_id']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('dep_id', $fields['dep_id'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['campaign_id']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('campaign_id', $fields['campaign_id'], $object)?>
</div>

<?php $translatableItem = array('identifier' => 'message'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<?php $translatableItem = array('identifier' => 'message_returning'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<?php $translatableItem = array('identifier' => 'message_returning_nick'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">
<label><?php echo $fields['autoresponder_id']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('autoresponder_id', $fields['autoresponder_id'], $object)?>
</div>