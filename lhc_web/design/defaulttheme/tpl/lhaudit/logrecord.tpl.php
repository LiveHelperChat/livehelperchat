<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Log preview') . ' - ' . $object->id;
$modalSize = 'xl';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div class="m-2 mx550">

    <?php if ($object->category == 'rest_api' &&  ($debugData = json_decode($object->message,true)) && isset($debugData['params_request'])) : ?>
        <button class="btn btn-xs btn-outline-secondary" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('audit/copycurl')?>/<?php print $object->id?>/audit'});"  type="button">Copy as CURL</button>
    <?php elseif ($debugData = json_decode($object->message,true)) : ?>
        <button class="btn btn-xs btn-outline-secondary" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('audit/copycurl')?>/<?php print $object->id?>/audit?json_view=1'});"  type="button">JSON View</button>
    <?php endif; ?>

    <?php foreach ($object->getFields() as $fieldName => $attr) : ?>
        <?php if (!isset($attr['hide_edit'])) : ?>
            <?php if ($attr['type'] == 'title') : ?>
                <?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?>
            <?php elseif ($attr['type'] == 'checkbox') : ?>
                <div class="form-group">
                    <label><?php echo erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)?> <?php echo $attr['trans'];?><?php echo $attr['required'] == true ? ' *' : ''?></label>
                </div>
            <?php else : ?>
                <?php if (($fieldEdit = erLhcoreClassAbstract::renderInput($fieldName, $attr, $object)) != '' || $attr['trans'] != '') : ?>
                <div class="form-group">
                    <?php if ($attr['trans'] != '') : ?>
                    <label class="fw-bold"><?php echo $attr['trans'];?><?php echo $attr['required'] == true ? ' *' : ''?></label>
                    <?php endif; ?>
                    <?php echo $fieldEdit?>
                    <?php if (isset($attr['trans_sub'])) : ?><p><small><i><?php echo $attr['trans_sub'];?></i></small></p><?php endif; ?>
                </div>
                <?php endif;?>
            <?php endif;?>
        <?php endif;?>
    <?php endforeach;?>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>