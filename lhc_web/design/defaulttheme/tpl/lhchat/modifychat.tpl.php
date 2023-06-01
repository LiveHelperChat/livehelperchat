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
    <li role="presentation" class="nav-item"><a class="active nav-link" href="#mainchatmodify" aria-controls="mainchatmodify" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','User attribute');?></a></li>
    
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','modifychatcore')) : ?>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#mainchatcore" aria-controls="mainchatcore" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Chat attributes');?></a></li>
    <?php endif; ?>
    
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','chatdebug')) : ?>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#chatdebug" aria-controls="chatdebug" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Debug');?></a></li>
    <?php endif; ?>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="mainchatmodify">
        <form action="" method="post" onsubmit="$('#main-update-btn').attr('disabled','disabled').prepend('<span class=\'lhc-spin material-icons\'>refresh</span>')">

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','E-mail');?></label>
                <input class="form-control form-control-sm" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Recipient e-mail');?>" name="Email" value="<?php echo htmlspecialchars($chat->email);?>" />
            </div>

            <?php if ($chat->online_user instanceof erLhcoreClassModelChatOnlineUser) : ?>
            <div class="form-group">
                <label><input type="checkbox" name="informReturn" <?php if (isset($chat->online_user->online_attr_system_array['lhc_ir']) && is_array($chat->online_user->online_attr_system_array['lhc_ir']) && in_array(erLhcoreClassUser::instance()->getUserID(),$chat->online_user->online_attr_system_array['lhc_ir'])) :?>checked="checked"<?php endif?> value="on">&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Inform me then visitor returns');?></label>
                <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','E-mail is send when visitor starts new browsing session')?></small></p>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?></label>
                <input class="form-control form-control-sm" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Nick');?>" name="UserNick" value="<?php echo htmlspecialchars($chat->nick);?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Phone');?></label>
                <input class="form-control form-control-sm" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Phone');?>" name="UserPhone" value="<?php echo htmlspecialchars($chat->phone);?>" />
            </div>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

            <input type="hidden" name="UpdateChat" value="on" />

            <button type="submit" id="main-update-btn" class="btn btn-sm btn-secondary" name="UpdateChat"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Update chat');?></button>
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
                    'css_class'      => 'form-control form-control-sm',
                    'selected_id'    => $chat->dep_id,
                    'list_function'  => 'erLhcoreClassModelDepartament::getList',
                    'list_function_params'  => array_merge(array('limit' => '1000000'))
                );
                echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
             </div>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

             <input type="submit" class="btn btn-sm btn-secondary" name="UpdateChatCore" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Update chat');?>" />
        </form>
    </div>
    <?php endif; ?>
    
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','chatdebug')) : ?>
        <div role="tabpanel" class="tab-pane" id="chatdebug">


            <table class="table table-sm">
                <tr>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Operator');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Duration');?></th>
                </tr>
            <?php foreach (\LiveHelperChat\Models\LHCAbstract\ChatParticipant::getList(['filter' => ['chat_id' => $chat->id]]) as $participiant) : ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($participiant->n_official)?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($participiant->duration_front)?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>

            <pre class="fs11"><?php echo htmlspecialchars(json_encode($chat->getState(),JSON_PRETTY_PRINT)); ?></pre>

            <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Duration calculation log');?></h6>
            <?php $logDuration = []; \LiveHelperChat\Helpers\ChatDuration::getChatDurationToUpdateChatID($chat,false,$logDuration);?>
            <pre class="fs11"><?php print_r($logDuration);?></pre>

        </div>
    <?php endif; ?>
    
</div>

