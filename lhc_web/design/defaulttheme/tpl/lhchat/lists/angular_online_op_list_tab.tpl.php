<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list_tab_pre.tpl.php')); ?>
<?php if ($chat_lists_online_operators_enabled == true) : ?>
<li role="presentation" class="nav-item"><a class="nav-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Online operators');?>" href="#onlineoperators" aria-controls="onlineoperators" role="tab" data-bs-toggle="tab"><i class="material-icons chat-operators me-0">account_box</i><span><lhc-chats-counter type="online_op"></lhc-chats-counter></a>
</li>
<?php endif;?>