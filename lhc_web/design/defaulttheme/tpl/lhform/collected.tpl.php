<h1><?php echo htmlspecialchars($form)?></h1>

<div class="row">
	<div class="col-xs-6"><a href="<?php echo erLhcoreClassDesign::baseurl('form/downloadcollected')?>/<?php echo $form->id?>" class="btn btn-default"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download XLS');?></a></div>
	<div class="col-xs-6">
	
	<div class="input-group">
      <div class="input-group-addon"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','URL');?></div>
      <input type="text" class="form-control" value="<?php echo erLhcoreClassXMP::getBaseHost(). $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('form/fill')?>/<?php echo $form->id?>">
    </div>
    
	</div>
</div>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Identifier');?></th>
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
        <td><?php echo $form->intro_attr != '' ? htmlspecialchars($item->getAttrValue($form->intro_attr)) : ''?></td>
        <td><?php echo $item->ctime_front?></td>
        <td><?php echo htmlspecialchars($item->ip)?></td>
        <td nowrap>
	        <div style="width:140px">
	        
	        	<div class="btn-group" role="group" aria-label="...">
	            	<a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/viewcollected')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','View');?></a>
	            	<a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download');?></a>
				</div>
				
	        </div>
        </td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/collected')?>/<?php echo $form->id?>/(action)/delete/(id)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>