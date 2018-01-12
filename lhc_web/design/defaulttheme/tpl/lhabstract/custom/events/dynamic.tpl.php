<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('dynamic_invitation', $fields['dynamic_invitation'], $object)?><?php echo $fields['dynamic_invitation']['trans'];?></label>
</div>

<div class="form-group">
<label><?php echo erLhcoreClassAbstract::renderInput('show_instant', $fields['show_instant'], $object)?><?php echo $fields['show_instant']['trans'];?></label>
</div>

<div class="form-group">		
<label><?php echo $fields['event_type']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('event_type', $fields['event_type'], $object)?>
</div>

<div class="form-group">		
<label><?php echo $fields['iddle_for']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('iddle_for', $fields['iddle_for'], $object)?>
</div>