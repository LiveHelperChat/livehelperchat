<div class="alert fs13 alert-<?php echo (erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value != 1) ? 'warning' : 'success'; ?> mt-4">
    <p>
        <?php if (erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value != 1) : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/chatmessagesghosting','Message content protection rules are disabled for visitors and operators. They still will work for Rest API calls.');?>
        <?php else : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/chatmessagesghosting','Message content protection rules are enabled for visitors and operators. Only one rule per department will be applied if multiple rules match.');?>
        <?php endif; ?>
    </p>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhsystem','singlesetting')) : ?> 
        <button type="button" class="btn btn-secondary btn-xs" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/system/singlesetting/guardrails_enabled'});">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/chatmessagesghosting', 'Change setting');?>
        </button>
    <?php endif; ?>
</div>