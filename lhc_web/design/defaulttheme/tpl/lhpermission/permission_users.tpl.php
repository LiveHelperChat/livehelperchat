
<div class="row mb-2" ng-non-bindable>
    <div class="col-6">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Groups');?></h5>
        <?php foreach ($groups as $group) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" target="_blank" class="badge bg-secondary me-1"><?php echo htmlspecialchars($group)?></a>
        <?php endforeach; ?>
    </div>
    <div class="col-6">
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Roles');?></h5>
        <?php foreach ($roles as $role) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('permission/editrole')?>/<?php echo $role->id?>" target="_blank" class="badge bg-secondary me-1"><?php echo htmlspecialchars($role)?></a>
        <?php endforeach; ?>
    </div>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Users');?></h5>

<?php if (isset($users) && !empty($users)) : ?>
<table class="table table-sm table-hover" ng-non-bindable>
    <thead>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','User');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','Groups');?></th>
    </thead>
    <?php foreach ($users as $user) : ?>
        <tr>
            <td>
                <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id?>"><?php echo $user->name_official;?></a>
            </td>
            <td>
                <?php foreach (erLhcoreClassModelGroup::getList(['filterin' => ['id' => $user->user_groups_id]]) as $group) : ?>
                    <?php if (key_exists($group->id,$groups)) : ?>
                        <a href="<?php echo erLhcoreClassDesign::baseurl('user/editgroup')?>/<?php echo $group->id?>" target="_blank" class="badge bg-secondary me-1"><?php echo htmlspecialchars($group)?></a>
                    <?php endif; ?>
                <?php endforeach ; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php else : ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/explorer','No users were found!');?></p>

<?php endif; ?>
