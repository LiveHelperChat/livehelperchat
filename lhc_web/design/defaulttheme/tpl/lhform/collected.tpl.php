<h1 ng-non-bindable><?php echo htmlspecialchars($form)?></h1>

<div class="row pb-2">
	<div class="col-6"><a href="<?php echo erLhcoreClassDesign::baseurl('form/downloadcollected')?>/<?php echo $form->id?>" class="btn btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download XLS');?></a></div>
	<?php if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_PUBLIC) : ?>
    <div class="col-6">
        <div class="input-group">
        <span class="input-group-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','URL');?></span>
        <input type="text" class="form-control" value="<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurldirect('form/fill')?>/<?php echo $form->id?>">
        </div>
	</div>
    <?php endif; ?>
</div>

<table class="table table-sm" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Name');?></th>
    <?php if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_INTERNAL) : ?>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Operator');?></th>
    <?php else : ?>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Identifier');?></th>
    <?php endif; ?>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Chat');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Intro');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Time');?></th>
    <?php if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_PUBLIC) : ?>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','IP');?></th>
    <?php endif; ?>
    <th width="1%">&nbsp;</th>
    <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhform', 'delete_collected')) : ?>
    <th width="1%">&nbsp;</th>
    <?php endif; ?>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
    	<td><?php echo htmlspecialchars($item->getAttrValue($form->name_attr))?></td>
    	<?php if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_INTERNAL) : ?>
        	<td><?php echo $item->user instanceof erLhcoreClassModelUser ? htmlspecialchars($item->user->name_official) : htmlspecialchars((string)$item->user_id)?></td>
        <?php else : ?>
        	<td>
        	<div class="page-url"><span><?php echo htmlspecialchars($item->identifier)?></span></div>
        	</td>
        <?php endif; ?>
        <td>
    	<?php if ($item->chat_id > 0) : ?>
            <a onclick="lhc.previewChat(<?php echo $item->chat_id?>)"><span class="material-icons">info_outline</span><?php echo $item->chat_id;?></a>
        <?php else : ?>
            <i class="material-icons">remove_circle_outline</i>
        <?php endif; ?>
    	</td>
        <td><?php echo $form->intro_attr != '' ? htmlspecialchars($item->getAttrValue($form->intro_attr)) : ''?></td>
        <td><?php echo $item->ctime_front?></td>
        <?php if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_PUBLIC) : ?>
        <td><?php echo htmlspecialchars($item->ip)?></td>
        <?php endif; ?>
        <td nowrap>
	        <div style="width:140px">
	        
	        	<div class="btn-group" role="group" aria-label="...">
	            	<a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/viewcollected')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','View');?></a>
	            	<a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/downloaditem')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Download');?></a>
				</div>
				
	        </div>
        </td>
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhform', 'delete_collected')) : ?>
        <td nowrap><a data-trans="delete_confirm" class="csfr-post csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('form/collected')?>/<?php echo $form->id?>/(action)/delete/(id)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/collected','Delete');?></a></td>
        <?php endif; ?>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>