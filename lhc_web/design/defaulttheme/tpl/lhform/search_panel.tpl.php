<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" ng-non-bindable autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row mb-2">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown(array(
                    'input_name' => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Choose department'),
                    'selected_id' => $input->department_ids,
                    'css_class' => 'form-control',
                    'display_name' => 'name',
                    'ajax' => 'deps',
                    'list_function_params' => array_merge(array('sort' => '`name` ASC', 'limit' => 50), erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function' => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Chat operator');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown(array(
                    'input_name' => 'user_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Choose operator'),
                    'selected_id' => $input->user_ids,
                    'css_class' => 'form-control',
                    'display_name' => 'name_official',
                    'ajax' => 'users',
                    'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(), array('sort' => '`name` ASC', 'limit' => 50)),
                    'list_function' => 'erLhcoreClassModelUser::getUserList'
                )); ?>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Date from');?></label>
                <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" value="<?php echo htmlspecialchars((string)$input->timefrom)?>" placeholder="<?php echo date('Y-m-d', time() - 7 * 24 * 3600)?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-4">
                        <select name="timefrom_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="timefrom_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="timefrom_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_seconds) && $input->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Date to');?></label>
                <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" value="<?php echo htmlspecialchars((string)$input->timeto)?>" placeholder="<?php echo date('Y-m-d')?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-4">
                        <select name="timeto_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="timeto_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="timeto_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 d-flex align-items-end">
            <div class="btn-group me-2">
                <button type="submit" class="btn btn-primary btn-sm" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Search');?></button>
                <a href="<?php echo erLhcoreClassDesign::baseurl('form/downloadcollected')?>/<?php echo $form->id?>" class="btn btn-outline-secondary btn-sm"><span class="material-icons">file_download</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download XLS');?></a>
            </div>
        </div>
    </div>

</form>

<script>
$(function() {
    $('#id_timefrom,#id_timeto').fdatepicker({
        format: 'yyyy-mm-dd'
    });
    $('.btn-block-department').makeDropdown();
});
</script>
