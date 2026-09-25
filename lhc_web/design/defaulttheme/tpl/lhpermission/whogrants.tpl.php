<?php 
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Summary');
$modalSize = 'xl';

// Is there an explicit grant for the checked module function
$explicitGranted = isset($permissions[$module_check][$function_check]);
// Is there an exclude for the checked module function
$excluded = isset($permissions['ex_perm'][$module_check][$function_check]);
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php')); ?>
    <table class="table table-sm list-links">
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Group')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Module')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Function')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Limitation')?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Type')?></th>
        </tr>
        <?php foreach (erLhcoreClassModelGroupUser::getList(['filter' => ['user_id' => $user_id]]) as $groupUser) : ?>
            <?php foreach (erLhcoreClassModelGroupRole::getList(['filter' => ['group_id' => $groupUser->group_id]]) as $role) : ?>
                <?php foreach (erLhcoreClassModelRoleFunction::getList(['filter' => ['role_id' => $role->role_id]]) as $ruleFunction) : ?>
                    <?php if (
                        ($ruleFunction->module === $module_check && $ruleFunction->function === $function_check) ||
                        ($ruleFunction->module === $module_check && $ruleFunction->function === '*') ||
                        ($ruleFunction->module === '*' && $ruleFunction->function === '*')
                    ) :
                        // Determine whatever this rule actually affects the effective permissions
                        if ($ruleFunction->type == 0) { // Grant rule
                            if ($ruleFunction->module === $module_check && $ruleFunction->function === $function_check) {
                                // Explicit grant always takes precedence
                                $isActive = true;
                            } else {
                                // Granted through module,* or *,* - has no effect when explicitly excluded
                                $isActive = $excluded == false;
                            }
                        } else { // Exclude rule
                            if ($ruleFunction->module === $module_check && $ruleFunction->function === $function_check) {
                                // Exclude is active unless an explicit grant overrides it
                                $isActive = $explicitGranted == false;
                            } else {
                                // Excluding through module,* or *,* has no effect
                                $isActive = false;
                            }
                        }
                    ?>
                        <tr class="<?php echo $isActive == true ? '' : 'text-decoration-line-through text-muted' ?>">
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
                                <?php echo htmlspecialchars($ruleFunction->limitation) ?>
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