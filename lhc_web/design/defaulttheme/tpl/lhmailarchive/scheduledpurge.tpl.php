<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Scheduled archive and deletion');?></h1>

<ul class="nav nav-tabs mb-3" role="tablist" data-remember="true">
    <li role="presentation" class="nav-item"><a href="#standard" class="nav-link active" aria-controls="standard" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Standard');?></a></li>
    <?php include(erLhcoreClassDesign::designtpl('lhmailarchive/scheduledpurge_tab_multiinclude.tpl.php'));?>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="standard">
        <table ng-non-bindable class="table table-sm" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th width="1%">ID</th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','User ID');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archive ID');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Status');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Created At');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Updated At');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Started At');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Finished At');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Filter');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Pending records to process');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Last ID');?></th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <?php include(erLhcoreClassDesign::designtpl('lhmailarchive/scheduledpurge_table_content.tpl.php'));?>
        </table>
    </div>
    <?php include(erLhcoreClassDesign::designtpl('lhmailarchive/scheduledpurge_tab_content_multiinclude.tpl.php'));?>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
