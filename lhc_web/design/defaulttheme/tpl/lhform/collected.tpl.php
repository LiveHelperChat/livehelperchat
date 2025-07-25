<h1 ng-non-bindable><?php echo htmlspecialchars($form)?></h1>

<div class="row pb-2">
	<div class="col-6"><a href="<?php echo erLhcoreClassDesign::baseurl('form/downloadcollected')?>/<?php echo $form->id?>" class="btn btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download XLS');?></a></div>
	<div class="col-6">
	
	<div class="input-group">
       <span class="input-group-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','URL');?></span>
      <input type="text" class="form-control" value="<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurldirect('form/fill')?>/<?php echo $form->id?>">
    </div>
    
	</div>
</div>

<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Identifier');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Chat');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Intro');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Time');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','IP');?></th>
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
    	<td><?php echo htmlspecialchars($item->getAttrValue($form->name_attr))?></td>
    	<td>
    	<div class="page-url"><span><?php echo htmlspecialchars($item->identifier)?></span></div>
    	</td>
        <td>
    	<?php if ($item->chat_id > 0) : ?>
            <a class="material-icons" onclick="lhc.previewChat(<?php echo $item->chat_id?>)">info_outline</a>
        <?php else : ?>
            <i class="material-icons">remove_circle_outline</i>
        <?php endif; ?>
    	</td>
        <td><?php echo $form->intro_attr != '' ? htmlspecialchars($item->getAttrValue($form->intro_attr)) : ''?></td>
        <td><?php echo $item->ctime_front?></td>
        <td><?php echo htmlspecialchars($item->ip)?></td>
        <td nowrap>
	        <div style="width:140px">
	        
	        	<div class="btn-group" role="group" aria-label="...">
	            	<a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/viewcollected')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','View');?></a>
	            	<a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download');?></a>
				</div>
				
	        </div>
        </td>
        <td nowrap><a data-trans="delete_confirm" class="csfr-post csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/collected')?>/<?php echo $form->id?>/(action)/delete/(id)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>