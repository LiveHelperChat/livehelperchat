<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Select a canned message')?></option>

<?php 

foreach ($canned_options as $depId => $group) :

$dataList = explode('_', $depId);

$typeTitle = '';

if ($dataList[0] == 0) {
    $typeTitle = htmlspecialchars(erLhcoreClassModelDepartament::fetch($dataList[1]));
} elseif ($dataList[0] == 1) {
    $typeTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Personal');
} else {
    $typeTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Global');
} ?>

<optgroup label="<?php echo $typeTitle ?>">
<?php foreach ($group as $item) : ?>
    <option data-msg="<?php echo htmlspecialchars($item->msg_to_user)?>" data-delay="<?php echo $item->delay?>" value="<?php echo $item->id?>"><?php echo htmlspecialchars($item->message_title)?></option>
<?php endforeach; ?>
</optgroup>
    
<?php endforeach; ?>