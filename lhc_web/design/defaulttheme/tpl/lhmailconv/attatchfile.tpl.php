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

    <table class="table table-sm table-fixed" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th style="width: 120px">&nbsp;</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload name');?></th>
        <th style="width: 120px"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File size');?></th>
        <th style="width: 120px">&nbsp;</th>
        <?php if ($input->attachment != 1) : ?>
        <th style="width: 120px">&nbsp;</th>
        <?php endif; ?>
    </tr>
    </thead>
    <?php foreach ($items as $file) : ?>
        <tr>
            <td>
                <?php if ($file->type == 'image/jpeg' ||  $file->type == 'image/png' || $file->type == 'image/gif') : ?>
                    <img style="max-width: 100px;max-height: 100px" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" alt="" />
                <?php else : ?>
                <?php echo $file->extension;?>
                <?php endif; ?>
            </td>
            <td>
                <div class="abbr-list">
                    <a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" class="link" target="_blank"><?php echo htmlspecialchars($file->upload_name)?></a>
                </div>
            </td>
            <td nowrap><?php echo htmlspecialchars(round($file->size/1024,2))?> Kb.</td>

            <td nowrap>
                <?php if ($input->attachment != 1) : ?>
                    <a onclick="insertContent(<?php echo $file->id?>)" href="#" class="csfr-required btn btn-secondary btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Insert as content');?></a>
                <?php else : ?>
                    <a onclick="insertAttachment(<?php echo $file->id?>)" href="#" class="csfr-required btn btn-secondary btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Attach');?></a>
                <?php endif; ?>
            </td>

            <?php if ($input->attachment != 1) : ?>
            <td nowrap><a onclick="insertLink(<?php echo $file->id?>)" href="#" class="csfr-required btn btn-secondary btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Insert as link');?></a></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

    <?php if (!isset($ajax_search)) : ?>
    <script>

        function insertAttachment(fileId) {
            $.getJSON("<?php echo erLhcoreClassDesign::baseurl('mailconv/attatchfiledata')?>/"+fileId, function (data) {
                window.parent.attatchReplyCurrent(data);
            });
        }

        function insertContent(fileId) {
            $.getJSON("<?php echo erLhcoreClassDesign::baseurl('mailconv/insertfile')?>/"+fileId+"/(mode)/direct",function (data) {
                window.parent.postMessage({
                    mceAction: 'insertContent',
                    content: data.result
                }, '*');
                window.parent.postMessage({ mceAction: 'close' });
            });
        }

        function insertLink(fileId) {
            $.getJSON("<?php echo erLhcoreClassDesign::baseurl('mailconv/insertfile')?>/"+fileId+"/(mode)/link",function (data) {
                window.parent.postMessage({
                    mceAction: 'insertContent',
                    content: data.result
                }, '*');
                window.parent.postMessage({ mceAction: 'close' });
            });
        }

    </script>
</div>
<?php endif; ?>
