<div class="row">
    <div class="col-12">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Proactive invitation to test against. Choose Any for auto select.');?></label>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Any'),
                'input_name'     => 'invitation_id',
                'css_class'      => 'form-control form-control-sm',
                'selected_id'    => 0,
                'list_function'  => 'erLhAbstractModelProactiveChatInvitation::getList',
                'list_function_params'  => ['limit' => false]
            )); ?>
        </div>
    </div>
    <div class="col-6 fs14">
        <input type="text" class="form-control form-control-sm" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Tag to test against. E.g. error_deposit')?>" id="invitation_tag" value="">
    </div>
    <div class="col-12">
        <div class="btn-group" role="group">
            <button id="test-visitor-invitation" type="button" class="btn btn-xs btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Test')?></button>
            <button id="reset-visitor-invitation" type="button" class="btn btn-xs btn-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Reset invitation data')?></button>
        </div>
    </div>
    <div class="col-12" id="debug-invitation-output">

    </div>
</div>

<script>
    $('#test-visitor-invitation').click(function(){
        $.get(WWW_DIR_JAVASCRIPT + "audit/debuginvitation/<?php echo $online_user->id ?>/" + $('#id_invitation_id').val() + '/' + $('#invitation_tag').val(), function(data) {
            $('#debug-invitation-output').html(data.data);
        });
    });
    $('#reset-visitor-invitation').click(function(){
        $.post(WWW_DIR_JAVASCRIPT + "audit/debuginvitation/<?php echo $online_user->id ?>/" + $('#id_invitation_id').val() + '/empty/(action)/resetinvitation', function(data) {
            $('#debug-invitation-output').html(data.data);
            $('#online-attr-debug').html(data.data_debug);
        });
    });
</script>

<div id="online-attr-debug">
<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/debug_data.tpl.php'));?>
</div>