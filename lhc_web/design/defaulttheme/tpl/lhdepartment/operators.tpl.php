<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2 ';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department operators');
$modalBodyClass = 'p-1';
$modalSize = 'xl';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
    <div class="modal-body" ng-non-bindable>
        <div class="p-2">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','We show only directly or group assigned operators.');?></p>
            <table class="table table-sm table-hover">
                <thead>
                <tr>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','User ID');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Last activity ago');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Read only');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Assignment type');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Exclude from auto assign workflow');?></th>
                </tr>
                </thead>
            <?php foreach (erLhcoreClassModelUserDep::getList(['sort' => 'user_id ASC, type ASC','filter' => ['dep_id' => $department->id], 'limit' => false]) as $member) : ?>
                <tr>
                    <td id="<?php echo $member->user_id?>">
                        <a href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $member->user_id?>" target="_blank"><span class="material-icons">open_in_new</span><?php echo htmlspecialchars($member->name_official)?></a>
                    </td>
                    <td>
                        <span class="material-icons"><?php echo $member->hide_online == 0 ? 'flash_on' : 'flash_off';?></span>
                        <?php $agoActivity = time() - $member->last_activity; ?>
                        <?php echo $agoActivity > 0 ? erLhcoreClassChat::formatSeconds($agoActivity) : '0 s.'; ?>
                    </td>
                    <td>
                        <?php if ($member->ro == 1) : ?>
                            <span class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?></span>
                        <?php else : ?>
                            <span class="text-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($member->type == 0) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department');?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Department group');?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars(erLhcoreClassModelDepartamentGroup::fetch($member->dep_group_id));?> [<?php echo $member->dep_group_id?>]
                    </td>
                    <td>
                        <?php if ($member->exclude_autoasign == 1) : ?>
                            <span class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','Yes');?></span>
                        <?php else : ?>
                            <span class="text-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/departmentstats','No');?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>

        </div>
    </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>