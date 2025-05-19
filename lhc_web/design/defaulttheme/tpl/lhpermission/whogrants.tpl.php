<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Summary');?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php')); ?>
    <table class="table table-sm list-links">
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Group')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Type')?></th>
        </tr>
        <?php foreach (erLhcoreClassModelGroupUser::getList(['filter' => ['user_id' => $user_id]]) as $groupUser) : ?>
            <?php foreach (erLhcoreClassModelGroupRole::getList(['filter' => ['group_id' => $groupUser->group_id]]) as $role) : ?>
                <?php foreach (erLhcoreClassModelRoleFunction::getList(['filter' => ['role_id' => $role->role_id]]) as $ruleFunction) : ?>
                    <?php if (
                        ($ruleFunction->module === $module_check && $ruleFunction->function === $function_check) ||
                        ($ruleFunction->module === $module_check && $ruleFunction->function === '*') ||
                        ($ruleFunction->module === '*' && $ruleFunction->function === '*')
                    ) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo erLhcoreClassDesign::baseurl('user/editgroup') ?>/<?php echo $role->group_id ?>"><?php echo htmlspecialchars(erLhcoreClassModelGroup::fetch($role->group_id)->name) ?></a>
                            </td>
                            <td>
                                <a href="<?php echo erLhcoreClassDesign::baseurl('permission/editrole') ?>/<?php echo $role->role_id ?>"><?php echo htmlspecialchars(erLhcoreClassModelRole::fetch($role->role_id)->name) ?></a>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($ruleFunction->module) ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($ruleFunction->function) ?>
                            </td>
                            <td>
                                <?php if ($ruleFunction->type === 0) : ?>
                                    <span class="material-icons text-success">verified_user</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Grant');?>
                                <?php else : ?>
                                    <span class="material-icons text-danger">remove_moderator</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Exclude');?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php')); ?>