<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/list','List of forms');?></h1>

<table class="table table-sm" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/list','Form name');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('form/list','Form link');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $form) : ?>

<?php $link = erLhcoreClassXMP::getBaseHost(). $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('form/fill') . '/' .  $form->id; ?>
    <tr>         
        <td nowrap><?php echo $form->name; ?></td>
        <td><input type="text" class="form-control" value="<?php echo $link ?>"></td>
        <td nowrap><a id="embed-button-<?php echo $form->id?>" onclick="return lhinst.sendLinkToEditor('<?php echo $chat->id?>','<?php echo '[url='.$link.']'.$form->name.'[/url]' ?>','<?php echo $form->id?>')" href="#" class="csfr-required btn btn-secondary btn-xs"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Embed Form link');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>