<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Department');?></label>
    <?php
    $params = array (
        'input_name'     => 'dep_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control form-control-sm',
        'selected_id'    => $item->dep_id,
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params'  => array('limit' => '1000000'),
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose')
    );
    echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To mailbox');?></label>
    <br>
    <?php echo erLhcoreClassRenderHelper::renderCheckbox( array (
        'input_name'     => 'mailbox_ids[]',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select mail'),
        'selected_id'    => $item->mailbox_ids,
        'css_class'      => 'form-control',
        'display_name'   => 'mail',
        'list_function_params' => [],
        'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
    )); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From mail');?></label>
    <textarea class="form-control form-control-sm" name="from_mail" placeholder="example1@example.org,example2@example.org"><?php echo htmlspecialchars($item->from_mail)?></textarea>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From name');?></label>
            <textarea class="form-control form-control-sm" name="from_name" placeholder="Live Helper Chat"><?php echo htmlspecialchars($item->from_name)?></textarea>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Subject contains');?></label>
            <textarea class="form-control form-control-sm" name="subject_contains" placeholder="Live Helper Chat"><?php echo htmlspecialchars($item->subject_contains)?></textarea>
        </div>
    </div>
    <div class="col-12">
        <p><i><small>Every possible combination should start from a new line.<br>E.g fish,car && price{2}$ - fish or car word plus price can have two typos.</small></i></small></i></p>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority of matching rule. Rules with higher priority will be checked first.');?></label>
            <input type="text" class="form-control form-control-sm" name="priority_rule" value="<?php echo htmlspecialchars($item->priority_rule)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority conversation should get');?></label>
            <input type="text" class="form-control form-control-sm" name="priority" value="<?php echo htmlspecialchars($item->priority)?>" />
        </div>
    </div>
</div>

<div class="form-group">
    <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Active');?></label>
</div>