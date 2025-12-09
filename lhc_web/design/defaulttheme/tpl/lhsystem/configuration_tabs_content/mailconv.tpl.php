<?php if ($currentUser->hasAccessTo('lhmailconv','use_admin')) : ?>
<div role="tabpanel" class="tab-pane" id="mailconv">

    <div class="row">

        <div class="col-md-6">
            <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mail options')?></h5>

            <ul class="circle small-list">

                <?php if ($currentUser->hasAccessTo('lhmailconv','mailbox_manage')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailbox list')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/mailbox')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailbox');?></a></li>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Personal mailbox groups')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/personalmailboxgroups')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Personal mailbox groups');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconv','mrules_manage')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Matching rules')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/matchingrules')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Matching rules');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconv','rtemplates_see')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Response templates')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/responsetemplates')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Response templates');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconv','mailbox_manage')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Editor options')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/options')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Editor options');?></a></li>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','General options')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/optionsgeneral')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','General options');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconvoauth','manage_oauth')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','OAuth options')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconvoauth/options')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','OAuth options');?></a></li>
                <?php endif; ?>

                <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Conversations')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Conversations');?></a></li>

                <?php if ($currentUser->hasAccessTo('lhmailarchive','archive')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mail archive')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/archive')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mail archive');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconv','delete_conversation') && $currentUser->hasAccessTo('lhmailarchive','archive')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Scheduled archive and deletion')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailarchive/scheduledpurge')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Scheduled archive and deletion');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconv','mailbox_manage')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Pending imports')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/pendingimport')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Pending imports');?></a></li>
                <?php endif; ?>

            </ul>
        </div>
        <div class="col-md-6">
            <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailing options')?></h5>

            <ul class="circle small-list">

                <?php if ($currentUser->hasAccessTo('lhmailing','mailinglist')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailing list')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailing/mailinglist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Mailing list');?></a></li>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Recipients')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailing/mailingrecipient')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Recipients');?></a></li>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Campaigns')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailing/campaign')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Campaigns');?></a></li>
                <?php endif; ?>

                <?php if ($currentUser->hasAccessTo('lhmailconv','send_mail')) : ?>
                    <li><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Send an e-mail')?>" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/module','Send an e-mail');?></a></li>
                <?php endif; ?>
            </ul>

        </div>



    </div>

</div>
<?php endif; ?>