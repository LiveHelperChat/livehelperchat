<h1><?php echo htmlspecialchars($form)?></h1>

<div class="row">
	<div class="columns large-6"><a href="<?php echo erLhcoreClassDesign::baseurl('form/downloadcollected')?>/<?php echo $form->id?>" class="button small radius"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download XLS');?></a></div>
	<div class="columns large-6">
	
		<div class="row collapse">        
	        <div class="small-2 columns">
	          <span class="prefix"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','URL');?></span>
	        </div>
	        <div class="small-10 columns">
	          <input type="text" value="<?php echo erLhcoreClassXMP::getBaseHost(). $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('form/fill')?>/<?php echo $form->id?>">
	        </div>
	    </div>    	
	
	</div>
</div>

<table class="twelve" cellpadding="0" cellspacing="0">
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
        <td><?php echo htmlspecialchars($item->getAttrValue($form->intro_attr))?></td>
        <td><?php echo $item->ctime_front?></td>
        <td><?php echo htmlspecialchars($item->ip)?></td>
        <td nowrap>
	        <div style="width:140px">
	        	<ul class="button-group round">
	            	<li><a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('form/viewcollected')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','View');?></a></li>
	            	<li><a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download');?></a></li>
				</ul>
	        </div>
        </td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required small alert button round" href="<?php echo erLhcoreClassDesign::baseurl('form/collected')?>/<?php echo $form->id?>/(action)/delete/(id)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Delete');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>