<br/>
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <?php foreach (erLhcoreClassModules::getModuleList() as $key => $Module) : ?>
  <?php $moduleFunctions = erLhcoreClassModules::getModuleFunctions($key); ?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="heading<?php echo $key?>One">     
        <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $key?>One" aria-expanded="true" aria-controls="collapseOne">
            <?php if (count($moduleFunctions) > 0) : $hasFunctions = true;?>
            <?php include(erLhcoreClassDesign::designtpl('lhpermission/gerpermissionsummary_module.tpl.php'));?>                      
            <?php else : $hasFunctions = false; // There is no custom functions that means user can use all module functions ?>                       
            <?php include(erLhcoreClassDesign::designtpl('lhpermission/gerpermissionsummary_module.tpl.php'));?>            
            <label class="pull-right label label-success">Y</label>            
            <?php endif;?>
        </a>
    </div>    
    <?php if ($hasFunctions == true) : ?>
    <div id="<?php echo $key?>One" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $key?>One">
      <div class="panel-body">
        <ul class="list-unstyled">
        <?php foreach ($moduleFunctions as $keyFunction => $function) : $canUse = erLhcoreClassRole::canUseByModuleAndFunction($permissions,$key,$keyFunction)?>
            <li><label><input class="<?php if ($canUse == false) : ?>PermissionSelector<?php endif;?>" <?php if ($canUse == true) : ?>disabled="disabled" checked="checked"<?php endif;?> type="checkbox" name="RequestPermission" value="<?php echo $key?>_f_<?php echo $keyFunction?>"> [<?php echo $keyFunction?>] <?php echo htmlspecialchars($function['explain'])?></label> <label class="label label-<?php if ($canUse == true) : ?>success<?php else : ?>danger<?php endif;?>"><?php if ($canUse == true) : ?>Y<?php else : ?>N<?php endif;?></label></li>        
        <?php endforeach;?>
        </ul>
      </div>
    </div>
    <?php endif;?>    
  </div>
  <?php endforeach; ?>
</div>
<script>function getSelectedPermissions(){
	var checked = [];
	$('.PermissionSelector:checked').each(function(key){
		checked.push($(this).val());
	});
	if (checked.length == 0) {
		alert(<?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('permission/getpermissionssummary','Please choose at least one permission'),ENT_QUOTES))?>);
	} else {
		lhc.revealModal({'iframe':true,'height':400,'url':'<?php echo erLhcoreClassDesign::baseurl('permission/request')?>/'+checked.join(',')})
	}
}</script>
<input type="button" class="btn btn-default" name="requestPermission" onclick="return getSelectedPermissions();" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/getpermissionssummary','Request permission')?>">
