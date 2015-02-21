<table class="table">
<thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/getpermissionsummary','Module/Function');?></th>
        <th></th>
    </tr>
</thead>
<?php foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
    
    <?php $moduleFunctions = erLhcoreClassModules::getModuleFunctions($key); ?>
    
    <?php if (count($moduleFunctions) > 0) : ?>
    <tr>
        <td colspan="2"><strong>[<?php echo $key?>] <?php echo htmlspecialchars($Module['name']);?></strong></td>
    </tr>
    <?php else : // There is no custom functions that means user can use all module functions ?>
    <tr>
        <td><strong>[<?php echo $key?>] <?php echo htmlspecialchars($Module['name']);?></strong></td>
        <td><label class="label label-success">Y</label></td>
    </tr>
    <?php endif;?>
    
    <?php foreach ($moduleFunctions as $keyFunction => $function) : $canUse = erLhcoreClassRole::canUseByModuleAndFunction($permissions,$key,$keyFunction)?>
    <tr>
        <td>[<?php echo $keyFunction?>] <?php echo htmlspecialchars($function['explain'])?></td>
        <td><label class="label label-<?php if ($canUse == true) : ?>success<?php else : ?>danger<?php endif;?>"><?php if ($canUse == true) : ?>Y<?php else : ?>N<?php endif;?></label></td>
    </tr>
    <?php endforeach;?> 
       
<?php endforeach; ?>
</table>