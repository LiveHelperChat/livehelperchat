<?php if ($currentUser->hasAccessTo('lhmailconv','use_admin')) : ?>
<div role="tabpanel" class="tab-pane" id="mailconv">
    <ul class="circle small-list">

        <?php if ($currentUser->hasAccessTo('lhmailconv','mailbox_manage')) : ?>
        <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailbox list')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/mailbox')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailbox');?></a></li>
        <?php endif; ?>

        <?php if ($currentUser->hasAccessTo('lhmailconv','mrules_manage')) : ?>
        <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Matching rules')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/matchingrules')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Matching rules');?></a></li>
        <?php endif; ?>

        <?php if ($currentUser->hasAccessTo('lhmailconv','rtemplates_manage')) : ?>
            <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Response templates')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/responsetemplates')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Response templates');?></a></li>
        <?php endif; ?>

        <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Conversations')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Conversations');?></a></li>
    </ul>
</div>
<?php endif; ?>