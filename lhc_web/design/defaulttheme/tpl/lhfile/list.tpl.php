<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/list.tpl.php'));?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhfile/parts/search_panel.tpl.php')); ?>

<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','User');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Chat');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File size');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Extension');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Date');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $file) : ?>
    <tr>
        <td><?php echo $file->id?></td>
        <td><?php echo htmlspecialchars($file->user)?></td>
        <td>
        <?php if ($file->chat !== false) : ?>
        <?php echo $file->chat->id;?>. <?php echo htmlspecialchars($file->chat->nick);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$file->chat->time);?>) (<?php echo htmlspecialchars($file->chat->department);?>)
        <?php else : ?>
        -
        <?php endif;?>
        </td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $file->id?>/<?php echo $file->security_hash?>" class="link" target="_blank"><?php echo htmlspecialchars($file->upload_name)?></a></td>
        <td nowrap><?php echo htmlspecialchars(round($file->size/1024,2))?> Kb.</td>
        <td nowrap><?php echo htmlspecialchars($file->extension)?></td>
        <td nowrap><?php echo htmlspecialchars($file->date_front)?></td>
        <td nowrap>
        <a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('file/delete')?>/<?php echo $file->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Delete the file');?></a>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<a href="<?php echo erLhcoreClassDesign::baseurl('file/new')?>" class="btn btn-default"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Upload a file');?></a>
