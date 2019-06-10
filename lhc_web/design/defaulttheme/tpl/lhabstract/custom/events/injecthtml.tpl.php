<div class="row">

    <div class="col-12">
        <div class="form-group">
            <label><?php echo $fields['inject_html']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('inject_html', $fields['inject_html'], $object)?>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('inject_only_html', $fields['inject_only_html'], $object)?> <?php echo $fields['inject_only_html']['trans'];?></label>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label><?php echo erLhcoreClassAbstract::renderInput('dynamic_everytime', $fields['dynamic_everytime'], $object)?> <?php echo $fields['dynamic_everytime']['trans'];?></label>
        </div>
    </div>

</div>