<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mailbox');?></label>
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                'input_name'     => 'mailbox_id',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose mailbox'),
                'css_class'      => 'form-control form-control-sm',
                'selected_id'      => '',
                'display_name'   => function($item) {
                    return $item->mail.' | '.$item->name;
                },
                'list_function_params' => ['limit' => false, 'sort' => '`mail` ASC'],
                'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
            )); ?>
        </div>
    </div>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_title.tpl.php')); ?>
    <div class="col-6">
        <label><?php echo $userTitle['user'];?></label>
        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
            'input_name'     => 'user_id',
            'optional_field' => $userTitle['user_select'],
            'selected_id'    => '',
            'css_class'      => 'form-control form-control-sm',
            'display_name'   => function($item){
                return $item->name_official.' | '.$item->email;
            },
            'list_function_params' => ['limit' => false, 'sort' => '`name` ASC'],
            'list_function'  => 'erLhcoreClassModelUser::getUserList'
        )); ?>
    </div>
    <div class="col-12">
        <button type="button" id="add-combination" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Add');?></button>
    </div>
</div>
<hr>

<div class="row" id="operators-with-mailbox">
    <?php foreach ($item->mails_array as $mailboxId => $userId) : ?>
    <div id="mailbox-user-<?php echo $mailboxId?>" class="col-4 mb-4">
        <input type="hidden" name="mailbox_id[]" value="<?php echo $mailboxId?>">
        <input type="hidden" name="user_id[<?php echo $mailboxId?>]" value="<?php echo $userId?>">
        <div class="row pb-2">
            <div class="col-12"><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailbox');?>: </b><?php echo erLhcoreClassModelMailconvMailbox::fetch($mailboxId, true)->mail,' | ', erLhcoreClassModelMailconvMailbox::fetch($mailboxId, true)->name?></div>
            <div class="col-12"><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Operator');?>: </b><?php echo erLhcoreClassModelUser::fetch($userId, true)->name_official,' | ', erLhcoreClassModelUser::fetch($userId, true)->email?></div>
        </div>
        <button onclick="$('#mailbox-user-<?php echo $mailboxId?>').remove()" type="button" class="btn btn-xs btn-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Delete');?></button>
    </div>
    <?php endforeach; ?>
</div>

<hr class="mt-0">

<div class="form-group">
    <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmr','Active');?></label>
</div>

<script>
    document.getElementById('add-combination').addEventListener('click',function(){
        if (parseInt($('#id_mailbox_id').val()) > 0 && parseInt($('#id_user_id').val()) > 0) {

            if ($('#mailbox-user-'+$('#id_mailbox_id').val()).length > 0) {
                alert('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','This mailbox was already added!');?>');
                return;
            }

            $('#operators-with-mailbox').prepend('<div id="mailbox-user-'+$('#id_mailbox_id').val()+'" class="col-4 mb-4">' +
                '<input type="hidden" name="mailbox_id[]" value="'+$('#id_mailbox_id').val()+'">' +
                '<input type="hidden" name="user_id['+$('#id_mailbox_id').val()+']" value="'+$('#id_user_id').val()+'">' +
                '<div class="row pb-2">' +
                '<div class="col-12"><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailbox');?>: </b>'+$('#id_mailbox_id option:selected').text()+'</div>' +
                '<div class="col-12"><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Operator');?>: </b>'+$('#id_user_id option:selected').text()+'</div>' +
                '</div> <button onclick="$(\'#mailbox-user-'+$('#id_mailbox_id').val()+'\').remove()" type="button" class="btn btn-xs btn-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Delete');?></button> ' +
                '</div>');
        } else {
            alert('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Please choose mailbox and user!');?>');
        }
    });
</script>