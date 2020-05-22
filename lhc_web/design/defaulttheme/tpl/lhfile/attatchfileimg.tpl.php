<?php
    $fileSearchOptions = array(
        'ajax' => true
    );
?>

<?php if (!isset($ajax_search)) : ?>
<div class="form-group">
    <a onclick="lhc.revealModal({'iframe':true,'height':400,'url':'<?php echo erLhcoreClassDesign::baseurl('file/new')?>' + '/(mode)/reloadparent/(persistent)/true'})" href="#" class="btn btn-secondary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload a file');?></a>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhfile/parts/search_panel.tpl.php')); ?>

<div id="file-search-content">
<?php endif; ?>

    <table class="table table-sm table-fixed"" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th style="width: 120px">&nbsp;</th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload name');?></th>
            <th style="width: 120px"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File size');?></th>
            <th style="width: 120px">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $file) : ?>
            <tr>
                <td>
                    <?php if ($file->type == 'image/jpeg' ||  $file->type == 'image/png' || $file->type == 'image/gif') : ?>
                        <img style="max-width: 100px;max-height: 100px" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" alt="" />
                    <?php endif; ?>
                </td>
                <td>
                    <div class="abbr-list">
                    <a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" class="link" target="_blank"><?php echo htmlspecialchars($file->upload_name)?></a>
                    </div>
                </td>
                <td nowrap><?php echo htmlspecialchars(round($file->size/1024,2))?> Kb.</td>
                <td nowrap><a id="embed-button-<?php echo $file->id?>" onclick="lhinst.sendLinkToGeneralEditor('[file=<?php echo $file->id,'_',$file->security_hash,'_img'?>]','<?php echo $file->id?>')" href="#" class="csfr-required btn btn-secondary btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Embed BB code');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>


<?php if (!isset($ajax_search)) : ?>
</div>
<?php endif; ?>
