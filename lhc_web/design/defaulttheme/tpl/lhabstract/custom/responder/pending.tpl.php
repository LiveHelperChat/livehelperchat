<div class="form-group">		
<label><?php echo erLhcoreClassAbstract::renderInput('ignore_pa_chat', $fields['ignore_pa_chat'], $object)?> <?php echo $fields['ignore_pa_chat']['trans'];?></label>
</div>

<div class="form-group">		
<label><?php echo $fields['repeat_number']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('repeat_number', $fields['repeat_number'], $object)?>
</div>

<div class="row">
    <div class="col-xs-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout']['trans'];?> [1]</label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout', $fields['wait_timeout'], $object)?>
        </div>
    </div>
    <div class="col-xs-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_message']['trans'];?> [1]</label>
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message', $fields['timeout_message'], $object)?>
        </div>
    </div>
</div>

<?php for ($i = 2; $i <= 5; $i++) : ?>
<div class="row">
    <div class="col-xs-3">
        <div class="form-group">		
        <label><?php echo $fields['wait_timeout_' . $i]['trans'];?> [<?php echo $i?>]</label>
        <?php echo erLhcoreClassAbstract::renderInput('wait_timeout_' . $i, $fields['wait_timeout_' . $i], $object)?>
        </div>
    </div>
    <div class="col-xs-9">
        <div class="form-group">		
        <label><?php echo $fields['timeout_message_' . $i]['trans'];?> [<?php echo $i?>]</label>
        <?php echo erLhcoreClassAbstract::renderInput('timeout_message_' . $i, $fields['timeout_message_' . $i], $object)?>
        </div>
    </div>
</div>
<?php endfor;?>