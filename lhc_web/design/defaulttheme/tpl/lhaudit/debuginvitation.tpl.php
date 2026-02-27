<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Simplified output')?></h5>

<ul>


    <?php if (erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value != 1) : ?>
    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Proactive invitations is')?>&nbsp;<span class="badge bg-danger">disabled</span>&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/listchatconfig')?>#onlinetracking"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','change it.')?></a></li>
    <?php endif; ?>

    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Valid invitations found')?> - <?php echo count($debug_invitation['rows']); ?> [<?php echo implode(',',array_keys($debug_invitation['rows']))?>]</li>

    <?php if (isset($debug_invitation['no_messages'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','No valid messages were found from candidates')?></li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['no_online_ops'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of no online operators')?> - [<?php echo implode(',',array_keys($debug_invitation['no_online_ops']))?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['invitation_was_seen'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because invitation was seen')?> - <span class="badge bg-secondary">[<?php echo implode(',',$debug_invitation['invitation_was_seen'])?>]</span></li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['last_visit_prev'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Last time seen on website ago')?>.</span> - [<?php echo implode(',',array_keys($debug_invitation['last_visit_prev']))?>], <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','conditions')?> - [<?php echo implode(',',$debug_invitation['last_visit_prev_cond'])?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['last_chat_time'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Last time had chat n minutes ago')?>.</span> - [<?php echo implode(',',array_keys($debug_invitation['last_chat_time']))?>], <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','conditions')?> - [<?php echo implode(',',$debug_invitation['last_chat_time_cond'])?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['conditions_not_valid'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Attributes conditions not valid')?></span> - [<?php echo implode(',',array_keys($debug_invitation['conditions_not_valid']))?>]</li>
    <?php endif; ?>

    <?php if (isset($debug_invitation['message_selected'])) : ?>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Message selected')?> - <?php echo $debug_invitation['message_selected']->id?>
            <ul>
                <?php if (isset($debug_invitation['conditions_unsatisfied'])) : ?>
                    <li><span class="badge bg-danger">conditions_unsatisfied</span></li>
                <?php endif; ?>

                <?php if (isset($debug_invitation['message_approved'])) : ?>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Attributes online visitor')?> - <?php echo json_encode($debug_invitation['message_approved'])?></li>
                <?php endif; ?>

                <?php if (isset($debug_invitation['time_on_site_missmatch'])) : ?>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Skipped because of')?> <span class="badge bg-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Time on site')?></span> - [<?php echo implode(',',array_keys($debug_invitation['time_on_site_missmatch']))?>], <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','conditions')?> - [<?php echo implode(',',$debug_invitation['time_on_site_missmatch_cond'])?>]</li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
</ul>

<div class="mx550">
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Online user output')?></h5>
    <pre class="fs11">
    <?php echo json_encode($online_user,JSON_PRETTY_PRINT); ?>
    </pre>
    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Raw output debug')?></h5>
    <pre class="fs11">
    <?php echo json_encode($debug_invitation,JSON_PRETTY_PRINT); ?>
    </pre>
</div>