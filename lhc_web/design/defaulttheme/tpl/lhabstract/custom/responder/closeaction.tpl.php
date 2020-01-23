<div class="row">
    <div class="col-9">
        <div class="form-group">
            <label><?php echo $fields['close_message']['trans'];?></label>
            <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_close_message]'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
            <?php echo erLhcoreClassAbstract::renderInput('close_message', $fields['close_message'], $object)?>
        </div>
    </div>
</div>