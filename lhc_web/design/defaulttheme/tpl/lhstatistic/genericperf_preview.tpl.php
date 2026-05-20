<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalSize = 'xl';
$modalBodyClass = 'p-2';

if ($item->type == \LiveHelperChat\Models\Statistic\Performance::OPERATOR) {
    $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Operator performance record');
    $columnTitles = array(
        'name'  => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Name'),
        'ton'   => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Total Online Time'),
        'toff'  => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Total Offline Time'),
        'ca'    => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats answered'),
        'frt'   => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','First response time (Agent)'),
        'aart'  => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Average response time (Agent)'),
        'tup'   => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs Up'),
        'tdown' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs Down'),
    );
} else {
    $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Department performance record');
    $columnTitles = array(
        'name'  => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Name'),
        'cr'    => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats received'),
        'ca'    => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats answered'),
        'wt'    => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Wait time'),
        'frt'   => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','First response time'),
        'aart'  => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Average response time'),
        'tup'   => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs up'),
        'tdown' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs down'),
    );
}

$dataArray = $item->data_array;

if ($item->type == \LiveHelperChat\Models\Statistic\Performance::OPERATOR) {
    foreach ($dataArray as &$row) {
        $user = \erLhcoreClassModelUser::fetch($row['id'], true);
        $row['name'] = $user ? $user->name_official : '';
        if (isset($row['ton']) && $row['ton'] !== '') {
            $row['ton'] = \erLhcoreClassChat::formatSeconds((int)$row['ton']);
        }
        if (isset($row['toff']) && $row['toff'] !== '') {
            $row['toff'] = \erLhcoreClassChat::formatSeconds((int)$row['toff']);
        }
    }
    unset($row);
} else {
    foreach ($dataArray as &$row) {
        $row['name'] = (string)\erLhcoreClassModelDepartament::fetch($row['id'], true);
    }
    unset($row);
}
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<table class="table table-sm table-hover" ng-non-bindable>
    <thead>
        <tr>
            <?php foreach ($columnTitles as $title) : ?>
                <th nowrap><?php echo htmlspecialchars($title); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($dataArray)) : ?>
            <?php foreach ($dataArray as $row) : ?>
                <tr>
                    <?php foreach (array_keys($columnTitles) as $key) : ?>
                        <td><?php echo isset($row[$key]) ? htmlspecialchars((string)$row[$key]) : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="<?php echo count($columnTitles); ?>" class="text-center"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','No data found'); ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>

<p class="text-muted small mb-0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/statistic','Generated at'); ?>: <?php echo $item->created_at > 0 ? date('Y-m-d H:i:s', $item->created_at) : '-'; ?></p>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
