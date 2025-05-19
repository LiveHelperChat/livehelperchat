<br/>
<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
  <?php foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
  <?php $moduleFunctions = erLhcoreClassModules::getModuleFunctions($key); ?>
  <div class="accordion-item">
    <h2 class="accordion-header" role="tab" id="heading<?php echo $key?>One">
        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-parent="#accordion" href="#<?php echo $key?>One" aria-expanded="true" aria-controls="collapseOne">
            <?php if (count($moduleFunctions) > 0) : $hasFunctions = true;?>
            <?php include(erLhcoreClassDesign::designtpl('lhpermission/gerpermissionsummary_module.tpl.php'));?>                      
            <?php else : $hasFunctions = false; // There is no custom functions that means user can use all module functions ?>                       
            <?php include(erLhcoreClassDesign::designtpl('lhpermission/gerpermissionsummary_module.tpl.php'));?>            
            <label class="float-end label label-success">Y</label>            
            <?php endif;?>
        </button>
    </h2>
    <?php if ($hasFunctions == true) : ?>
    <div id="<?php echo $key?>One" class="accordion-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $key?>One">
      <div class="accordion-body">
        <ul class="list-unstyled mb-0">
        <?php foreach ($moduleFunctions as $keyFunction => $function) : $canUse = erLhcoreClassRole::canUseByModuleAndFunction($permissions,$key,$keyFunction)?>
            <li>
                <label><input class="<?php if ($canUse == false) : ?>PermissionSelector<?php endif;?>" <?php if ($canUse == true) : ?>disabled="disabled" checked="checked"<?php endif;?> type="checkbox" name="RequestPermission" value="<?php echo $key?>_f_<?php echo $keyFunction?>"> [<?php echo $keyFunction?>] <?php echo htmlspecialchars($function['explain'])?></label> <label class="badge bg-<?php if ($canUse == true) : ?>success<?php else : ?>danger<?php endif;?>"><?php if ($canUse == true) : ?>Y<?php else : ?>N<?php endif;?></label>
                <?php if ($detail_permissions === true) : ?><button type="button" data-modul="<?php echo htmlspecialchars($key)?>" data-func="<?php echo htmlspecialchars($keyFunction)?>" class="perm-btn-info btn btn-link btn-xs"><span class="material-icons me-0">info</span></button><?php endif; ?>
            </li>
        <?php endforeach;?>
        </ul>
      </div>
    </div>
    <?php endif;?>    
  </div>
  <?php endforeach; ?>
</div>
<script>

    $('.perm-btn-info').click(function(){
        lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('permission/whogrants')?>/<?php echo $user->id?>/'+$(this).attr('data-modul') + '/' + $(this).attr('data-func')});
    });

    function getSelectedPermissions(){
	var checked = [];
	$('.PermissionSelector:checked').each(function(key){
		checked.push($(this).val());
	});
	if (checked.length == 0) {
		alert(<?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('permission/getpermissionssummary','Please choose at least one permission'),ENT_QUOTES))?>);
	} else {
		lhc.revealModal({'iframe':true,'height':400,'url':'<?php echo erLhcoreClassDesign::baseurl('permission/request')?>/'+checked.join(',')})
	}}</script>
<input type="button" class="btn btn-secondary" name="requestPermission" onclick="return getSelectedPermissions();" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/getpermissionssummary','Request permission')?>">