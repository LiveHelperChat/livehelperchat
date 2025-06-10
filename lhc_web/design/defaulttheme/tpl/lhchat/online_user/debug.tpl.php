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
        <button id="test-visitor-invitation" type="button" class="btn btn-xs btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Test')?></button>
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
</script>

<hr />
<pre class="fs11"><?php 
$state = $online_user->getState();
if (isset($state['online_attr']) && !empty($state['online_attr'])) {
    $decoded = json_decode($state['online_attr'], true);
    if ($decoded !== null) {
        $state['online_attr'] = $decoded;
    }
}
if (isset($state['online_attr_system']) && !empty($state['online_attr_system'])) {
    $decoded = json_decode($state['online_attr_system'], true);
    if ($decoded !== null) {
        $state['online_attr_system'] = $decoded;
    }
}
echo htmlspecialchars(json_encode($state, JSON_PRETTY_PRINT)); 
?></pre>