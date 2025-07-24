<h1><?php include(erLhcoreClassDesign::designtpl('lhfile/titles/listmail.tpl.php'));?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhfile/parts/search_panel_mail.tpl.php')); ?>

<table class="table table-sm" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','ID');?></th>
     <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Conversation');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File name');?></th>
   
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Message');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File size');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Extension');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Disposition');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $file) : ?>
    <tr>
        <td><?php echo $file->id?></td>
        <td>
            <?php $conversations_id = 0;if ($file->message_id > 0) : 
                $message = erLhcoreClassModelMailconvMessage::fetch($file->message_id);
                if ($message instanceof erLhcoreClassModelMailconvMessage) : $conversations_id = $message->conversation_id;?>
                   <a id="preview-item-<?php echo $message->conversation_id?>" data-list-navigate="true" onclick="lhc.previewMail(<?php echo $message->conversation_id?>,this);" class="material-icons">info_outline</a> 
                   <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($message->subject)?>" onclick="lhinst.startMailNewWindow(<?php echo $message->conversation_id?>,$(this).attr('data-title'))">open_in_new</a>
                   <?php echo $message->conversation_id; ?> <?php echo erLhcoreClassDesign::shrt($message->subject,50)?>
           <?php endif; endif; ?>
        </td>
        <td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/inlinedownload')?>/<?php echo $file->id?>/<?php echo $conversations_id?>" target="_blank"><span class="material-icons text-muted">attach_file</span> <?php echo htmlspecialchars($file->name)?></a>
            <?php if (!empty($file->meta_msg)) : ?>
                <span class="material-icons text-info" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Has metadata');?>">info</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if ($file->message_id > 0) : ?>
                <?php echo $file->message_id?>
            <?php else : ?>
                -
            <?php endif; ?>
        </td>
        <td nowrap><?php echo htmlspecialchars(round($file->size/1024,2))?> Kb.</td>
        <td nowrap><?php echo htmlspecialchars($file->extension)?></td>
        <td nowrap><?php echo htmlspecialchars($file->disposition)?></td>
        <td nowrap>
            <a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('file/editmail')?>/<?php echo $file->id?>" target="_blank">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Details');?>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
