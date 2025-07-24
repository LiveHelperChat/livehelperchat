<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Mail file details');?></h1>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','ID');?></label>
            <p><?php echo htmlspecialchars($item->id)?></p>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File name');?></label>
            <p><?php echo htmlspecialchars($item->name)?></p>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File size');?></label>
            <p><?php echo htmlspecialchars(round($item->size/1024,2))?> Kb.</p>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Extension');?></label>
            <p><?php echo htmlspecialchars($item->extension)?></p>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Disposition');?></label>
            <p><?php echo htmlspecialchars($item->disposition)?></p>
        </div>

        <div class="form-group">
            <label>MIME Type</label>
            <p><?php echo htmlspecialchars($item->type)?></p>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Message');?> ID</label>
            <p><?php echo $item->message_id > 0 ? htmlspecialchars($item->message_id) : '-'?></p>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Conversation');?> ID</label>
            <p><?php echo $item->conversation_id > 0 ? htmlspecialchars($item->conversation_id) : '-'?></p>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Attachment');?> ID</label>
            <p><?php echo htmlspecialchars($item->attachment_id)?></p>
        </div>

        <?php if (!empty($item->content_id)) : ?>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Content');?> ID</label>
            <p><?php echo htmlspecialchars($item->content_id)?></p>
        </div>
        <?php endif; ?>

        <?php if ($item->width > 0 || $item->height > 0) : ?>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','Dimensions');?></label>
            <p><?php echo htmlspecialchars($item->width)?> x <?php echo htmlspecialchars($item->height)?></p>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/list','File path');?></label>
            <p class="text-muted fs12"><?php echo htmlspecialchars($item->file_path . $item->file_name)?></p>
        </div>
    </div>
</div>

<?php if ($item->description != '') : ?>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Description</label>
            <p><?php echo htmlspecialchars($item->description)?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($item->meta_msg != '') : ?>
<div class="row">
    <div class="col-12">
        <h6 class="mt-4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('file/new','Meta data');?></h6>
        <pre class="fs11"><?php echo htmlspecialchars(json_encode($item->meta_msg_array,JSON_PRETTY_PRINT))?></pre>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="btn-group" role="group">
            <a href="<?php echo erLhcoreClassDesign::baseurl('file/listmail')?>" class="btn btn-secondary">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Back');?>
            </a>

            <?php
                $conversations_id = 0;
                if ($item->message_id > 0) :
                    $message = erLhcoreClassModelMailconvMessage::fetch($item->message_id);
                    if ($message instanceof erLhcoreClassModelMailconvMessage) :
                        $conversations_id = $message->conversation_id;
                    endif;
                endif;
            ?>
            <?php if ($conversations_id > 0) : ?>
            <a href="<?php echo erLhcoreClassDesign::baseurl('mailconv/inlinedownload')?>/<?php echo $item->id?>/<?php echo $conversations_id?>" target="_blank" class="btn btn-info">
                <span class="material-icons">download</span> Download File
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
