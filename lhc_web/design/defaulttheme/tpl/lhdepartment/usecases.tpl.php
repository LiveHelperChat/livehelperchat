<?php
$useCaseTypes = array(
    'auto_responder' => array('icon' => 'auto_awesome', 'badge' => 'bg-success', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Auto responders')),
    'proactive_invitation' => array('icon' => 'chat', 'badge' => 'bg-warning', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Proactive invitation')),
    'canned_msg' => array('icon' => 'message', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Canned messages')),
    'canned_msg_replace' => array('icon' => 'swap_horiz', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Replaceable variable')),
    'chat_variable' => array('icon' => 'data_object', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Chat variable')),
    'chat_priority' => array('icon' => 'label', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Chat priority')),
    'content_chunk' => array('icon' => 'view_agenda', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Content chunk')),
    'subject' => array('icon' => 'folder', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Subject')),
    'msg_protection' => array('icon' => 'shield', 'badge' => 'bg-danger', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Messages content protection')),
    'product' => array('icon' => 'inventory_2', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Product')),
    'mailbox' => array('icon' => 'email', 'badge' => 'bg-primary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Mailbox')),
    'mail_match_rule' => array('icon' => 'filter_alt', 'badge' => 'bg-primary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Mail matching rule')),
    'mail_response_template' => array('icon' => 'quickreply', 'badge' => 'bg-primary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Mail response template')),
    'incoming_webhook' => array('icon' => 'webhook', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Incoming webhooks')),
    'bot_command' => array('icon' => 'terminal', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Bot command')),
    'bot_trigger' => array('icon' => 'action_key', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Bot trigger')),
    'bot_condition' => array('icon' => 'rule', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Bot condition')),
    'chat_start_settings' => array('icon' => 'play_circle', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Chat start settings')),
    'chat_event_track' => array('icon' => 'track_changes', 'badge' => 'bg-secondary', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Chat event tracking')),
    'department_group' => array('icon' => 'group_work', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Department group')),
    'department_limit_group' => array('icon' => 'pending_actions', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Pending chats limit group')),
    'brand' => array('icon' => 'branding_watermark', 'badge' => 'bg-info', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Brand')),
    'transfer_destination' => array('icon' => 'swap_horiz', 'badge' => 'bg-warning', 'label' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Transfers chats to this department')),
);
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Use cases');?><?php if ($department !== null) : ?> - <?php echo htmlspecialchars($department->name);?><?php endif;?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php if ($department !== null) : ?>
                <?php if (!empty($items)) : ?>
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'This department is used in the following places');?>:</p>
                    <?php foreach ($items as $item) : ?>
                        <?php $typeData = isset($useCaseTypes[$item['type']]) ? $useCaseTypes[$item['type']] : array('icon' => 'link', 'badge' => 'bg-secondary', 'label' => $item['type']);?>
                        <div class="lhc-item-list mb-2">
                            <div class="lhc-item-list-title fs13">
                                <i class="material-icons"><?php echo $typeData['icon'];?></i><a class="me-1" href="<?php echo $item['url'];?>" target="_blank"><?php echo htmlspecialchars($item['name'] != '' ? $item['name'] : ($item['item'] !== null ? '#' . $item['item']->id : ''));?></a>
                                <span class="badge <?php echo $typeData['badge'];?>"><?php echo $typeData['label'];?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="alert alert-info">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'This department is not used anywhere yet.');?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="alert alert-danger">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Department not found.');?>
                </div>
            <?php endif; ?>
        </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
