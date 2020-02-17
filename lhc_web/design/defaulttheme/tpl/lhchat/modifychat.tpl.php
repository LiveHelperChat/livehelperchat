<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($chat_updated) && $chat_updated == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Chat information was updated'); ?>
<script>
parent.lhinst.reloadTab('<?php echo $chat->id?>',parent.$('#tabs'),'<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>');
setTimeout(function() {
	parent.$('#myModal').modal('hide');
    parent.$('#CSChatMessage-<?php echo $chat->id?>').focus();
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<ul class="nav nav-pills" role="tablist">
    <li role="presentation" class="nav-item"><a class="active nav-link" href="#mainchatmodify" aria-controls="mainchatmodify" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','User attribute');?></a></li>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','modifychatcore')) : ?>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#mainchatcore" aria-controls="mainchatcore" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Chat attributes');?></a></li>
    <?php endif; ?>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="mainchatmodify">
        <form action="" method="post">

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','E-mail');?></label>
                <input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Recipient e-mail');?>" name="Email" value="<?php echo htmlspecialchars($chat->email);?>" />
            </div>

            <?php if ($chat->online_user instanceof erLhcoreClassModelChatOnlineUser) : ?>
            <div class="form-group">
                <label><input type="checkbox" name="informReturn" <?php if (isset($chat->online_user->online_attr_system_array['lhc_ir']) && is_array($chat->online_user->online_attr_system_array['lhc_ir']) && in_array(erLhcoreClassUser::instance()->getUserID(),$chat->online_user->online_attr_system_array['lhc_ir'])) :?>checked="checked"<?php endif?> value="on"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Inform me then visitor returns');?></label>
                <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','E-mail is send then visitor starts new browsing session')?></small></p>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?></label>
                <input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?>" name="UserNick" value="<?php echo htmlspecialchars($chat->nick);?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Phone');?></label>
                <input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Phone');?>" name="UserPhone" value="<?php echo htmlspecialchars($chat->phone);?>" />
            </div>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <input type="submit" class="btn btn-secondary" name="UpdateChat" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Update chat');?>" />
        </form>
    </div>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','modifychatcore')) : ?>
    <div role="tabpanel" class="tab-pane" id="mainchatcore">
        <form action="" method="post">
             <label><input type="checkbox" name="unanswered_chat" value="on" <?php echo $chat->unanswered_chat == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Unanswered chat')?></label>

             <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Department')?></label>
                <?php
                $params = array (
                    'input_name'     => 'DepartmentID',
                    'display_name'   => 'name',
                    'css_class'      => 'form-control',
                    'selected_id'    => $chat->dep_id,
                    'list_function'  => 'erLhcoreClassModelDepartament::getList',
                    'list_function_params'  => array_merge(array('limit' => '1000000'))
                );
                echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
             </div>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

             <input type="submit" class="btn btn-secondary" name="UpdateChatCore" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Update chat');?>" />
        </form>
    </div>
    <?php endif; ?>
</div>

