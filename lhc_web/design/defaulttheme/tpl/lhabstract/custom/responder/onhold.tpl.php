<div class="form-group">
    <label><?php echo $fields['wait_timeout_hold']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('wait_timeout_hold', $fields['wait_timeout_hold'], $object)?>
</div>

<?php for ($i = 1; $i <= 5; $i++) : ?>
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                <label><?php echo $fields['wait_timeout_hold_' . $i]['trans'];?></label>
                <?php echo erLhcoreClassAbstract::renderInput('wait_timeout_hold_' . $i, $fields['wait_timeout_hold_' . $i], $object)?>
            </div>
        </div>
        <div class="col-xs-9">
            <div class="form-group">
                <label><?php echo $fields['timeout_hold_message_' . $i]['trans'];?></label>
                <?php echo erLhcoreClassAbstract::renderInput('timeout_hold_message_' . $i, $fields['timeout_hold_message_' . $i], $object)?>
            </div>
        </div>
    </div>
<?php endfor;?>