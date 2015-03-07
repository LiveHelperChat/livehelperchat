<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','List of files');?></h1>

<div class="form-group">
    <a onclick="lhc.revealModal({'iframe':true,'height':400,'url':'<?php echo erLhcoreClassDesign::baseurl('file/new')?>' + '/(mode)/reloadparent'})" href="#" class="btn btn-default btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload a file');?></a>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhfile/parts/search_panel.tpl.php')); ?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File size');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $file) : ?>
    <tr>         
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" class="link" target="_blank"><?php echo htmlspecialchars($file->upload_name)?></a></td>
        <td nowrap><?php echo htmlspecialchars(round($file->size/1024,2))?> Kb.</td>
        <td nowrap><a id="embed-button-<?php echo $file->id?>" onclick="lhinst.sendLinkToMail('[file=<?php echo $file->id,'_',$file->security_hash?>] <?php echo htmlspecialchars($file->upload_name,ENT_QUOTES)?>','<?php echo $file->id?>')" href="#" class="csfr-required btn btn-default btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Embed BB code');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>